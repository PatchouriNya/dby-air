<?php

namespace App\Http\Controllers\Air;

use App\Http\Controllers\Controller;
use App\Imports\DataImport;
use App\Models\Air\Air_detail;
use App\Models\Air_true;
use App\Models\Client\Air_group_relationship;
use App\Models\Client\Client;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use Validator;

class AirController extends Controller
{
    // 获取客户的空调列表
    public function getAirById(int $id)
    {
        $pageSize = request()->query('size');
        $filters = request()->only(['designation', 'online_state', 'power_state', 'operation_mode', 'wind_speed', 'set_temperature', 'room_temperature', 'air_brand']);

        $data = (new Air_detail())->getOneClient($id, $pageSize, $filters);
        return api($data, 200, '获取空调列表成功');
    }

    // 获取客户的指定组的空调列表(首先先把整个air里面属于该客户的所有未分组的找出来,然后把属于该组的所有空调找出来,最后合并)
    public function getUnGroupedAirByClient(int $id)
    {
        $group_id = request()->query('group_id');
        $data = Air_detail::where('client_id', $id)->where('is_grouped', 0)->get(['id', 'designation', 'show_id']);
        $data2 = Air_group_relationship::where('group_id', $group_id)->with(['airDetail:id,designation,show_id'])->get();
        // 提取数据2中的 air_detail
        $airDetails = collect($data2)->map(function ($item) {
            return $item->airDetail;
        });

        // 合并到数据1中
        $data = $data->merge($airDetails);

        return api($data, 200, '获取未分组空调列表成功');
    }

    // 获取客户的未分组空调列表,并以id数组的形式返回
    public function getGroupedAirByClient(int $id)
    {
        $data = Air_group_relationship::where('group_id', $id)->pluck('air_id');
        return api($data, 200, '获取已分组空调列表成功');
    }

    // 修改某一台空调

    public function show($id)
    {
        // 查询空调的详细信息
        $airDetail = Air_detail::find($id);

        // 如果空调信息不存在，返回错误信息
        if (!$airDetail) {
            return api([], 404, '空调信息不存在');
        }

        // 查询关联的客户信息
        $client = Client::find($airDetail->client_id, ['clientname']);

        // 如果客户信息不存在，返回错误信息
        if (!$client) {
            return api([], 404, '客户信息不存在');
        }

        // 将空调信息和客户信息合并为一个对象
        $data = array_merge($airDetail->toArray(), $client->toArray());

        // 返回合并后的数据
        return api($data, 200, '获取空调信息成功');
    }

    // 某台空调信息，暂用于找所属

    public function refreshAirByClient(int $id)
    {
        $air_true_data = Air_true::where('client_id', $id)->get();
        foreach ($air_true_data as $air) {
            $air_detail = Air_detail::find($air->mac_id);
            if ($air_detail) {
                $air_detail->update([
                    'online_state'      => $air->online_state,
                    'electrify_state'   => $air->electrify_state,
                    'power_state'       => $air->power_state,
                    'operation_mode'    => $air->operation_mode,
                    'wind_speed'        => $air->wind_speed,
                    'wind_mode'         => $air->wind_mode,
                    'set_temperature'   => $air->set_temperature,
                    'room_temperature'  => $air->room_temperature,
                    'voltage'           => $air->voltage,
                    'electric_current'  => $air->electric_current,
                    'power'             => $air->power,
                    'electric_quantity' => $air->electric_quantity,
                ]);
            } else {
                $air_detail = Air_detail::create([
                    'client_id'          => $id,
                    'designation'        => '未设置',
                    'responsible_person' => '未指派',
                    'show_id'            => Air_detail::where('client_id', $id)->max('show_id') + 1,
                    'online_state'       => $air->online_state,
                    'electrify_state'    => $air->electrify_state,
                    'power_state'        => $air->power_state,
                    'operation_mode'     => $air->operation_mode,
                    'wind_speed'         => $air->wind_speed,
                    'wind_mode'          => $air->wind_mode,
                    'set_temperature'    => $air->set_temperature,
                    'room_temperature'   => $air->room_temperature,
                    'voltage'            => $air->voltage,
                    'electric_current'   => $air->electric_current,
                    'power'              => $air->power,
                    'electric_quantity'  => $air->electric_quantity,
                ]);
            }
        }
        return api(null, 201, '真实数据刷新成功');
    }

    // 刷新真实空调数据(指定客户)

    public function update($id, Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'designation'        => 'nullable|string|max:255', // 可以为空，最大长度255
                'air_brand'          => 'nullable|string|max:255',
                'online_state'       => 'nullable|string|max:255',
                'electrify_state'    => 'nullable|max:255',
                'power_state'        => 'nullable|max:255',
                'operation_mode'     => 'nullable|string|max:255',
                'wind_speed'         => 'nullable|string|max:255',
                'wind_mode'          => 'nullable|string|max:255',
                'set_temperature'    => 'nullable|string|max:255',
                'room_temperature'   => 'nullable|string|max:255',
                'voltage'            => 'nullable|string|max:255',
                'electric_current'   => 'nullable|string|max:255',
                'power'              => 'nullable|string|max:255',
                'electric_quantity'  => 'nullable|string|max:255',
                'responsible_person' => 'nullable|string|max:255'
            ]);
            $data = $validator->validate(); // 尝试验证数据
            $airModel = Air_detail::findOrFail($id);
            $res = $airModel->update($data);
            return api($res, 201, '更新成功');
        } catch (ValidationException $e) {
            return api($e->errors(), 400, '表单验证不通过');
        } catch (\Exception $e) {
            return api([], 500, '更新失败: ' . $e->getMessage());
        }
    }

    public function testExcel()
    {
        $filepath = storage_path('../resources/tt.xlsx');
        //        $import = Excel::toCollection(new DataImport, $filepath);
        $skip = 1;
        $import = new DataImport($skip);
        Excel::import($import, $filepath);
        $rows = $import->getRows();
        $formattedData = $rows->map(function ($row) {
            // 把第七列从16进制转化为10进制
            $row[7] = hexdec($row[7]) / 10 . '℃';
            // 返回剩下的

            return ['id' => $row[0], 'online' => $row[7],];
        });

        return api($formattedData, 200, '导入成功');
    }
}
