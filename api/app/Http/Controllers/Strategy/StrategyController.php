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
    public function index()
    {
        // 获取策略列表
        try {
            if (\request()->query('all_data') == 'true') {
                // 如果存在 all_data 参数 为 true，直接返回所有数据
                $data = Strategy::all();
            } else {
                // 分页
                $pageSize = \request()->query('pageSize') ?? 5;
                $name = \request()->input('name');
                // 如果提供了名称，添加名称检索条件
                $query = Strategy::query();
                if ($name) {
                    $query->where('name', 'like', '%' . $name . '%');
                }

                $data = $query->paginate($pageSize);
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
                    'name'            => 'required|string|max:20',
                    'info'            => 'required|string|max:50',
                    'power_state'     => 'required|string|max:20',
                    'operation_mode'  => 'required|string|max:20',
                    'wind_speed'      => 'required|string|max:20',
                    'wind_mode'       => 'required|string|max:20',
                    'set_temperature' => 'required|string|max:20',
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
            $data['week_days'] = json_encode($data['week_days']);
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
            $res = $strategy->delete();
            if ($res) {
                Air_group::where('strategy_id', $id)->update(['strategy_id' => null]);
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
            if (!$strategy) {
                return api(null, 404, '策略不存在');
            }
            $data['week_days'] = json_encode($data['week_days']);
            $strategy->update($data);
            return api($strategy, 201, '更新策略成功');
        } catch (\Exception $e) {
            return api(null, 500, $e->getMessage());
        }
    }
}
