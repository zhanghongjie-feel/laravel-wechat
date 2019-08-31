<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
class WechatController extends Controller
{
    /**
     * 获取access_token
     * @return bool|string
     */

    public function get_wechat_access_token(){
        return $this->get_access_token();
    }

    /**
     * @return bool|string
     * 获取用户列表
     */
    public function get_user_list(){
        //获取用户openid
        $result=file_get_contents('https://api.weixin.qq.com/cgi-bin/user/get?access_token='.$this->get_wechat_access_token().'&next_openid=');
        $re=json_decode($result,1);
//        dd($re);
        //获取用户信息
        $last_info=[];
        foreach($re['data']['openid'] as $k=>$v){
            $user_info=file_get_contents('https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$this->get_wechat_access_token().'&openid='.$v.'&lang=zh_CN');
            $user=json_decode($user_info,1);
//            dd($user);
            $last_info[$k]['nickname']=$user['nickname'];
            $last_info[$k]['openid']=$v;
            $last_info[$k]['subscribe']=$user['subscribe'];
            $last_info[$k]['city']=$user['city'];
            $last_info[$k]['country']=$user['country'];
            $last_info[$k]['headimgurl']=$user['headimgurl'];
            $last_info[$k]['subscribe_time']=$user['subscribe_time'];
//            dd($user);
        }
//        dd($last_info);
//        $db=DB::connection('wechat')->table('user_info')->insert([
//
//        ]);
//        dd($db);
//        dd($last_info);
//        dd($re['data']['openid']);
        return view('Wechat.userList',['info'=>$last_info]);
    }
    public function get_detailed_info(){
        $data=$this->get_user_list();
//        dd($data);
//        $dat=json_decode($data,1);
//        dd($dat);
        return view('Wechat.userInfo');
    }

    public function get_access_token(){
//        $result=file_get_contents('./web.config');
//        dd($result);

//        dd($re);
        $redis=new \Redis();
        $redis->connect('127.0.0.1','6379');

//        dd($redis);
        //加入缓存
        $access_token_key='wechat_access_token';
        if($redis->exists($access_token_key)){
            //存在
            return $redis->get($access_token_key);
        }else{
            //不存在
            $result=file_get_contents('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.env('WECHAT_APPID').'&secret='.env('WECHAT_APPSECRET'));
//        dd($result);
            $re=json_decode($result,1);
//            dd($re);
            $redis->set($access_token_key,$re['access_token'],$re['expires_in']);//加入缓存
            return $re['access_token'];
        }

    }


}
