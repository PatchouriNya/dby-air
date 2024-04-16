<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User\Create_record;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getUserInfo(Request $request,int $id)
    {
//        $data = (new Create_record())->user_info();
        return (new Create_record())->getUserById($id);
    }
}
