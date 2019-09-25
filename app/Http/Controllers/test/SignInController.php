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


    public function send_message(){
        $u_info=DB::connection('test')->table('user_info')->get()->toArray();
        $today=date('Y-m-d',time());
//        dd($today);
        foreach ($u_info as $k=>$v){
//            print_r($v);
           if($today!==$u_info[$k]->signin){
               $url='https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$this->tools->get_access_token();
               $data=[
                   'touser'=>$u_info[$k]->openid,
                   'template_id'=>'gDsIyl1h_elVHIzk_V2txsZhno_jspfhZwISvAbukEY',
                   'url'=>'www.laravel.com',
                   'data'=>[
                       'first'=>[
                           'value'=>'签到提醒',
                           'color'=>''
                       ],
                       'keyword1'=>[
                           'value'=>'今日未签到'
                       ],
                       'keyword2'=>[
                           'value'=>'积分是'.$u_info[$k]->score
                       ],
                       'remark'=>[
                           'value'=>'祝你快乐每一天',
                           'color'=>''
                       ]
                   ]
               ];
               $re=$this->tools->curl_post($url,json_encode($data,JSON_UNESCAPED_UNICODE));
           }elseif($today!==$u_info[$k]['signin']){
               $url='https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$this->tools->get_access_token();
               $data=[
                   'touser'=>$u_info[$k]->openid,
                   'template_id'=>'gDsIyl1h_elVHIzk_V2txsZhno_jspfhZwISvAbukEY',
                   'url'=>'www.laravel.com',
                   'data'=>[
                       'first'=>[
                           'value'=>'签到提醒',
                           'color'=>''
                       ],
                       'keyword1'=>[
                           'value'=>'今日已经签到'
                       ],
                       'keyword2'=>[
                           'value'=>'积分是'.$u_info[$k]->score
                       ],
                       'remark'=>[
                           'value'=>'假期快乐',
                           'color'=>''
                       ]
                   ]
               ];
               $re=$this->tools->curl_post($url,json_encode($data,JSON_UNESCAPED_UNICODE));
           }
        }

//        $sign_num=$u_info->sign_num;
//        $score=$u_info->score;
        $result=json_decode($re,1);
        dd($result);

    }



}
