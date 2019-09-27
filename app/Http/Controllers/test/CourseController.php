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
//    dd($request->session()->all());
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

    public function send(){
        $tools=new Tools();
        $openid=file_get_contents('https://api.weixin.qq.com/cgi-bin/user/get?access_token='.$tools->get_access_token().'&next_openid=');
        $res=json_decode($openid,1);
        $openid_list=$res['data']['openid'];
//        dd($openid_list);
        foreach($openid_list as $k=>$v){
            $url='https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$this->tools->get_access_token();
            $data=[
                'touser'=>$v,
                'template_id'=>'gDsIyl1h_elVHIzk_V2txsZhno_jspfhZwISvAbukEY',
                'url'=>'wechat.distantplace.vip',
                'data'=>[
                    'first'=>[
                        'value'=>'我丢',
                        'color'=>''
                    ],
                    'keyword1'=>[
                        'value'=>'没事'
                    ],
                    'keyword2'=>[
                        'value'=>'我就发个玩玩'
                    ],
                    'remark'=>[
                        'value'=>'喝喽，艾瑞巴蒂',
                        'color'=>''
                    ]
                ]
            ];
            $re=$this->tools->curl_post($url,json_encode($data,JSON_UNESCAPED_UNICODE));
        }

        $result=json_decode($re,1);
        dd($result);
    }

            public function ol(){
                $tools=new Tools();
                $openid=file_get_contents('https://api.weixin.qq.com/cgi-bin/user/get?access_token='.$tools->get_access_token().'&next_openid=');
                $res=json_decode($openid,1);
                $openid_list=$res['data']['openid'];
                $url='https://api.weixin.qq.com/cgi-bin/message/mass/send?access_token='.$tools->get_access_token();
//        dd($message);
                $data=[
                    'touser'=>$openid_list,
                    'msgtype'=>'text',
                    'text'=>[
                        'content'=>'嘟嘟！'
                    ],
//            'clientmsgid'=>'send_tag_100'
                ];
//        dd($data);
                $res=$tools->curl_post($url,json_encode($data,JSON_UNESCAPED_UNICODE));
                $result=json_decode($res,1);
                dd($result);

            }

}
