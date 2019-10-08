<?php

namespace App\model;

class Curl
{
    public function curl(){
        $curl = curl_init('http://aaa.liqiaomeng.top/api/post_test');//先初始化一个网址
        //var_dump($curl);
        //curl_setopt($curl,CURLOPT_UPLOAD,0);
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);//CURLOPT_RETURNTRANSFER：返回输出，返回原生的（Raw）输出。
        curl_setopt($curl,CURLOPT_POST,true);//发送POST
        $media=new \CURLFile('资源绝对路径');//returntransfer
        $form_data=[
            'question'=>'curl是个什么玩意',
            'answer'=>'我tm怎么知道'
        ];
        curl_setopt($curl,CURLOPT_POSTFIELDS,$form_data);//全部数据使用HTTP协议中的"POST"操作来发送
        $data = curl_exec($curl);//执行一个cURL会话。
        $errno=curl_errno($curl);//错误码
        $err_msg=curl_error($curl);//错误信息




        //var_dump($errno);
        //var_dump($err_msg);
        print_r($data);//这里面的字符一共多少个
        curl_close($curl);//关闭curl会话
    }
}
