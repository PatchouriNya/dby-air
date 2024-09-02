<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Account\Account;
use App\Models\Air\Air_detail;
use App\Models\Client\Client;
use App\Models\Client\Client_account_relationship;
use App\Models\Client\Client_overview;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    // 获取对应账号下的客户列表(层级显示)
    public function getClientByAccount($id)
    {
        $data = (new Account())->getClient($id);
        $client_id = $data['with_client']['client_id'];
        $client = (new Client());
        $data = $client->getOverview($client_id);
        return api($data, 200, '获取客户信息成功');
    }

    // 获取对应账号下的客户列表(用于下拉树,看不到自己,以及type为1的单位)
    public function getClientSelectTree($accountId, $clientId)
    {
        $data = Client::getAccountClients($accountId, $clientId);

        return api($data, 200, '获取客户信息成功');
    }

    // 找爸爸接口
    public function getParent($id)
    {
        $parentId = Client::where('id', $id)->first(['pid'])->pid;
        $data = Client::where('id', $parentId)->first();
        return api($data, 200, '获取上级客户信息成功');
    }

    // 获取对应账号下的客户列表(不层级显示,分页查询)
    public function getChildrenByAccount($id)
    {
        $account = (new Account())->getClient($id);
        $client_id = $account['with_client']['client_id'];


        $client = new Client();
        $pageSize = request()->query('size'); // 每页显示的孩子数量
        $keyword = request()->query('keyword'); // 要搜索的关键字


        $data = $client->getAllChildrenWithOverview($client_id, $pageSize, $keyword);
        return api($data, 200, '获取客户列表成功');
    }

    // 根据登录账号获取对应的客户
    public function getMainClientByAccount($id)
    {
        $res = Client_account_relationship::where('account_id', $id)->first();
        $data = Client::select(['id', 'city', 'province', 'district', 'clientname', 'info', 'pid'])->findOrFail($res->client_id);
        return api($data, 200, '获取账号对应的客户成功');
    }

    // 客户详细信息
    public function show($id)
    {
        $data = Client::findOrFail($id);
        return api($data, 200, '获取客户信息成功');
    }

    // 添加客户
    public function store(Request $request)
    {
        try {
            $validator = \Validator::make($request->all(), [
                'pid'        => 'required|integer',
                'type'       => 'required|integer|min:0|max:1',
                'clientname' => 'required|string|max:255',
                'province'   => 'required|string',
                'city'       => 'required|string',
                'district'   => 'required|string',
                'info'       => 'nullable|string',
                'total_air'  => 'nullable|integer',
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors()->toArray();
                $errorString = implode(',', \Arr::flatten($errors));
                return api($errors, 400, $errorString);
            }

            $data = $validator->validate();
            if ($data['type'] === 0) {
                unset($data['total_air']);
            }
            $data['info'] = $data['info'] ?? '这家客户很懒~没有写任何简介~';
            $res = Client::create($data);
            Client_overview::create(['client_id' => $res->id]);
            return api($res, 201, '添加客户成功');
        } catch (\Exception $e) {
            return api(null, 500, $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = \Validator::make($request->all(), [
                'type'       => 'integer|min:0|max:1|nullable',
                'clientname' => 'required|string|max:255',
                'province'   => 'nullable|string',
                'city'       => 'nullable|string',
                'district'   => 'nullable|string',
                'info'       => 'nullable|string',
                'pid'        => 'required|integer',
                'total_air'  => 'nullable|integer'
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors()->toArray();
                $errorString = implode(',', \Arr::flatten($errors));
                return api($errors, 400, $errorString);
            }
            $data = $validator->validate();
            $res = Client::findOrFail($id)->update($data);
            return api($res, 201, '编辑客户成功');
        } catch (\Exception $e) {
            return api(null, 500, $e->getMessage());
        }
    }

    // 删除客户
    public function destroy($id)
    {
        try {
            $client = Client::with(['childs'])->findOrFail($id);
            // 不能删除有子客户的单位
            if ($client->childs->count() > 0) {
                return api(null, 400, '该单位有子客户，不能删除');
            }
            // 删除客户概览
            Client_overview::where('client_id', $client->id)->forceDelete();
            // 删除客户对应的所有账号,还要删对应关系,还要删除所有空调记录
            $data = (new Client_account_relationship())->getAccountList($id);
            foreach ($data as $item) {
                // 找到账号
                $account = Account::findOrFail($item['account_id']);
                // 删账号对应关系
                Client_account_relationship::where('account_id', $account->id)->forceDelete();
                // 删账号
                $account->forceDelete();
            }
            // 删空调
            Air_detail::where('client_id', $id)->forceDelete();
            // 删除客户
            $client->forceDelete();
            return api(null, 204, '删除客户成功');
        } catch (\Exception $e) {
            return api(null, 500, $e->getMessage());
        }
    }

    // 获取系统信息
    public function getSystemClient()
    {
        $data = Client::where('pid', 0)->select(['id', 'city', 'province', 'district', 'clientname', 'info'])->first();
        return api($data, 200, '获取账号对应的客户成功');
    }

    // 给前端地图数据的接口 返回账号下的所有type为1的客户不要层级显示
    public function getMapData($id, Request $request)
    {
        $client_id = Client_account_relationship::where('account_id', $id)->first(['client_id'])->client_id;
        $clientModel = new \App\Models\Client\Client();
        $data = $clientModel->getAllDescendants($client_id);

        if ($request->query('highlight') == 'true') {
            // 获取所有省、市、区字段并去重
            $locations = $data->flatMap(function ($client) {
                return [
                    str_replace('省', '', $client->province),
                    $client->city,
                    $client->district,
                ];
            })->unique()->filter()->values()->all();
            return api($locations, 200, 'haha');
        }

        return api($data, 200, '获取账号对应的客户成功');
    }

    public function searchByDistrict($id, Request $request)
    {
        $client_id = Client_account_relationship::where('account_id', $id)->first(['client_id'])->client_id;
        $district = $request->query('district');

        if (!$client_id || !$district) {
            return api(null, 400, '参数错误');
        }

        // 获取指定客户及其所有子客户
        $clientModel = new Client();
        $allDescendants = $clientModel->getAllDescendants($client_id);

        // 过滤指定区名且 type 为 1 的客户
        $filteredClients = $allDescendants->filter(function ($client) use ($district) {
            return $client->district == $district && $client->type == 1;
        })->values();

        return api($filteredClients, 200, '获取指定区名且 type 为 1 的客户成功');
    }
}

