<?php
Route::get('/php', 'testController@a');
Route::get('wechat/get_access_token', 'WechatController@get_access_token');//获取access_token
Route::get('wechat/get_user_list', 'WechatController@get_user_list');
Route::get('wechat/get_detailed_info', 'WechatController@get_detailed_info');
Route::get('wechat/login', 'LoginController@login');//登录页面
Route::get('wechat/wechat_login', 'LoginController@wechat_login');//登录
Route::get('wechat/code', 'LoginController@code');//登录
///////////////////////////////////////上传系列
Route::get('wechat/upload','WechatController@upload');//上传
Route::post('wechat/do_upload','WechatController@do_upload');//上传

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
