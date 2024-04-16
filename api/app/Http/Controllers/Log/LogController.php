<?php

namespace App\Http\Controllers\Log;

use App\Http\Controllers\Controller;
use App\Models\Account\Account;
use App\Models\Client\Client;
use App\Models\Client\Client_account_relationship;
use App\Models\Log\Log;
use Illuminate\Http\Request;

class LogController extends Controller
{
   public function index()
    {
        $data = Log::where('type',1)->with(['account_detail:id,nickname','client_detail:id,clientname'])->orderBy('created_at','desc')->paginate();
        return api($data,200,'获取登录日志列表成功');
    }

   public function store(Request $request)
    {
        $form = $request->all(['id','type','content']);
        $id = $form['id'];
        $ip = $request->ip();
        $account = Account::where('id', $id)->first('account')->account;
        $client_id = Client_account_relationship::where('account_id', $id)->first('client_id')->client_id;
        $type = (integer)$form['type'];
        $content = $form['content'];
        $res = Log::create(['account_id' => $id,'account'=>$account ,'ip' => $ip,'type' => $type, 'content' => $content,'client_id'=>$client_id]);
        if ($res){
            return api($res,201,'新增日志成功');
        }
    }
}
