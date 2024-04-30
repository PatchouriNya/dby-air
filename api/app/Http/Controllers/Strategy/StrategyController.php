<?php

namespace App\Http\Controllers\Strategy;

use App\Http\Controllers\Controller;
use App\Models\Strategy\Strategy;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class StrategyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        // 获取策略列表
        try {
            $data = Strategy::all();
            return api($data, 200, '获取策略列表成功');
        } catch (\Exception $e) {
            return api(null, 500, $e->getMessage());
        }
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        // 新增策略
        try {
            $validator = \Validator::make($request->all(), [
                'name' =>'required|string|max:20',
                'info' =>'required|string|max:50',
                'power_state' =>'required|string|max:20',
                'operation_mode' =>'required|string|max:20',
                'wind_speed' =>'required|string|max:20',
                'wind_mode' =>'required|string|max:20',
                'set_temperature' =>'required|string|max:20',
                'electrify_state' =>'nullable|string|max:20'
                ]
            );
            if ($validator->fails()) {
                return api(null, 400, $validator->errors()->first());
            }
            $data = $validator->validate();
            $strategy = Strategy::create($data);
            return api($strategy, 201, '新增策略成功');
        } catch (\Exception $e) {
            return api(null,500, $e->getMessage());
        }
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        // 更新策略
        try {
            $validator = \Validator::make($request->all(), [
                'name' =>'required|string|max:20',
                'info' =>'required|string|max:50',
                'power_state' =>'required|string|max:20',
                'operation_mode' =>'required|string|max:20',
                'wind_speed' =>'required|string|max:20',
                'wind_mode' =>'required|string|max:20',
                'set_temperature' =>'required|string|max:20',
                'electrify_state' =>'nullable|string|max:20'
                ]
            );
            if ($validator->fails()) {
                return api(null, 400, $validator->errors()->first());
            }
            $data = $validator->validate();
            $strategy = Strategy::find($id);
            if (!$strategy) {
                return api(null, 404, '策略不存在');
            }
            $strategy->update($data);
            return api($strategy, 200, '更新策略成功');
    }catch (\Exception $e) {
            return api(null,500, $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        // 删除策略
        try {
            $strategy = Strategy::find($id);
            if (!$strategy) {
                return api(null, 404, '策略不存在');
            }
            $strategy->delete();
            return api(null, 204, '删除策略成功');
        } catch (\Exception $e) {
            return api(null, 500, $e->getMessage());
        }
    }
}
