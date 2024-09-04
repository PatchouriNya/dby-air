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
    public function getLatestData(Request $request)
    {
        $res = $this->getHostInfo($request->input('client_id'));
        if (!$res) {
            return api(null, 400, '该客户还没有接通真实数据哟~');
        }

        $key = 'reading' . $res['com_port'];
        $redis = Redis::connection();
        if ($redis->exists($key)) {
            return api(null, 400, '该单位数据正在被读取,请等待几秒后再次尝试~');
        }

        $url = 'http://47.103.60.199:9000/serial/send?host_address=' . $res['host_address'] . '&com_port=' . $res['com_port'];

        $maxRetries = 5;
        $retryCount = 0;

        try {
            $redis->setex($key, 30, 1);
            // 设置超时时间为30秒

            while ($retryCount < $maxRetries) {
                $response = Http::timeout(30)->get($url);

                // 获取返回的数据
                $responseData = $response->json();
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
                        return api(null, 200, '获取最新数据成功');
                    }
                }

                // 若返回结果不符合预期，增加重试次数
                $retryCount++;
            }

            // 达到最大重试次数仍未成功
            $redis->del($key);
            return api(null, 500, '读取数据超时,请再试几次或联系管理员');
        } catch (\Illuminate\Http\Client\RequestException $e) {
            $redis->del($key);
            return api(null, 500, '请求超时，无法获取最新数据');
        }
    }


    private function getHostInfo($client_id)
    {
        // 传客户id
        $client = Client::where('id', $client_id)->first(['host_address', 'com_port', 'start_address'])->toArray();
        $host_address = $client['host_address'];
        $com_port = $client['com_port'];
        $res = [];
        //返回一个数组，包含host_address和com_port
        $res['host_address'] = $host_address;
        $res['com_port'] = $com_port;
        if ($host_address === null || $com_port === null)
            return false;

        return $res;


    }

    public function controlAir(Request $request)
    {
        $air_id = $request->input('air_id');
        $res = $this->getHostInfo($request->input('client_id'));
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

}
