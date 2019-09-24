<?php
Route::get('send','WechatController@send');
Route::get('/php', 'testController@a');
Route::get('/switch', 'testController@switch');
Route::get('/while', 'testController@while');
//Route::get('wechat/get_access_token', 'WechatController@get_access_token');//获取access_token
Route::get('wechat/get_user_list', 'WechatController@get_user_list');
Route::get('wechat/get_detailed_info', 'WechatController@get_detailed_info');
Route::get('wechat/login', 'LoginController@login');//登录页面
Route::get('wechat/wechat_login', 'LoginController@wechat_login');//登录
Route::get('wechat/code', 'LoginController@code');//登录
///////////////////////////////////////上传系列
Route::get('wechat/upload','WechatController@upload');//上传
Route::post('wechat/do_upload','WechatController@do_upload');//上传
//线上
Route::get('wechat/upload_wechat','WechatController@upload_wechat');//上传
Route::post('wechat/do_upload_wechat','WechatController@do_upload_wechat');//上传
Route::get('wechat/source','WechatController@wechat_source');
//////////api次数清0
Route::get('wechat/clear_api','WechatController@clear_api');
                                        //CURL
Route::post('post_test','WechatController@post_test');
Route::get('download_source','WechatController@download_source');//下载素材资源
Route::get('del_wechat_source','WechatController@del_wechat_source');//删除上传素材
///////////////////////////////////////////标签/////////////////////
Route::get('tag_list','TagController@tag_list');
Route::get('add_tag','TagController@add_tag');
Route::post('do_add_tag','TagController@do_add_tag');
Route::get('delete_tag','TagController@delete_tag');
Route::get('update_tag','TagController@update_tag');
Route::post('do_update_tag','TagController@do_update_tag');
Route::get('wechat/tag_openid_list','TagController@tag_openid_list');//标签下的openid列表
Route::post('wechat/add_tag_openid','TagController@add_tag_openid');//为用户打上标签
Route::get('wechat/push_tag_message','TagController@push_tag_message');//推送
Route::post('wechat/do_push_tag_message','TagController@do_push_tag_message');//推送操作
Route::get('get_openid','TagController@get_openid');//获取这个openid存在于那些标签
Route::get('cancel_tag','TagController@cancel_tag');//把一个标签下粉丝一次性取消好几个
////////////////////////////////////模板消息        //////////////////////////////
Route::get('send_template_message','WechatController@send_template_message');

/////////////////Exam
Route::get('exam/login','ExamController@login');
Route::get('exam/wechat_login','ExamController@wechat_login');
Route::get('exam/code', 'ExamController@code');//登录

//////////////////////////////////////////////////生成专属二维码
Route::get('agent/user_list','AgentController@agent_list');
Route::get('agent/create_qrcode','AgentController@create_qrcode');//创建二维码
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
    $yes=strtotime('-1 days');
    echo date('Y-m-d H:i:s',$yes);
//    die();
    return view('welcome');
});
/////////exam->login
Route::group(['middleware' => ['login']], function () {
    Route::post('exam/openid_list','ExamController@openid_list');
    Route::get('exam/liuyan','ExamController@liuyan');
    Route::post('exam/do_liuyan','ExamController@do_liuyan');

});

///////////////////////自定义菜单
Route::get('menu','WechatController@menu');
Route::get('menu_list','WechatController@menuList');
Route::post('menu/create','WechatController@create_menu');
Route::get('menu/load','WechatController@load_menu');
Route::get('menu/del','WechatController@menu_del');



//////////////////////////jssdk
Route::get('location','WechatController@location');















/////////////////////////////////////////////Secret
Route::get('secret/user_list','Secret\UserController@userList');















//////////////////////////////////////////////TEST
Route::get('test/login','test\TestController@login');
Route::get('test/wechat_login','test\TestController@wechat_login');
Route::get('test/code','test\TestController@code');
Route::get('test/taglist','test\TestController@tagList');
Route::get('test/add_tag','test\TestController@add_tag');
Route::post('test/do_add_tag','test\TestController@do_add_tag');
Route::get('test/get_user_list','test\TestController@get_user_list');
Route::post('test/add_tag_user','test\TestController@add_tag_user');
Route::get('test/push_tag_message','test\TestController@push_tag_message');
Route::post('test/do_push_tag_message','test\TestController@do_push_tag_message');

Route::get('test/menu','test\SignInController@menu');