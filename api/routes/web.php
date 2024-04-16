<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('user',function (){
    return 123;
});





/*Route::get('test/{id?}',function (int $id = 0)
{
    dump(sha1(md5(123456)));
});

Route::post('/', function () {
    return 'post';
});*/
