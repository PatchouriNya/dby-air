<?php

namespace App\Http\Controllers\Log;

use App\Http\Controllers\Controller;
use App\Models\Account\Account;
use App\Models\Client\Client;
use App\Models\Client\Client_account_relationship;
use App\Models\Log\Log;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;

class LogController extends Controller
{
   public function index()
    {
        // 登录账号id
        $id = \request()->query('id');

       if ( Account::findOrFail($id)->main === 1){
           // 获取登录账号对应的客户信息
           $data = (new Account())->getClient($id);

           // 获取客户id
           $client_id = $data['with_client']['client_id'];

           // 获取客户下的所有账号信息
           $client = (new Client());
           $data = $client->getOverview($client_id);

           // 定义一个空数组来存储所有 id
           $allIds = [];

           // 递归函数，遍历对象并将 id 存入数组
           function extractIds($object, &$allIds) {
               // 将当前对象的 id 存入数组
               $allIds[] = $object['id'];

               // 如果当前对象有子对象（childs），则继续递归处理子对象
               if (isset($object['childs']) && is_array($object['childs'])) {
                   foreach ($object['childs'] as $child) {
                       extractIds($child, $allIds);
                   }
               }
           }

// 调用递归函数，遍历对象并将 id 存入数组
           extractIds($data, $allIds);

           // 构建查询条件
           $query = Log::whereIn('client_id', $allIds);
       }
       else{
           $query = Log::where('account_id', $id);
       }



        // 获取前端传递过来的筛选条件
        $filters = request()->only(['client', 'account', 'content', 'ip']);


       // 根据筛选条件过滤数据
    foreach ($filters as $column => $value) {
        if (!empty($value)) {
            $query->where($column, 'like', '%' . $value . '%');
        }
    }

        // 处理日期
       $startDate = date(\request()->query('start_date'));
       $endDate = date(\request()->query('end_date'));
       if ( $endDate == '' && $startDate != ''){
           $query ->where('created_at', '>=', $startDate);
       }
       elseif ($startDate == '' && $endDate != ''){
       $endDate = date('Y-m-d', strtotime($endDate . ' +1 day'));
           $query ->where('created_at', '<=',$endDate);
       }
       elseif ($startDate != '' && $endDate != ''){
           $endDate = date('Y-m-d', strtotime($endDate . ' +1 day'));
           $query ->where('created_at', '>=', $startDate)
               ->where('created_at', '<=',$endDate);
       }

        $pageSize = \request()->query('pageSize') ?? 10;
        $type = \request()->query('type');
        $type = (integer)$type;

        if($type === 1)
        $data = $query->where('type',1)->with(['account_detail:id,nickname,account','client_detail:id,clientname'])->orderBy('created_at','desc')->paginate($pageSize);
        elseif ($type === 2){
            $data = $query->where('type',2)->with(['account_detail:id,nickname,account','client_detail:id,clientname'])->orderBy('created_at','desc')->paginate($pageSize);
        }
        return api($data,200,'获取登录日志列表成功');
    }

    public function store(Request $request)
    {
        $form = $request->all(['id','type','content']);
        $id = $form['id'];
        $ip = $request->ip();
        $type = (integer)$form['type'];
        $content = $form['content'];
        $account = Account::where('id', $id)->first('account')->account;
        $client_id = Client_account_relationship::where('account_id', $id)->first('client_id')->client_id;
        $clientname = Client::where('id', $client_id)->first('clientname')->clientname;
        if ($type === 1){
            $res = Log::create(['account_id' => $id,'account'=>$account ,'ip' => $ip,'type' => $type, 'content' => $content,'client_id'=>$client_id,'client'=>$clientname]);
            if ($res){
                return api($res,201,'新增日志成功');
            }else{
                return api([],500,'新增日志失败');
            }
        }
        else if ($type === 2){

            $res = Log::create(['account_id' => $id ,'account'=>$account ,'ip' => $ip,'type' => $type, 'content' => $content,'client_id'=>$client_id,'client'=>$clientname]);
            if ($res){
                return api($res,201,'新增日志成功');
            }else{
                return api([],500,'新增日志失败');
            }
        }
        return api([],500,'服务器错误,新增日志失败');
    }

    public function destroy($id){
       $res = Log::where('id',$id)->delete();
       if ($res){
           return api([],200,'删除日志成功');
       }else{
           return api([],500,'删除日志失败');
       }
    }
}
