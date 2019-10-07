<?php

namespace App\Http\Controllers\Admin;
use App\Tools\Tools;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
class AdminController extends Controller
{
    public function __construct(Tools $tools)
    {
        $this->tools=$tools;
    }

    public function index(){
        return view('Admin.index');
    }

    public function bangding(){
        return view('Admin.bangding');
    }
    public function do_bangding(Request $request){
        $data=$request->all();
        $openid='oJMd0weUXJppG4bt4GaqSKRw9Ct4';
//        dd($data['tel']);
        DB::connection('wechat')->table('user_info')->where(['openid'=>$openid])->update([
           'tel'=>$data['tel']
        ]);
    }

    public function send_code(Request $request){
        $data=$request->all();
//        dd($data);
        $info=DB::connection('wechat')->table('user_info')->first();
        $openid=$info->openid;

    }


    public function send_template_message(){
        $openid='oJMd0weUXJppG4bt4GaqSKRw9Ct4';
        $url='https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$this->tools->get_access_token();
        $code=rand(1000,9999);
        $data=[
            'touser'=>$openid,
            'template_id'=>'IKESIfR0TJiRHHkgBgCzFPwB3iL3S5EYOe0lAW3s3Dw',
            'url'=>'www.laravel.com',
            'data'=>[
                'first'=>[
                    'value'=>'二维码',
                    'color'=>''
                ],
                'keyword1'=>[
                    'value'=>$code
                ],

            ]
        ];
        $re=$this->tools->curl_post($url,json_encode($data,JSON_UNESCAPED_UNICODE));
        $result=json_decode($re,1);
        dd($result);
    }
}
