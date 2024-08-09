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

/*Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::group(['prefix' => 'dby', 'namespace' => 'App\Http\Controllers','middleware' => ['cors']], function () {
    Route::get('/command-liuhe/{str}', 'DataController@sendCommand');

    // 传客户id 读取空调
    Route::get('/air-latest/{id}', 'DataController@getLatestAirData');

    // 传客户id 控制一台空调
    Route::post('/air-control/{id}', 'DataController@controlOneAir');

    // 传组id 控制组内空调
    Route::post('/air-control-group/{id}', 'DataController@controlAirGroup');

    // 读一台空调
    Route::get('/air-read/{client_id}/{air_id}', 'DataController@readOneAir');

    Route::get('/air-test', 'DataController@test11');

});
