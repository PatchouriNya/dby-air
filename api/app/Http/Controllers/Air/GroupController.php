<?php

namespace App\Http\Controllers\Air;

use App\Events\AirGroupStrategyUpdated;
use App\Http\Controllers\Controller;
use App\Models\Air\Air_detail;
use App\Models\Client\Air_group;
use App\Models\Client\Air_group_relationship;
use App\Models\Strategy\Strategy;
use Illuminate\Http\Request;
use Nette\Schema\ValidationException;

class GroupController extends Controller
{
    public function index(Request $request)
    {
        $pageSize = $request->query('pageSize') ?? 5;
        $client_id = $request->input('client_id');
        $name = $request->input('name');

        $query = Air_group::where('client_id', $client_id);

        // 如果提供了名称，添加名称检索条件
        if ($name) {
            $query->where('name', 'like', '%' . $name . '%');
        }

        $data = $query->paginate($pageSize);

        return api($data, 200, '获得该客户下空调组成功');
    }

    public function store(Request $request)
    {
        try {
            $validator = \Validator::make($request->all(), [
                'name'      => 'required|string|max:255',
                'info'      => 'nullable|string|max:255',
                'client_id' => 'required|integer|exists:clients,id',
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors()->first();
                return api(null, 400, $errors);
            }
            $data = $validator->validate();
            $res = Air_group::create($data);
            return api($res->id, 201, '创建组成功');
        } catch (\Exception $e) {
            return api(null, 500, $e->getMessage());
        }
    }

    public function destroy($id)
    {
        if (Air_group_relationship::where('group_id', $id)->first())
            return api(null, 400, '该组下有空调,不能删除,请清空后再尝试');
        $airGroup = Air_group::find($id);
        $ori_id = $airGroup->strategy_id;
        $res = $airGroup->delete();
        if ($res) {
            if ($ori_id) {
                $res = Air_group::where('strategy_id', $ori_id)->exists();
                if ($res) {
                    Strategy::find($ori_id)->update(['status' => 1]);
                } else {
                    Strategy::find($ori_id)->update(['status' => 0]);
                }
            }
            return api(null, 204, '删除组成功');
        }

        return api(null, 404, '删除组失败');
    }

    public function update(Request $request, $id)
    {

        try {

            $validator = \Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'info' => 'nullable|string|max:255'
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors()->first();
                return api(null, 400, $errors);
            }
            $data = $validator->validate();
            $res = Air_group::where('id', $id)->update($data);
            if ($res)
                return api($res, 201, '更新组成功');
            return api(null, 404, '更新组失败');
        } catch (\Exception $e) {
            return api(null, 500, $e->getMessage());
        }
    }

    public function addAirToGroup(Request $request, $id)
    {
        $air_ids = $request->input('air_id');
        $air_id_array = explode(',', $air_ids);
        $error_air_id_array = [];
        foreach ($air_id_array as $air_id) {
            // 在这里对每个 $air_id 进行操作
            $res = Air_group_relationship::create(['air_id' => $air_id, 'group_id' => $id]);
            if (!$res) {
                $error_air_id_array[] = $air_id;
            } else
                Air_detail::where('id', $air_id)->update(['is_grouped' => 1]);

        }

        if (count($error_air_id_array) > 0) {
            return api(null, 400, '添加失败,请检查air_id是否正确添加失败的id如下:' . implode(',', $error_air_id_array));
        }
        return api(null, 201, '添加成功');
    }

    public function removeAirFromGroup(Request $request, $id)
    {
        $air_ids = $request->input('air_id');
        $air_id_array = explode(',', $air_ids);
        $error_air_id_array = [];

        foreach ($air_id_array as $air_id) {
            // 在这里对每个 $air_id 进行操作
            $res = Air_group_relationship::where('air_id', $air_id)->where('group_id', $id)->delete();
            if (!$res) {
                $error_air_id_array[] = $air_id;
            } else
                Air_detail::where('id', $air_id)->update(['is_grouped' => 0]);
        }

        if (count($error_air_id_array) > 0) {
            return api(null, 400, '移除失败，请检查air_id是否正确。失败的id如下: ' . implode(',', $error_air_id_array));
        }

        return api(null, 204, '移除成功');
    }


    public function getAirByGroup($id)
    {
        $pageSize = \request()->query('pageSize') ?? 10;
        $data = Air_group_relationship::where('group_id', $id)->with(['airDetail:id,client_id,show_id,designation,responsible_person'])->paginate($pageSize);
        // 返回空调详情数组
        return api($data, 200, '成功获取指定组的所有空调');
    }

    public function setStrategy($id)
    {
        $airGroup = Air_group::find($id);
        $client_id = $airGroup->client_id;
        $ori_id = $airGroup->strategy_id;
        // ori_Id变为数组
        $strategy_id = \request('strategy_id');
        $error_strategy_id_array = [];
        $msg = '';
        if ($strategy_id === []) {
            $res = $airGroup->update(['strategy_id' => null]);
            $msg = '策略停用成功';
        } else {
            // 循环判断策略是否存在,存在则更新状态为1,不存在就进入error_strategy_id_array最后去掉错误的值再入库
            foreach ($strategy_id as $strategy) {
                if ($strategy) {
                    $res = Strategy::find($strategy);
                    if ($res) {
                        $res->update(['status' => 1]);
                    } else {
                        $error_strategy_id_array[] = $strategy;
                    }
                }
            }
            $res = $airGroup->update(['strategy_id' => json_encode(array_diff($strategy_id, $error_strategy_id_array))]);
            $msg = '策略设置成功';
        }
        if ($res) {
            if ($ori_id) {
                $ori_id_bak = $ori_id;
                // 仍然在使用的策略id数组
                $res_arr = [];
                $arr = Air_group::whereNotNull('strategy_id')->where('client_id', $client_id)->get('strategy_id')->toArray();
                foreach ($arr as $value) {
                    foreach ($ori_id as $ori_strategy) {
                        if (in_array($ori_strategy, $value['strategy_id'])) {
                            $res_arr[] = $ori_strategy;
                        }
                    }
                    $ori_id = array_diff($ori_id, $res_arr);
                    if ($ori_id == [])
                        break;
                }
                // 不在使用的id数组
                $ori_id_arr = array_diff($ori_id_bak, $res_arr);
                // 将不在使用的策略启用状态改为0
                Strategy::whereIn('id', $ori_id_arr)->update(['status' => 0]);

                if ($error_strategy_id_array == [])
                    return api(null, 201, $msg);
                else
                    return api(null, 201, '策略设置成功,但部分策略id不存在,请检查:' . implode(',', $error_strategy_id_array));
            }
        } else {
            return api(null, 400, '策略设置失败,请检查策略id是否正确');
        }
        return api(null, 201, '策略设置成功');
    }


    public function groupControl(Request $request, $id)
    {
        //        根据集控模式($id是否为0)，发送分组控制指令或集体强控
        try {
            $validator = \Validator::make($request->all(), [
                    'power_state'     => 'required|string|max:20',
                    'operation_mode'  => 'required|string|max:20',
                    'wind_speed'      => 'required|string|max:20',
                    'wind_mode'       => 'required|string|max:20',
                    'set_temperature' => 'required|string|max:20',
                ]
            );
            if ($validator->fails()) {
                return api(null, 400, $validator->errors()->first());
            }
            $data = $validator->validate();
            $error_air_id_array = [];
            if ($id == 0) {
                $client_id = \Validator::make($request->all(), [
                    'client_id' => 'required|integer|exists:clients,id',
                ]);
                if ($client_id->fails()) {
                    return api(null, 400, $client_id->errors()->first());
                }
                $client_id = (int)$client_id->validate()['client_id'];
                $air_ids = Air_detail::where('client_id', $client_id)->pluck('id')->toArray();
                unset($data['client_id']);
                foreach ($air_ids as $air_id) {
                    $res = Air_detail::where('id', $air_id)->update($data);
                    if (!$res) {
                        $error_air_id_array[] = $air_id;
                    }
                }
                if (count($error_air_id_array) > 0) {
                    return api(null, 400, '指令部分发送失败,请检查air_id是否正确。失败的id如下: ' . implode(',', $error_air_id_array));
                }
                return api($client_id, 201, '集体强控成功');

            } else {
                $air_ids = Air_group_relationship::where('group_id', $id)->pluck('air_id')->toArray();
                unset($data['client_id']);
                foreach ($air_ids as $air_id) {
                    $res = Air_detail::where('id', $air_id)->update($data);
                    if (!$res) {
                        $error_air_id_array[] = $air_id;
                    }
                }
                if (count($error_air_id_array) > 0) {
                    return api(null, 400, '指令部分发送失败,请检查air_id是否正确。失败的id如下: ' . implode(',', $error_air_id_array));
                }
                return api($request->all(), 201, '发送指令成功');
            }

        } catch (\Exception $e) {
            return api(null, 500, $e->getMessage());
        }
    }


}
