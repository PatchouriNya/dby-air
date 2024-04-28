<?php

namespace App\Http\Controllers\Air;

use App\Http\Controllers\Controller;
use App\Models\Client\Air_group;
use Illuminate\Http\Request;
use Nette\Schema\ValidationException;

class GroupController extends Controller
{
    public function index(Request $request){
        $client_id = $request->input('client_id');
        $data = Air_group::where('client_id',$client_id)->get();
        return api($data,200,'获取组列表成功');
    }

    public function store(Request $request)
    {
        try {
            $validator = \Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'info' => 'nullable|string|max:255',
                'client_id' => 'required|integer|exists:clients,id',
            ]);
            if ($validator->fails())
            {
                $errors = $validator->errors()->first();
                return api(null,400,$errors);
            }
            $data = $validator->validate();
            $res = Air_group::create($data);
            return api($res->id,201,'创建组成功');
        }
        catch (\Exception $e) {
            return api(null,500, $e->getMessage());
        }
    }

    public function destroy($id){
        $data = Air_group::where('id',$id)->delete();
        if ($data)
        return api($data,204,'删除组成功');

        return api(null,404,'删除组失败');
    }

    public function update(Request $request,$id){

        try {

            $validator = \Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'info' => 'nullable|string|max:255'
            ]);
            if ($validator->fails())
            {
                $errors = $validator->errors()->first();
                return api(null,400,$errors);
            }
            $data = $validator->validate();
            $res = Air_group::where('id',$id)->update($data);
            if ($res)
                return api($res,200,'更新组成功');
            return api(null,404,'更新组失败');
        }catch (\Exception $e){
            return api(null,500, $e->getMessage());
        }
    }
}
