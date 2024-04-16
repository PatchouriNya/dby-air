<?php

namespace App\Http\Controllers\Menu;

use App\Http\Controllers\Controller;
use App\Models\Menu\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    // 获取全部菜单
    public function getMenu()
    {
        $data = Menu::with('children')->where('pid', 0)->orderBy('sort', 'asc')->get()->toArray();
//        $data = Menu::with('children')->where('pid', 0)->ordered()->get()->toArray();
        return api($data,200,'获取菜单信息成功');
    }

    public function getMenuRoute(Request $request)
    {
        $route =  $request->get('route');
        $res = Menu::getAncestorsByUrl($route);
        $data = '';
        if ($res) {
            foreach ($res as $ancestor) {
                $data .= $ancestor->name . '-';

            }
        }
        $data = rtrim($data, '-');
        return api($data,200,'获取当前路由地址成功');
    }
}
