<?php

namespace App\Http\Controllers\Login;

use App\Http\Controllers\Controller;
use App\Models\Account\Account;
use App\Models\User\Create_record;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        try {
            // 表单验证
            $validator = \Validator::make($request->all(),[
                'username'  =>  'required|min:3',
                'password'  =>  'required|min:6',
            ],[
                'username.required'     =>  '用户不能为空',
                'username.min'          =>  '用户名长度错误',
                'password.required' =>  '密码不能为空',
                'password.min'      =>  '密码长度错误'
            ]);
            // 若表单未通过
            if($validator->fails()){
                $error = $validator->errors()->toArray();
                return api($error,400,'表单验证未通过');
            }

            // 若验证通过
            $username = $validator->validate()['username'];
            $password = makePassword($validator->validate()['password']);
            $user = Account::where('account',$username)->where('password',$password);
            if(!$user->get()->isEmpty()){
                $id = $user->first(['id']);
                $system_id = (integer)Redis::connection()->get('system_id');
                // 存过期时间
                $key = 'user_id_'.$id->id.'_'.$request->ip();
                Redis::connection()->setex($key,3600,json_encode(['id' => $id->id,'ip' => $request->ip()]));
                if ($id->id === $system_id)
                return api($id,999,'登录成功');
                else
                return api($id,200,'登录成功');
            }
            return api(null,403,'登录失败,用户名或密码错误');
        }catch (\Exception $exception){
            return api(null,500,'登录出错');
        }

    }

    public function check($id)
    {
        $key = 'user_id_'.$id.'_'.\request()->ip();
        $redis = Redis::connection();
        if ($redis->get($key)){
//            $content = json_decode($redis->get($key));
            $time = $redis->ttl($key);
            return api($time,200,$time);
        }
        else
            return api(null,204,'登录已过期');
    }


    public function logout($id){
        $key = 'user_id_'.$id.'_'.\request()->ip();
        $redis = Redis::connection();
        $redis->del($key);
        return api(null,200,'退出成功');
    }

}
