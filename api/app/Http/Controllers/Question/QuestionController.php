<?php

namespace App\Http\Controllers\Question;

use App\Http\Controllers\Controller;
use App\Models\Question\Question;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    //
    public function index(Request $request)
    {
        $menu_id = $request->query('menu_id');
        $data = Question::where('menu_id', $menu_id)->orderBy('sort', 'asc')->get();
        return api($data, 200, '获取FAQ列表成功');
    }
}
