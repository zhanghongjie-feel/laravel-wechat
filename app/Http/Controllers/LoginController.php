<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
class LoginController extends Controller
{
    public function login(){
        return view('Wechat.login');
    }

    /***
     * 微信登录
     */
    public function wechat_login(){
        $redirect_uri='http://www.laravel.com/wechat/code';
        //引导关注着打开这个页面(是否同意授权)
        $url='https://open.weixin.qq.com/connect/oauth2/authorize?appid='.env('WECHAT_APPID').'&redirect_uri='.urlencode($redirect_uri).'&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect';
        header('Location:'.$url);
    }
    /***
     * 接受code11
     *
     */
    public function code(Request $request){
        $req=$request->all();
//        dd($req['code']);//出来code
        //通过code换取网页授权access_token和openid,userinfo也可以获取openid,snsapi_base不出来"近期已经授权过，自动登录中"
        $result=file_get_contents('https://api.weixin.qq.com/sns/oauth2/access_token?appid='.env('WECHAT_APPID').'&secret='.env('WECHAT_APPSECRET').'&code='.$req['code'].'&grant_type=authorization_code');
        $re=json_decode($result,1);
        dd($re);
        //拉取用户信息（通过access_token和openid）
            $user_info=file_get_contents('https://api.weixin.qq.com/sns/userinfo?access_token='.$re['access_token'].'&openid='.$re['openid'].'&lang=zh_CN');
            $wechat_user_info=json_decode($user_info,1);
//            dd($wechat_user_info);
        $openid=$re['openid'];
        $wechat_info=DB::connection('wechat')->table('user_wechat')->where(['openid'=>$openid])->first();
//        dd($wechat_info);
        if(!empty($wechat_info)){
            //存在，登录
            $request->session()->put('uid',$wechat_info->uid);

            echo '欧了';
        }else{
            //先注册，后登录
            DB::connection('wechat')->beginTransaction();//打开事务
            $uid=DB::connection('wechat')->table('user_info')->insertGetId([
                'name'=>$wechat_user_info['nickname'],
               'password'=>'',
                'reg_time'=>time()
            ]);
            $insert_result=DB::connection('wechat')->table('user_wechat')->insert([
               'uid'=>$uid,
                'openid'=>$openid
            ]);
            //登录操作
            $request->session()->put('uid',$uid);
            echo '你是先注册了然后登录';
        }
    }
}
