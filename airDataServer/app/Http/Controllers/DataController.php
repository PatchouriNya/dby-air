<?php

namespace App\Http\Controllers;

use App\Models\Air_detail;
use App\Models\Client;
use Illuminate\Http\Request;
class DataController extends Controller
{
    private array $windSpeed = ['50' =>'高风','40' => 'h','30' => '中风','20' => 'l','10' => '低风','a0' => '自动'];
    private array $windSpeed_write = ['高风' =>'5000','中风' => '3000','低风' => '1000','自动' => 'a000'];


    private array $mode = ['4200' => '送风','4201' => '制热','4202' => '制冷','4207' => '除湿'];
    private array $mode_write = ['送风' =>'0000','制热' => '0001','制冷' => '0002','除湿' => '0007','自动' => '0003'];

    private array $power = ['00' => '关机','01' => '开机','42' => '未知'];
    private array $power_write = ['关机' =>'0060','开机' => '0061'];

    private array $guzhangma = ['0000' => '在线','0001' => '异常'];

    private array $chuanganqi = ['0000' => '正常'];
    private function sendCommand($str,$com,$mode): \Illuminate\Http\JsonResponse
    {
        // $mode为读写模式 1为读 2为写
        $s = pack('H*',$str);
        $t = $this->crc16($s);
        $command = strtoupper(unpack("H*", $s.$t)[1]);
        // 定义com口 baud 波特率 data 数据位 stop 停止位 由供应商提供
        $baud = '9600';
        $data = '8';
        $stop = '1';

        set_time_limit(0);

        exec('mode ' . $com . ': baud=' . $baud . ' data=' . $data . ' stop=' . $stop . ' parity=n xon=on', $output);
        // 打开串口 O_RDWR读写模式 O_RDONLY只读
        $ck = dio_open($com . ':', O_RDWR);

        // 如果打开串口失败，停止脚本，并输出“打开串口COM2失败”
        if (!$ck) {
            return response()->json(['error' => '110', "message" => "打开串口" . $com . "失败"]);
        }

        // 发送命令
        $command = hex2bin($command);
        dio_write($ck, $command);

        // 读取串口数据
        $len = 1024; // 设置读取长度
        $shuju = '';

        // 等待数据响应
        usleep(100000); // 100ms 延迟

        // 读取数据
        do {
            $data = dio_read($ck,$len);
            if ($data) {
                $shuju .= $data;
            }
        } while ($data == null);

        // 关闭串口
        dio_close($ck);

        $shuju = bin2hex($shuju);

        if ($mode === 1){
            // 去掉前6位和最后4位
            $shuju = substr($shuju, 6, -4);

            // 使用 str_split 按每24个字符进行分割
            $shujuArray = str_split($shuju, 24);

            $translatedArray = $this->translateData($shujuArray);
            return response()->json(['received' =>$translatedArray]);

        }else{
            return response()->json(['received' => $shuju]);
        }

        // 返回接收到的数据
    }

    public function getLatestAirData($id)
    {
        $client = Client::where('id',$id)->first(['host_address','com_port','start_address','total_air'])->toArray();
        if ($client['host_address'] === null || $client['com_port'] === null || $client['start_address'] === null || $client['total_air'] === null)
            return api(null,400,'该客户还没有接通真实数据哟~');
        $numAircons = $client['total_air'];
        $results = [];
        $maxPerRequest = 20;
        $registersPerAircon = 6;
        $baseRegister = hexdec($client['start_address']);
        $index = 1; // 初始化编号

        for ($i = 0; $i < ceil($numAircons / $maxPerRequest); $i++) {
            // 计算读取时的起始地址
            $startRegister = $baseRegister + ($i * $maxPerRequest * $registersPerAircon);
            // 计算写入时的起始地址
            $writeStartRegister = $baseRegister + ($i * $maxPerRequest * 4);

            $count = min($maxPerRequest, $numAircons - ($i * $maxPerRequest));

            // 构造命令
            $command = sprintf($client['host_address'].'04%04X%04X', $startRegister, $count * $registersPerAircon);
            $receivedData = $this->sendCommand($command, $client['com_port'],1);

            // 将 JSON 响应转换为数组
            $receivedDataArray = $receivedData->getData(true);


            if (isset($receivedDataArray['received'])) {
                foreach ($receivedDataArray['received'] as &$data) {
                    $data['id'] = $index++; // 为每台空调添加编号
                    // 读取数据时的起始地址
                    $data['startAddress'] = dechex($startRegister + (($data['id'] - 1) % $maxPerRequest) * $registersPerAircon);
                    // 将十六进制数转换为固定长度的字符串（四位），如果不够四位则在前面填充零
                    $data['startAddress'] = str_pad($data['startAddress'], 4, '0', STR_PAD_LEFT);

                    // 写入数据时的起始地址
                    $data['writeAddress'] = dechex($writeStartRegister + (($data['id'] - 1) % $maxPerRequest) * 4);
                    $data['writeAddress'] = str_pad($data['writeAddress'], 4, '0', STR_PAD_LEFT);
                    // 将 id 和 startAddress 放在数组的第一个位置
                    $data = ['id' => $data['id'], 'startAddress' => $data['startAddress'], 'writeAddress' => $data['writeAddress']] + $data;
                }
                $results = array_merge($results, $receivedDataArray['received']);
            } else {
                return api(null,500,'获取空调数据失败');
            }
        }
        foreach ($results as $item) {
            if($item['error_code'] === '在线' && ((int)$item['set_temperature'] >=16 && (int)$item['set_temperature'] <= 32)){
               Air_detail::where('client_id',$id)->where('show_id' ,$item['id'])->update([
                   'read_base_address' => $item['startAddress'],
                   'write_base_address' => $item['writeAddress'],
                   'wind_speed' => $item['wind_speed'],
                   'power_state' => $item['power_state'],
                   'operation_mode' => $item['operation_mode'],
                   'set_temperature' => $item['set_temperature'],
                   'room_temperature' => $item['room_temperature'],
                   'online_state' => '在线'
               ]);
            }else{
                Air_detail::where('client_id',$id)->where('show_id' ,$item['id'])->update([
                    'online_state' => '离线',
                    'read_base_address' => $item['startAddress'],
                    'write_base_address' => $item['writeAddress'],
                    // 测试用
                    'wind_speed' => $item['wind_speed'],
                    'power_state' => $item['power_state'],
                    'operation_mode' => $item['operation_mode'],
                    'set_temperature' => '0℃',
                    'room_temperature' => '0℃',

                ]);
            }
        }
        return api($results,200,'获取最新数据成功');
    }

    public function controlOneAir($id,Request $request){
        $client = Client::where('id',$id)->first(['host_address','com_port','start_address'])->toArray();
        if ($client['host_address'] === null || $client['com_port'] === null || $client['start_address'] === null)
            return api(null,400,'该客户还没有接通真实数据哟~');
        $air_id = $request->input('air_id');
        $air = Air_detail::where('client_id',$id)->where('show_id',$air_id)->first('write_base_address');
        $wind_speed = $request->input('wind_speed');
        $power_state = $request->input('power_state');
        $operation_mode = $request->input('operation_mode');
        // 字符串转成数字之后扩大十倍再转成16进制之后再前面补0够4位
        $set_temperature = str_pad(dechex((int)$request->input('set_temperature') *10), 4, '0', STR_PAD_LEFT);
        $command = $client['host_address'].'10'.$air->write_base_address.'0004'.'08'.$this->windSpeed_write[$wind_speed].$this->power_write[$power_state].$this->mode_write[$operation_mode].$set_temperature;
        $res = $this->sendCommand($command,$client['com_port'],2);
        $receivedDataArray = $res->getData(true);
        Air_detail::where('client_id',$id)->where('show_id',$air_id)->update([
            'wind_speed' => $wind_speed,
            'power_state' => $power_state,
            'operation_mode' => $operation_mode,
           'set_temperature' => $request->input('set_temperature').'℃',
        ]);
        return api($receivedDataArray['received'],201,'控制空调成功');
        // 数据校验没写
    }


    private function crc16($string, $length = 0): string
    {
        $auchCRCHi = array(
            0x00, 0xC1, 0x81, 0x40, 0x01, 0xC0, 0x80, 0x41, 0x01, 0xC0, 0x80, 0x41, 0x00, 0xC1, 0x81,
            0x40, 0x01, 0xC0, 0x80, 0x41, 0x00, 0xC1, 0x81, 0x40, 0x00, 0xC1, 0x81, 0x40, 0x01, 0xC0,
            0x80, 0x41, 0x01, 0xC0, 0x80, 0x41, 0x00, 0xC1, 0x81, 0x40, 0x00, 0xC1, 0x81, 0x40, 0x01,
            0xC0, 0x80, 0x41, 0x00, 0xC1, 0x81, 0x40, 0x01, 0xC0, 0x80, 0x41, 0x01, 0xC0, 0x80, 0x41,
            0x00, 0xC1, 0x81, 0x40, 0x01, 0xC0, 0x80, 0x41, 0x00, 0xC1, 0x81, 0x40, 0x00, 0xC1, 0x81,
            0x40, 0x01, 0xC0, 0x80, 0x41, 0x00, 0xC1, 0x81, 0x40, 0x01, 0xC0, 0x80, 0x41, 0x01, 0xC0,
            0x80, 0x41, 0x00, 0xC1, 0x81, 0x40, 0x00, 0xC1, 0x81, 0x40, 0x01, 0xC0, 0x80, 0x41, 0x01,
            0xC0, 0x80, 0x41, 0x00, 0xC1, 0x81, 0x40, 0x01, 0xC0, 0x80, 0x41, 0x00, 0xC1, 0x81, 0x40,
            0x00, 0xC1, 0x81, 0x40, 0x01, 0xC0, 0x80, 0x41, 0x01, 0xC0, 0x80, 0x41, 0x00, 0xC1, 0x81,
            0x40, 0x00, 0xC1, 0x81, 0x40, 0x01, 0xC0, 0x80, 0x41, 0x00, 0xC1, 0x81, 0x40, 0x01, 0xC0,
            0x80, 0x41, 0x01, 0xC0, 0x80, 0x41, 0x00, 0xC1, 0x81, 0x40, 0x00, 0xC1, 0x81, 0x40, 0x01,
            0xC0, 0x80, 0x41, 0x01, 0xC0, 0x80, 0x41, 0x00, 0xC1, 0x81, 0x40, 0x01, 0xC0, 0x80, 0x41,
            0x00, 0xC1, 0x81, 0x40, 0x00, 0xC1, 0x81, 0x40, 0x01, 0xC0, 0x80, 0x41, 0x00, 0xC1, 0x81,
            0x40, 0x01, 0xC0, 0x80, 0x41, 0x01, 0xC0, 0x80, 0x41, 0x00, 0xC1, 0x81, 0x40, 0x01, 0xC0,
            0x80, 0x41, 0x00, 0xC1, 0x81, 0x40, 0x00, 0xC1, 0x81, 0x40, 0x01, 0xC0, 0x80, 0x41, 0x01,
            0xC0, 0x80, 0x41, 0x00, 0xC1, 0x81, 0x40, 0x00, 0xC1, 0x81, 0x40, 0x01, 0xC0, 0x80, 0x41,
            0x00, 0xC1, 0x81, 0x40, 0x01, 0xC0, 0x80, 0x41, 0x01, 0xC0, 0x80, 0x41, 0x00, 0xC1, 0x81,
            0x40
        );
        $auchCRCLo = array(
            0x00, 0xC0, 0xC1, 0x01, 0xC3, 0x03, 0x02, 0xC2, 0xC6, 0x06, 0x07, 0xC7, 0x05, 0xC5, 0xC4,
            0x04, 0xCC, 0x0C, 0x0D, 0xCD, 0x0F, 0xCF, 0xCE, 0x0E, 0x0A, 0xCA, 0xCB, 0x0B, 0xC9, 0x09,
            0x08, 0xC8, 0xD8, 0x18, 0x19, 0xD9, 0x1B, 0xDB, 0xDA, 0x1A, 0x1E, 0xDE, 0xDF, 0x1F, 0xDD,
            0x1D, 0x1C, 0xDC, 0x14, 0xD4, 0xD5, 0x15, 0xD7, 0x17, 0x16, 0xD6, 0xD2, 0x12, 0x13, 0xD3,
            0x11, 0xD1, 0xD0, 0x10, 0xF0, 0x30, 0x31, 0xF1, 0x33, 0xF3, 0xF2, 0x32, 0x36, 0xF6, 0xF7,
            0x37, 0xF5, 0x35, 0x34, 0xF4, 0x3C, 0xFC, 0xFD, 0x3D, 0xFF, 0x3F, 0x3E, 0xFE, 0xFA, 0x3A,
            0x3B, 0xFB, 0x39, 0xF9, 0xF8, 0x38, 0x28, 0xE8, 0xE9, 0x29, 0xEB, 0x2B, 0x2A, 0xEA, 0xEE,
            0x2E, 0x2F, 0xEF, 0x2D, 0xED, 0xEC, 0x2C, 0xE4, 0x24, 0x25, 0xE5, 0x27, 0xE7, 0xE6, 0x26,
            0x22, 0xE2, 0xE3, 0x23, 0xE1, 0x21, 0x20, 0xE0, 0xA0, 0x60, 0x61, 0xA1, 0x63, 0xA3, 0xA2,
            0x62, 0x66, 0xA6, 0xA7, 0x67, 0xA5, 0x65, 0x64, 0xA4, 0x6C, 0xAC, 0xAD, 0x6D, 0xAF, 0x6F,
            0x6E, 0xAE, 0xAA, 0x6A, 0x6B, 0xAB, 0x69, 0xA9, 0xA8, 0x68, 0x78, 0xB8, 0xB9, 0x79, 0xBB,
            0x7B, 0x7A, 0xBA, 0xBE, 0x7E, 0x7F, 0xBF, 0x7D, 0xBD, 0xBC, 0x7C, 0xB4, 0x74, 0x75, 0xB5,
            0x77, 0xB7, 0xB6, 0x76, 0x72, 0xB2, 0xB3, 0x73, 0xB1, 0x71, 0x70, 0xB0, 0x50, 0x90, 0x91,
            0x51, 0x93, 0x53, 0x52, 0x92, 0x96, 0x56, 0x57, 0x97, 0x55, 0x95, 0x94, 0x54, 0x9C, 0x5C,
            0x5D, 0x9D, 0x5F, 0x9F, 0x9E, 0x5E, 0x5A, 0x9A, 0x9B, 0x5B, 0x99, 0x59, 0x58, 0x98, 0x88,
            0x48, 0x49, 0x89, 0x4B, 0x8B, 0x8A, 0x4A, 0x4E, 0x8E, 0x8F, 0x4F, 0x8D, 0x4D, 0x4C, 0x8C,
            0x44, 0x84, 0x85, 0x45, 0x87, 0x47, 0x46, 0x86, 0x82, 0x42, 0x43, 0x83, 0x41, 0x81, 0x80,
            0x40
        );
        $length        = ($length <= 0 ? strlen($string) : $length);
        $uchCRCHi    = 0xFF;
        $uchCRCLo    = 0xFF;
        $uIndex        = 0;
        for ($i = 0; $i < $length; $i++) {
            $uIndex        = $uchCRCLo ^ ord(substr($string, $i, 1));
            $uchCRCLo    = $uchCRCHi ^ $auchCRCHi[$uIndex];
            $uchCRCHi    = $auchCRCLo[$uIndex];
        }
        return (chr($uchCRCLo) . chr($uchCRCHi));
    }

    private function translateData($dataArray): array
    {
        $translatedArray = [];

        foreach ($dataArray as $data) {
            $fengsu = substr($data, 0, 2);
            $power = substr($data, 2, 2);
            $moshi = substr($data, 4, 4);
            $wendu = substr($data, 8, 4);
            $wendu = hexdec($wendu);
            $guzhangma = substr($data, 12, 4);
            $shiwen = substr($data, 16, 4);
            $shiwen = hexdec($shiwen) / 10;
            $wenduchuanganqi = substr($data, 20, 4);

            $translated = [
                'wind_speed' => $this->windSpeed[$fengsu] ?? '未知风速',
                'power_state' => $this->power[$power] ?? '未知开关状态',
                'operation_mode' => $this->mode[$moshi] ?? '未知模式',
                'set_temperature' => $wendu . '℃',
                'error_code' => $this->guzhangma[$guzhangma] ?? '未知故障码',
                'room_temperature' => $shiwen . '℃',
                '温度传感器' => $wenduchuanganqi
            ];

            $translatedArray[] = $translated;
        }

        return $translatedArray;
    }

}
