<?php

namespace App\Http\Controllers\Question;

use App\Http\Controllers\Controller;
use App\Models\Question\Question;
use Illuminate\Http\Request;
use Validator;

class QuestionController extends Controller
{
    //
    public function index(Request $request)
    {
        $menu_id = $request->query('menu_id');
        $data = Question::where('menu_id', $menu_id)->orderBy('sort', 'asc')->get();
        return api($data, 200, '获取FAQ列表成功');
    }

    public function update($id, Request $request)
    {
        $validator = validator::make($request->all(), [
            'question' => 'nullable|string',
            'answer'   => 'nullable|string',
            'sort'     => 'nullable|integer',
        ]);
        if ($validator->fails()) {
            return api(null, 400, $validator->errors()->first());
        }
        $data = $validator->validate();
        $res = Question::where('id', $id)->update($data);
        if ($res) {
            return api(null, 201, '修改成功');
        } else {
            return api(null, 400, '修改失败');
        }
    }

    public function store(Request $request)
    {
        $validator = validator::make($request->all(), [
            'menu_id'  => 'required|integer',
            'question' => 'required|string',
            'answer'   => 'required|string',
            'sort'     => 'required|integer',
        ]);
        if ($validator->fails()) {
            return api(null, 400, $validator->errors()->first());
        }
        $data = $validator->validate();
        $res = Question::create($data);
        if ($res) {
            return api(null, 201, '创建成功');
        } else {
            return api(null, 400, '创建失败');
        }
    }

    public function destroy($id)
    {
        $res = Question::where('id', $id)->delete();
        if ($res) {
            return api(null, 204, '删除成功');
        }
        return api(null, 400, '删除失败');
    }
}
