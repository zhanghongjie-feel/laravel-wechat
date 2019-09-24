<?php

namespace App\Http\Controllers\test;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use App\Tools\Tools;
class SignInController extends Controller
{
    public function __construct(Tools $tools)
    {
        $this->tools=$tools;
    }
    /**
     * 自定义菜单（最基础原始的发送）
     */
    public function menu(Request $request){
        $url='https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$this->tools->get_access_token();
        $data=[
            'button'=>[
                [
                    'type'=>'click',
                    'name'=>'积分查询',
                    'key'=>'duduo'
                ],

                [
                    'type'=>'click',
                    'name'=>'签到',
                    'key'=>'dudu'
                ],

            ],
        ];
        //dd(json_encode($data));

        $res=$this->tools->curl_post($url,json_encode($data,JSON_UNESCAPED_UNICODE));
//        dd($res);
        $result=json_decode($res,1);
        dd($result);
    }
}
