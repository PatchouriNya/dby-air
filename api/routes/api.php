<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


// 客户
Route::group(['prefix' => 'dby', 'namespace' => 'App\Http\Controllers\Client', 'middleware' => ['cors']], function () {
    Route::apiResource('client', 'ClientController');

    // 指定账号的客户列表
    Route::get('client/list/{id}', 'ClientController@getClientByAccount');

    Route::get('client/select/tree/{accountId}/{clientId}', 'ClientController@getClientSelectTree');

    Route::get('client/parent/{id}', 'ClientController@getParent');

    Route::get('client/children/{id}', 'ClientController@getChildrenByAccount');

    Route::get('client/main/{id}', 'ClientController@getMainClientByAccount');

    Route::get('system/client', 'ClientController@getSystemClient');

});

// 账号
Route::group(['prefix' => 'dby', 'namespace' => 'App\Http\Controllers\Account', 'middleware' => ['cors']], function () {
    // 资源路由
    Route::apiresource('account', 'AccountController');

    // 改密码
    Route::put('cgp/{id}', 'AccountController@changePassword');

    // 管理员改密码
    Route::put('cgp/admin/{id}', 'AccountController@changePasswordAdmin');

    // 获取客户的所有账号列表
    Route::get('accountlist/{id}', 'AccountController@getAccountListByClient');

    // 设置为主账号
    Route::put('setmain/{id}', 'AccountController@setMainAccount');

});

// 登录
Route::group(['prefix' => 'dby', 'namespace' => 'App\Http\Controllers\Login', 'middleware' => ['cors']], function () {
    // 登录
    Route::post('login', 'LoginController@login');

    // 检验过期时间
    Route::get('login/check/{id}', 'LoginController@check');

    // 登出
    Route::get('logout/{id}', 'LoginController@logout');
});

// 菜单
Route::group(['prefix' => 'dby', 'namespace' => 'App\Http\Controllers\Menu', 'middleware' => ['cors']], function () {
    // 获取全部菜单
    Route::get('menu', 'MenuController@getMenu');

    Route::get('menu/route', 'MenuController@getMenuRoute');
});

// 空调
Route::group(['prefix' => 'dby', 'namespace' => 'App\Http\Controllers\Air', 'middleware' => ['cors']], function () {
    // 根据当前客户返回列表
    Route::get('airs/{id}', 'AirController@getAirById');

    // 资源路由
    Route::apiResource('air', 'AirController');

    // 分组资源路由
    Route::apiResource('group', 'GroupController');

    // 添加某台空调至分组
    Route::post('group/add/{id}', 'GroupController@addAirToGroup');

    // 将空调从分组移出
    Route::delete('group/remove/{id}', 'GroupController@removeAirFromGroup');

    // 查询指定组的所有空调
    Route::get('group/air/{id}', 'GroupController@getAirByGroup');

    // 获取客户的未分组空调列表
    Route::get('air/ungrouped/{id}', 'AirController@getUnGroupedAirByClient');

    // 获取客户的未分组空调列表,并以数组的形式返回
    Route::get('air/grouped/{id}', 'AirController@getGroupedAirByClient');

    // 设置分组策略
    Route::put('group/strategy/{id}', 'GroupController@setStrategy');
});
// 策略

Route::group(['prefix' => 'dby', 'namespace' => 'App\Http\Controllers\Strategy', 'middleware' => ['cors']], function () {
    // 资源路由
    Route::apiResource('strategy', 'StrategyController');
});


// 日志

Route::group(['prefix' => 'dby', 'namespace' => 'App\Http\Controllers\Log', 'middleware' => ['cors']], function () {
    // 资源路由
    Route::resource('log', 'LogController');

});


