<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Tools\Tools;
class ExamController extends Controller
{

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * 咱做的点击按钮，是不是相当于正式号用户点击同意授权(code->$result)
     *
     */

    public function __construct(Tools $tools)
    {
        $this->tools=$tools;
    }

    public function login(){
        return view('login');
    }
    public function wechat_login(){
        $redirect_uri='http://www.laravel.com/exam/code';
        //用户同意授权，获取code
        $url='https://open.weixin.qq.com/connect/oauth2/authorize?appid='.env('WECHAT_APPID').'&redirect_uri='.urlencode($redirect_uri).'&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect';
        header('Location:'.$url);
    }
    /***
     * 接受code
     *
     */
    public function code(Request $request){
        $req=$request->all();
//        dd($req['code']);
        //通过code换取网页授权access_token和openid
        $result=file_get_contents('https://api.weixin.qq.com/sns/oauth2/access_token?appid='.env('WECHAT_APPID').'&secret='.env('WECHAT_APPSECRET').'&code='.$req['code'].'&grant_type=authorization_code');
        $re=json_decode($result,1);
//        dd($re);
        //拉取用户信息
        $user_info=file_get_contents('https://api.weixin.qq.com/sns/userinfo?access_token='.$re['access_token'].'&openid='.$re['openid'].'&lang=zh_CN');
        $wechat_user_info=json_decode($user_info,1);
//            dd($wechat_user_info);
        $openid=$re['openid'];
        $wechat_info=DB::connection('wechat')->table('user_wechat')->where(['openid'=>$openid])->first();
//        dd($wechat_info);
        if(!empty($wechat_info)){
            //存在，登录
            $request->session()->put('uid',$wechat_info->uid);
            return redirect('exam/liuyan');
//            echo '欧了';
        }else{
            //先注册，后登录
            DB::connection('wechat')->beginTransaction();//打开事务
            $uid=DB::connection('wechat')->table('user_info')->insertGetId([
                'name'=>$wechat_user_info['nickname'],
                'password'=>'',
                'reg_time'=>time()
            ]);
            $insert_result=DB::connection('wechat')->table('user_wechat')->insert([
                'uid'=>$uid,
                'openid'=>$openid
            ]);
            //登录操作
            $request->session()->put('uid',$uid);
            echo '你是先注册了然后登录';
        }
    }

    /**
     * 留言主页
     */
    public function openid_list(Request $request){
        $req=$request->all();
//        dd($req);
        $content=$req['content'];
//        dd($content);
       $openid_list = DB::connection('wechat')->table('user_info')->get();
//        dd($openid_list);
        return view('Exam.openidList',['info'=>$openid_list,'content'=>$content]);
    }
    /**
     * 留言
     */
    public function liuyan(Request $request){
        return view('Exam.liuyan');
    }

    public function do_liuyan(Request $request){
        $req=$request->all();
//        dd($req);
//        dd($req['openid_list']);
        $url='https://api.weixin.qq.com/cgi-bin/message/mass/send?access_token='.$this->tools->get_access_token();
        $data=[
            'touser'=>$req['openid_list'],
            'msgtype'=>'text',
            'text'=>[
                'content'=>$req['content']
            ],
        ];
        $re=$this->tools->curl_post($url,json_encode($data,JSON_UNESCAPED_UNICODE));
        $res=json_decode($re,1);
        dd($res);
    }
}
