<?php
namespace App\Tools;

class Tools{
    public $redis;

    public function __construct(){
        $this->redis=new \Redis();
        $this->redis->connect('127.0.0.1','6379');
    }

    /***
     * curl传输数据
     */
    public function curl_post($url,$data){
        $curl=curl_init($url);//初始化
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);//配置参数
        curl_setopt($curl,CURLOPT_POST,1);
        curl_setopt($curl,CURLOPT_POSTFIELDS,$data);
        $d=curl_exec($curl);//执行
        curl_close($curl);
        return $d;
    }

    /**
     * 获取access_token
     */
    public function get_access_token(){
//        $result=file_get_contents('./web.config');
//        dd($result);

//        dd($re);
        //这是php的redis(放Tools里面了)
//        $redis=new \Redis();
//        $redis->connect('127.0.0.1','6379');

//        dd($redis);
        //加入缓存
        $access_token_key='wechat_access_token';
        if($this->redis->exists($access_token_key)){
            //存在
//            dd($this->redis->get($access_token_key));
            return $this->redis->get($access_token_key);
        }else{
            //不存在
            $result=file_get_contents('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.env('WECHAT_APPID').'&secret='.env('WECHAT_APPSECRET'));
            //        dd($result);
            $re=json_decode($result,1);
            $this->redis->set($access_token_key,$re['access_token'],$re['expires_in']);//加入缓存
            return $re['access_token'];
        }

    }

    /**
     * 微信api_ticket
     */
    public function get_wechat_jsapi_ticket(){
        //加入缓存
        $jsapi_ticket_key='wechat_jsapi_ticket';
        if($this->redis->exists($jsapi_ticket_key)){
            //存在
            return $this->redis->get($jsapi_ticket_key);
        }else{
            //不存在
            $result=file_get_contents('https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token='.$this->get_access_token().'&type=jsapi');
//                    dd($result);
            $re=json_decode($result,1);
            $this->redis->set($jsapi_ticket_key,$re['ticket'],$re['expires_in']);//加入缓存
            return $re['ticket'];
        }

    }
}