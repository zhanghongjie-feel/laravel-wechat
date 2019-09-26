<?php

namespace App\Http\Controllers\test;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Tools\Tools;
use DB;
class CourseController extends Controller
{
    public function __construct(Tools $tools)
    {
        $this->tools=$tools;
    }
    /**
     * 自定义菜单（最基础原始的发送）
     */
    public function menu(){
        $url='https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$this->tools->get_access_token();
        $data=[
            'button'=>[
                [
                    'type'=>'click',
                    'name'=>'查看课程',
                    'key'=>'see'
                ],


                        [
                            'type'=>'view',
                            'name'=>'管理课程',
                            'url'=>'http://wechat.distantplace.vip/wechat/login'
                        ],

                ]

        ];
//        dd(json_encode($data));
//
        $res=$this->tools->curl_post($url,json_encode($data,JSON_UNESCAPED_UNICODE));
        $result=json_decode($res,1);
        dd($result);
    }

    public function admin(Request $request){

        $uid=$request->session()->all()['uid'];
        $info=DB::connection('wechat')->table('user_wechat')->where(['uid'=>$uid])->first();
        return view('Test.admin',['info'=>$info]);
    }

    public function admin_create(Request $request){
        $req=$request->all();
//        dd($request->all());
        $res=DB::connection('wechat')->table('user_wechat')->update([
           'one'=>$req['one'],
            'two'=>$req['two'],
            'three'=>$req['three'],
            'four'=>$req['four']
        ]);
        if($res){
            return redirect('course/admin');
        }
    }

}
