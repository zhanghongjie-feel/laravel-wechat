<?php

namespace App\Http\Controllers\Secret;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Tools\Tools;

class UserController extends Controller
{
    public $tools;
    public function  __construct(Tools $tools){
        $this->tools=$tools;
    }
    public function userList(){
        //拿到其中openid
        $url=file_get_contents('https://api.weixin.qq.com/cgi-bin/user/get?access_token='.$this->tools->get_access_token().'&next_openid=');
        $res=json_decode($url,1);
//        dd($res);
        $user_info_list=[];
        foreach($res['data']['openid'] as $k=>$v){
            $user_info=file_get_contents('https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$this->tools->get_access_token().'&openid='.$v.'&lang=zh_CN');
            $user=json_decode($user_info,1);

            $user_info_list[$k]['nickname']=$user['nickname'];
            $user_info_list[$k]['openid']=$user['openid'];
            $user_info_list[$k]['headimgurl']=$user['headimgurl'];

        }
        dd($user_info_list);
    }
}
