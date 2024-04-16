<?php

namespace App\Http\Controllers\Air;

use App\Http\Controllers\Controller;
use App\Models\Air\Air_detail;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Validator;

class AirController extends Controller
{
    // 获取客户的空调列表
    public function getAirById(int $id)
    {
        $pageSize = request()->query('size');
        $filters = request()->only(['designation', 'online_state', 'power_state', 'operation_mode', 'wind_speed', 'set_temperature', 'room_temperature','air_brand']);

        $data = (new Air_detail())->getOneClient($id, $pageSize, $filters);
        foreach ($data as $item ){
            switch ($item->wind_mode){
                case  1:
                    $item->wind_mode = '走风';
                    break;
                case 2:
                    $item->wind_mode = '扫风';
                    break;
            }
    }
        return api($data, 200, '获取空调列表成功');
    }

    // 修改某一台空调
    public function update($id, Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'designation' => 'nullable|string|max:255', // 可以为空，最大长度255
                'air_brand' => 'nullable|string|max:255',
                'online_state' => 'nullable|string|max:255',
                'electrify_state' => 'nullable|max:255',
                'power_state' => 'nullable|string|max:255',
                'operation_mode' => 'nullable|string|max:255',
                'wind_speed' => 'nullable|string|max:255',
                'wind_mode' => 'nullable|between:1,5',
                'set_temperature' => 'nullable|string|max:255',
                'room_temperature' => 'nullable|string|max:255',
                'voltage' => 'nullable|string|max:255',
                'electric_current' => 'nullable|string|max:255',
                'power' => 'nullable|string|max:255',
                'electric_quantity' => 'nullable|string|max:255',
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
}
