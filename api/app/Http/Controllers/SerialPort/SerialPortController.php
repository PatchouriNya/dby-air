<?php

namespace App\Http\Controllers\SerialPort;

use App\Http\Controllers\Controller;
use App\Models\Air\Air_detail;
use App\Models\Client\Air_group;
use App\Models\Client\Air_group_relationship;
use App\Models\Client\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;

class SerialPortController extends Controller
{
    public array $device01_operation_mode = ['0000' => '制热', '0001' => '制热', '0002' => '制热', '0003' => '制冷', '0004' => '自动', '0005' => '除湿', '0009' => '送风'];
    public array $device01_power_state = ['0000' => '关机', '0001' => '开机'];
    public array $device01_wind_speed = ['0001' => '低风', '0002' => '中风', '0003' => '高风', '0004' => '未知4', '0000' => '未知0'];

    public array $device01_write_operation_mode = ['制热' => '0002', '制冷' => '0003', '送风' => '0009', '自动' => '0004', '除湿' => '0005'];
    public array $device01_write_power_state = ['关机' => '0000', '开机' => '0001'];
    public array $device01_write_wind_speed = ['低风' => '0001', '中风' => '0002', '高风' => '0003', '自动' => '0004'];

    public string $random_ip = 'http://47.103.60.199:9000';

    public function getLatestData(Request $request)
    {
        $res = $this->getHostInfo($request->input('client_id'));
        if (!$res) {
            return api(null, 400, '该客户还没有接通真实数据哟~');
        }

        if ($res['device_type'] === 1) {
            $url = $this->random_ip . '/tcp/get-latest-data?total_air=5&ip=49.65.165.86';
            $response = Http::timeout(30)->get($url);
            $responseData = $response->json();
            $body = $responseData['data']['data'];
            if (strlen($body) !== 498) {
                return api(null, 500, '服务器忙,请30s后尝试');
            }
            $fixedLength = 90; // 每个部分的固定长度
            $partsList = [];

            for ($i = 38; $i < strlen($body) - 10; $i += $fixedLength) {
                $end = min($i + $fixedLength, strlen($body) - 10);
                $str = substr($body, $i, $end);
                // 截取$str前2位为show_id
                $show_id = substr($str, 0, 2);
                $show_id = ltrim($show_id, '0');
                //截取接下来4位是室温
                $room_temperature = substr($str, 2, 4);
                $room_temperature = hexdec($room_temperature) / 10 . '℃';
                $set_temperature = substr($str, 6, 4);
                $set_temperature = hexdec($set_temperature) / 10 . '℃';
                $power_state = substr($str, 10, 4);
                $power_state = $power_state ? $this->device01_power_state[$power_state] : '未知';
                $operation_mode = substr($str, 14, 4);
                $operation_mode = $operation_mode ? $this->device01_operation_mode[$operation_mode] : '未知';
                $wind_speed = substr($str, 18, 4);
                $wind_speed = $wind_speed ? $this->device01_wind_speed[$wind_speed] : '未知';
                Air_detail::where('client_id', $request->input('client_id'))->where('show_id', $show_id)->update([
                    'room_temperature' => $room_temperature,
                    'set_temperature'  => $set_temperature,
                    'power_state'      => $power_state,
                    'operation_mode'   => $operation_mode,
                    'wind_speed'       => $wind_speed,
                ]);
                $partsList[] = [
                    "show_id"          => $show_id,
                    "room_temperature" => $room_temperature,
                    "set_temperature"  => $set_temperature,
                    "power_state"      => $power_state,
                    "operation_mode"   => $operation_mode,
                    "wind_speed"       => $wind_speed,
                ];
            }


            return api($partsList, 200, '获取数据成功');
        }

        $key = 'reading' . $res['com_port'];
        $redis = Redis::connection();
        if ($redis->exists($key)) {
            return api(null, 400, '该单位数据正在被读取,请等待几秒后再次尝试~');
        }

        $url = 'http://47.103.60.199:9000/serial/send?host_address=' . $res['host_address'] . '&com_port=' . $res['com_port'] . '&total=' . $res['total_air'];

        $maxRetries = 10;
        $retryCount = 0;


        try {
            $redis->setex($key, 30, 1);
            // 设置超时时间为30秒
            $msg = '';
            while ($retryCount < $maxRetries) {
                $response = Http::timeout(30)->get($url);

                // 获取返回的数据
                $responseData = $response->json();
                $msg = $responseData['msg'] ?? '未知错误';
                $responseData['code'] = $responseData['code'] ?? 500;
                if ($responseData['code'] === 200) {
                    $airs = $responseData['data']['airs'];
                    if ($airs !== null) {
                        foreach ($airs as $item) {
                            if ($item['error_code'] === '在线' && ((int)$item['set_temperature'] >= 16 && (int)$item['set_temperature'] <= 32)) {
                                $res = Air_detail::where('client_id', $request->input('client_id'))->where('show_id', $item['id'])->update([
                                    'read_base_address'  => $item['read_base_address'],
                                    'write_base_address' => $item['write_base_address'],
                                    'wind_speed'         => $item['wind_speed'],
                                    'power_state'        => $item['power_state'],
                                    'operation_mode'     => $item['operation_mode'],
                                    'set_temperature'    => $item['set_temperature'] . '℃',
                                    'room_temperature'   => $item['room_temperature'] . '℃',
                                    'online_state'       => $item['error_code']
                                ]);

                                if (!$res) {
                                    Air_detail::create([
                                        'client_id'          => $request->input('client_id'),
                                        'show_id'            => $item['id'],
                                        'read_base_address'  => $item['read_base_address'],
                                        'write_base_address' => $item['write_base_address'],
                                        'wind_speed'         => $item['wind_speed'],
                                        'power_state'        => $item['power_state'],
                                        'operation_mode'     => $item['operation_mode'],
                                        'set_temperature'    => $item['set_temperature'] . '℃',
                                        'room_temperature'   => $item['room_temperature'] . '℃',
                                        'online_state'       => $item['error_code']
                                    ]);
                                }
                            }
                        }
                        $redis->del($key);
                        return api($responseData['data'], 200, '获取最新数据成功');
                    }
                }

                // 若返回结果不符合预期，增加重试次数
                $retryCount++;
            }

            // 达到最大重试次数仍未成功
            $redis->del($key);
            return api(null, 500, $msg);
        } catch (\Illuminate\Http\Client\RequestException $e) {
            $redis->del($key);
            return api(null, 500, '请求超时，无法获取最新数据');
        }
    }

    private function getHostInfo($client_id)
    {
        // 传客户id
        $client = Client::where('id', $client_id)->first(['host_address', 'com_port', 'start_address', 'total_air', 'device_type'])->toArray();
        $host_address = $client['host_address'];
        $com_port = $client['com_port'];
        $total_air = $client['total_air'];
        $device_type = $client['device_type'];
        $res = [];
        //返回一个数组，包含host_address和com_port
        $res['host_address'] = $host_address;
        $res['com_port'] = $com_port;
        $res['total_air'] = (int)$total_air;
        $res['device_type'] = (int)$device_type;
        if ($host_address === null || $com_port === null)
            return false;

        return $res;


    }

    public function controlAir(Request $request)
    {
        $air_id = $request->input('air_id');
        $res = $this->getHostInfo($request->input('client_id'));
        $wind_speed = $request->input('wind_speed');
        $power_state = $request->input('power_state');
        $operation_mode = $request->input('operation_mode');
        $set_temperature = $request->input('set_temperature');

        if (!$res)
            return api(null, 400, '该客户还没有接通真实数据哟~');

        if ($res['device_type'] === 1) {
            $wind_speed = $this->device01_write_wind_speed[$wind_speed];
            $power_state = $this->device01_write_power_state[$power_state];
            $operation_mode = $this->device01_write_operation_mode[$operation_mode];

            // air_id转化为16进制并且补足2位
            $air_id_hex = dechex($air_id);
            $air_id_hex = str_pad($air_id_hex, 2, '0', STR_PAD_LEFT);
            //温度转化为16进制不足4位则补0
            $set_temperature_hex = dechex($set_temperature * 10);
            $set_temperature_hex = str_pad($set_temperature_hex, 4, '0', STR_PAD_LEFT);
            // 设置温度
            $command1 = "000e060001000000000001" . $air_id_hex . "0001" . $set_temperature_hex;
            // 设置开关机
            $command2 = "000e060001000000000001" . $air_id_hex . "0002" . $power_state;
            // 设置风速
            $command3 = "000e060001000000000001" . $air_id_hex . "0005" . $wind_speed;
            // 设置模式
            $command4 = "000e060001000000000001" . $air_id_hex . "0003" . $operation_mode;

            if ($request->input('power_state') === '关机') {
                $url2 = $this->random_ip . '/tcp/send-msg?ip=49.65.165.86&msg=' . $command2;
                $response = Http::timeout(30)->get($url2);

            } else {
                $url1 = $this->random_ip . '/tcp/send-msg?ip=49.65.165.86&msg=' . $command1;
                $url2 = $this->random_ip . '/tcp/send-msg?ip=49.65.165.86&msg=' . $command2;
                $url3 = $this->random_ip . '/tcp/send-msg?ip=49.65.165.86&msg=' . $command3;
                $url4 = $this->random_ip . '/tcp/send-msg?ip=49.65.165.86&msg=' . $command4;

                $response = Http::timeout(30)->get($url2);
                $response = Http::timeout(30)->get($url3);
                $response = Http::timeout(30)->get($url4);
                $response = Http::timeout(30)->get($url1);
            }


            $responseData = $response->json();

            Air_detail::where('client_id', $request->input('client_id'))->where('show_id', $air_id)->update([
                'wind_speed'      => $request->input('wind_speed'),
                'power_state'     => $request->input('power_state'),
                'operation_mode'  => $request->input('operation_mode'),
                'set_temperature' => $request->input('set_temperature') . '℃',
            ]);

            return api($responseData, 200, '控制成功');
        }

        $key = 'reading' . $res['com_port'];
        $redis = Redis::connection();
        if ($redis->exists($key))
            return api(null, 400, '该单位数据正在被读取,请等待几秒后再次尝试~');


        $url = 'http://47.103.60.199:9000/serial/control-air?host_address=' . $res['host_address'] . '&com_port=' . $res['com_port'] . '&air_id=' . $air_id . '&wind_speed=' . $wind_speed . '&power_state=' . $power_state . '&operation_mode=' . $operation_mode . '&set_temperature=' . $set_temperature;
        $response = Http::post($url);
        $responseData = $response->json();
        if ($responseData['code'] !== 200)
            return api(null, 500, '请重新发送指令~');
        //        $responseData = $response->json();

        Air_detail::where('client_id', $request->input('client_id'))->where('show_id', $air_id)->update([
            'wind_speed'      => $wind_speed,
            'power_state'     => $power_state,
            'operation_mode'  => $operation_mode,
            'set_temperature' => $set_temperature . '℃',
        ]);

        return api(null, 200, '发送指令成功');
    }

    public function controlAirGroup(Request $request)
    {
        $group_id = $request->input('group_id');
        $client_id = Air_group::where('id', $group_id)->value('client_id');
        // 传group_id
        $airs = Air_group_relationship::where('group_id', $group_id)->pluck('air_id')->toArray();
        $airs_string = '[' . implode(',', $airs) . ']';
        $res = $this->getHostInfo($client_id);
        if (!$res)
            return api(null, 400, '该客户还没有接通真实数据哟~');

        $key = 'reading' . $res['com_port'];
        $redis = Redis::connection();
        if ($redis->exists($key))
            return api(null, 400, '该单位数据正在被读取,请等待几秒后再次尝试~');

        $wind_speed = $request->input('wind_speed');
        $power_state = $request->input('power_state');
        $operation_mode = $request->input('operation_mode');
        $set_temperature = $request->input('set_temperature');

        $url = 'http://47.103.60.199:9000/serial/control-air-group?host_address=' . $res['host_address'] . '&com_port=' . $res['com_port'] . '&airs=' . $airs_string . '&wind_speed=' . $wind_speed . '&power_state=' . $power_state . '&operation_mode=' . $operation_mode . '&set_temperature=' . $set_temperature;
        $response = Http::post($url);
        $responseData = $response->json();
        if ($responseData['code'] !== 200)
            return api(null, 500, '请重新发送指令~');

        foreach ($airs as $air_id) {
            Air_detail::where('client_id', $client_id)->where('show_id', $air_id)->update([
                'wind_speed'      => $wind_speed,
                'power_state'     => $power_state,
                'operation_mode'  => $operation_mode,
                'set_temperature' => $set_temperature . '℃',
            ]);
        }
        return api(null, 200, '发送指令成功');
    }

    public function controlAirRange(Request $request)
    {
        $client_id = $request->input('client_id');
        $start_number = $request->input('start_number');
        $end_number = $request->input('end_number');
        $min = $request->input('min_temperature') ?? 16;
        $airs = Air_detail::where('client_id', $client_id)->where('show_id', '>=', $start_number)->where('show_id', '<=', $end_number)->pluck('show_id')->toArray();
        // 默认低风,制冷,开机
        /*            $wind_speed = $request->input('wind_speed');
                    $power_state = $request->input('power_state');
                    $operation_mode = $request->input('operation_mode');*/
        $res = $this->getHostInfo($client_id);
        if (!$res)
            return api(null, 400, '该客户还没有接通真实数据哟~');

        $key = 'reading' . $res['com_port'];
        $redis = Redis::connection();
        if ($redis->exists($key))
            return api(null, 400, '该单位数据正在被读取,请等待几秒后再次尝试~');

        $url = 'http://47.103.60.199:9000/serial/control-air-range?host_address=' . $res['host_address'] . '&com_port=' . $res['com_port'] . '&start_number=' . $start_number . '&end_number=' . $end_number . '&min_temperature=' . $min;
        $response = Http::post($url);
        $responseData = $response->json();
        if ($responseData['code'] !== 200)
            return api(null, 500, '请重新发送指令~');

        $temp = $min;
        foreach ($airs as $air_id) {
            Air_detail::where('client_id', $client_id)->where('show_id', $air_id)->update([
                'wind_speed'      => '低风',
                'power_state'     => '开机',
                'operation_mode'  => '制冷',
                'set_temperature' => $temp . '℃',
            ]);
            $temp++;
            if ($temp > 30)
                $temp = $min;
        }
        return api(null, 200, '发送指令成功');


    }

}
