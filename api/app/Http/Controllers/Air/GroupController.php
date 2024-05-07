<?php

namespace App\Http\Controllers\Air;

use App\Events\AirGroupStrategyUpdated;
use App\Http\Controllers\Controller;
use App\Models\Air\Air_detail;
use App\Models\Client\Air_group;
use App\Models\Client\Air_group_relationship;
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
        $res = $airGroup->delete();
        if ($res) {
            event(new AirGroupStrategyUpdated($airGroup));
            return api($res, 204, '删除组成功');
        }

        return api(null, 404, '删除组失败');
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
        $res = $airGroup->update(['strategy_id' => \request('strategy_id')]);
        if ($res) {
            event(new AirGroupStrategyUpdated($airGroup));
            return api(null, 201, '策略设置成功');
        }
        return api(null, 400, '策略设置失败');
    }


}
