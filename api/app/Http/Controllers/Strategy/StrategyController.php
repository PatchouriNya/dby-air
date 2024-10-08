<?php

namespace App\Http\Controllers\Strategy;

use App\Http\Controllers\Controller;
use App\Models\Client\Air_group;
use App\Models\Strategy\Strategy;
use Carbon\Carbon;
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
    public function index(Request $request)
    {
        // 获取策略列表
        try {
            $client_id = $request->query('client_id');
            if ($request->query('all_data') == 'true') {
                // 如果存在 all_data 参数 为 true，直接返回所有数据
                $data = Strategy::where('client_id', $client_id)->get();
            } else {
                // 分页
                $pageSize = $request->query('pageSize') ?? 5;
                $name = $request->input('name');
                // 如果提供了名称，添加名称检索条件
                $query = Strategy::query();
                if ($name) {
                    $query->where('name', 'like', '%' . $name . '%');
                }

                $data = $query->where('client_id', $client_id)->paginate($pageSize);
            }

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
                    'client_id'       => 'required|integer',
                    'name'            => 'required|string|max:20',
                    'info'            => 'required|string|max:50',
                    'power_state'     => 'required|string|max:20',
                    'operation_mode'  => 'required|string|max:20',
                    'wind_speed'      => 'required|string|max:20',
                    'wind_mode'       => 'required|string|max:20',
                    'set_temperature' => 'required|string|max:20',
                    'delta'           => 'numeric|min:0|max:5',
                    'electrify_state' => 'nullable|string|max:20',
                    'start_date'      => 'required|date',
                    'end_date'        => 'required|date',
                    'start_time'      => 'required|string|max:20',
                    'end_time'        => 'required|string|max:20',
                    'interval_time'   => 'numeric|min:1|max:30',
                    'week_days'       => 'required|array|min:0|max:6'
                ]
            );
            if ($validator->fails()) {
                return api(null, 400, $validator->errors()->first());
            }
            $data = $validator->validate();
            $data['week_days'] = json_encode($data['week_days']);
            // 检查是否重名

            $exist = Strategy::where('client_id', $data['client_id'])
                ->where('name', $data['name'])
                ->first();
            if ($exist) {
                return api(null, 400, '策略名称已存在');
            }
            $strategy = Strategy::create($data);
            return api($strategy, 201, '新增策略成功');
        } catch (\Exception $e) {
            return api(null, 500, $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
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
            if ($strategy->status === 1)
                return api(null, 400, '策略正在被使用,请停用后再尝试删除');
            $res = $strategy->delete();
            if ($res) {
                return api(null, 204, '删除策略成功');
            }
            return api(null, 500, '删除策略失败');
        } catch (\Exception $e) {
            return api(null, 500, $e->getMessage());
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
                    'name'            => 'required|string|max:20',
                    'info'            => 'required|string|max:50',
                    'power_state'     => 'required|string|max:20',
                    'operation_mode'  => 'required|string|max:20',
                    'wind_speed'      => 'required|string|max:20',
                    'wind_mode'       => 'required|string|max:20',
                    'set_temperature' => 'required|string|max:20',
                    'delta'           => 'numeric|min:0|max:5',
                    'electrify_state' => 'nullable|string|max:20',
                    'start_date'      => 'required|date',
                    'end_date'        => 'required|date',
                    'start_time'      => 'required|string|max:20',
                    'end_time'        => 'required|string|max:20',
                    'interval_time'   => 'numeric|min:1|max:30',
                    'week_days'       => 'required|array|min:1|max:7'
                ]
            );
            if ($validator->fails()) {
                return api(null, 400, $validator->errors()->first());
            }
            $data = $validator->validate();
            $strategy = Strategy::find($id);
            $exist = Strategy::where('client_id', $strategy->client_id)
                ->where('name', $data['name'])
                ->first();
            if ($exist && $exist->id != $strategy->id) {
                return api(null, 400, '策略名称已存在');
            }
            if (!$strategy) {
                return api(null, 404, '策略不存在');
            }
            if ($strategy->status === 1)
                return api(null, 400, '策略正在被使用,请停用后再尝试修改');

            $data['week_days'] = json_encode($data['week_days']);
            $strategy->update($data);
            return api($strategy, 201, '更新策略成功');
        } catch (\Exception $e) {
            return api(null, 500, $e->getMessage());
        }
    }
}
