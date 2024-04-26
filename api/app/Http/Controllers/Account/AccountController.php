<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Account\Account;
use App\Models\Client\Client_account_relationship;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
class AccountController extends Controller
{
    public function changePassword($id,Request $request)
    {
        try {
            // 表单验证
            $validator = \Validator::make($request->all(), [
                'ori_password'  =>  'required|min:6',
                'password'      =>  'required|min:6|confirmed'
            ], [
                'ori_password.required'  =>     '旧密码不能为空',
                'ori_password.min'       =>     '旧密码长度至少为6',
                'password.required'      =>     '密码不能为空',
                'password.min'           =>     '密码最小长度为6',
                'password.confirmed'     =>     '密码填写不一致'
            ]);

            // 若表单出错则不通过
            if ($validator->fails()) {
                $errors = $validator->errors()->toArray();
                return api($errors, 400, '表单验证不通过');
            }
            // 获取通过验证的参数
            $data = $validator->validate();
            // 查询原密码
            $ori_password = Account::find($id)->password;
            if (makePassword($data['ori_password']) == $ori_password) {
                unset($data['ori_password']);
                $data['password'] = makePassword($data['password']);
                $res = Account::whereId($id)->update($data);
                if ($res === 1)
                    return api(null, 201, '修改密码成功');
                else
                    return api(null, 400, '密码修改失败');
            } else {
                return api(null, 400, '与旧密码不符');
            }
        }catch (\Exception $exception){
            return api(null,500,'修改出错');
        }
    }

    public function changePasswordAdmin($id,Request $request)
    {
        try {
            // 表单验证
            $validator = \Validator::make($request->all(), [
                'password'      =>  'required|min:6|confirmed'
            ]);

            // 若表单出错则不通过
            if ($validator->fails()) {
                $errors = $validator->errors()->toArray();
                return api($errors, 400, '表单验证不通过');
            }
            // 获取通过验证的参数
            $data = $validator->validate();
            // 查询原密码
                $data['password'] = makePassword($data['password']);
                $res = Account::whereId($id)->update($data);
                if ($res === 1)
                    return api(null, 201, '修改密码成功');
                else
                    return api(null, 400, '密码修改失败');
            }catch (\Exception $exception){
                return api(null,500,'修改出错');
            }
        }

    public function getAccountListByClient($id)
    {
        $data = (new Client_account_relationship())->getAccountList($id);
        return api($data,200,'数据请求成功');
    }

    public function show($id)
    {
        return api(Account::find($id)->toArray(),200,'获取当前帐号信息成功');
    }

    public function update($id,Request $request)
    {
        try {
            // 表单验证
            $validator = \Validator::make($request->all(), [
                'nickname'      =>  'required|min:2',
                'email'         =>  'required|email',
                'mobile'        =>  'required|regex:/^1[34578]\d{9}$/'
            ]);

            // 若表单出错则不通过
            if ($validator->fails()) {
                $errors = $validator->errors()->toArray();
                $errorString = implode(', ', Arr::flatten($errors));
                return api($errors, 400, $errorString);
            }
            // 获取通过验证的参数
            $data = $validator->validate();
            $res = Account::whereId($id)->update($data);
            if ($res === 1)
                return api(null, 201, '修改成功');
            else
                return api(null, 400, '修改失败');
        }catch (\Exception $exception){
            return api(null,500,'修改出错');
        }
    }

    public function store(Request $request)
    {
        try {
        // 验证请求数据
        $validator = \Validator::make($request->all(),[
            'client_id' => 'required|integer',
            'account' => 'required|string|unique:accounts,account|min:3',
            'password' => 'required|string|confirmed',
            'nickname' => 'required|min:2',
            'email' => 'required|email',
            'mobile' => 'regex:/^1[34578]\d{9}$/',
        ]);
        // 若表单出错则不通过
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            $errorString = implode(', ', Arr::flatten($errors));
            return api($errors, 400, $errorString);
        }

        $data = $validator->validate();
        $client_id = $data['client_id'];
        $data['account_status'] = false;
        $data['avatar'] = '/uploads/default.jpg';
        $data['password'] = makePassword($data['password']);
        // 默认给没有账号的客户主管权限
            $res = Client_account_relationship::where('client_id', $client_id)->first();
            if (!$res)
                $data['main'] = 1;
        unset($data['client_id']);
        // 创建账号
        $account = Account::create($data);

        // 创建客户和账号关系
         Client_account_relationship::create([
            'client_id' => $client_id,
            'account_id' => $account->id,
        ]);

        return api($data,201,'新增账号成功');
        }catch (\Exception $exception){
            return api(null,500,'服务器出错了');
        }
    }

    public function destroy($id)
    {

        try {
            // 获取要删除的账号
            $account = Account::findOrFail($id);

            // 删除账号与客户关系
            Client_account_relationship::where('account_id', $account->id)->forceDelete();

            // 删除账号
            $account->forceDelete();

            return api(null, 200, '账号删除成功');
        } catch (\Exception $exception) {
            return api(null, 500, '服务器出错了');
        }
    }

    public function setMainAccount($id)
    {
        $res = Client_account_relationship::where('account_id', $id)->first(['client_id'])->toArray();
        $res = Client_account_relationship::where('client_id', $res['client_id'])->where('account_id','!=',$id)->get(['account_id'])->toArray();

        foreach ($res as $value){
           $res =  Account::findOrFail($value['account_id'])->main;
           if ($res === 1){
               Account::where('id',$value['account_id'])->update(['main' => 0]);
               break;
           }
        }
        $account = Account::findOrFail($id);
        $account->main = 1;
        $account->save();
        return api(null, 201, '设为主账号成功!');

    }
}
