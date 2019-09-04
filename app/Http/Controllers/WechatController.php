<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
class WechatController extends Controller
{
    public function post_test(){
        dd($_POST);
    }
    /*
     * 上传
     */
    public function upload(){
        return view('Wechat.upload');
    }
    public function do_upload(Request $request){
        $name='image';
//        dd($request->hasFile($name));//这个是false
//        dd($request->file($name));//如果没有返回null
//        dd($request->file($name)->isValid());//这个如果没有文件就会报错
        if(!empty($request->hasFile($name)) && request()->file($name)->isValid()){
            $path=request()->file($name)->store('goods');
            dd('/storage/'.$path);
        }else{
            echo '嘟嘟';
        }
    }
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
//        //获取用户openid
//        $openid="oJMd0wZtgTLURJ3hS1OaiZTd_ZvE";
//        //获取用户信息
//            $user_info=file_get_contents('https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$this->get_wechat_access_token().'&openid='.$openid.'&lang=zh_CN');
//        $user=json_decode($user_info,1);
//        $db=DB::connection('wechat')->table('user_info')->insert([
//            'openid'=>$user['openid'],
//            'nickname'=>$user['nickname'],
//            'subscribe'=>$user['subscribe'],
//            'city'=>$user['city'],
//            'country'=>$user['country'],
//            'headimgurl'=>$user['headimgurl'],
//            'subscribe_time'=>$user['subscribe_time']
//        ]);
//        die();
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

//        dd($db);
//        dd($last_info);
//        dd($re['data']['openid']);
        return view('Wechat.userList',['info'=>$last_info]);
    }
    public function get_detailed_info(){
        $data=$this->get_user_list();
//        $data=json_decode($this->get_user_list(),1);
        dd($data);
//        $dat=json_decode($data,1);
//        dd($dat);
        return view('Wechat.userInfo');
    }

    public function get_access_token(){
//        $result=file_get_contents('./web.config');
//        dd($result);

//        dd($re);
        //这是php的redis
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
