<?php

namespace App\Http\Controllers\test;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use App\Tools\Tools;
class TestController extends Controller
{
    public function __construct(Tools $tools)
    {
        $this->tools=$tools;
    }

    public function login(){
        return view('Test.login');
    }

    /***
     * 微信登录
     */
    public function wechat_login(){
        $redirect_uri=env('APP_URL').'/test/code';
        //引导关注着打开这个页面(是否同意授权)
        $url='https://open.weixin.qq.com/connect/oauth2/authorize?appid='.env('WECHAT_APPID').'&redirect_uri='.urlencode($redirect_uri).'&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect';
        header('Location:'.$url);
    }
    /***
     * 接受code11
     *
     */
    public function code(Request $request){
        $req=$request->all();
//        dd($req['code']);//出来code
        //通过code换取网页授权access_token和openid,userinfo也可以获取openid,snsapi_base不出来"近期已经授权过，自动登录中"
        $result=file_get_contents('https://api.weixin.qq.com/sns/oauth2/access_token?appid='.env('WECHAT_APPID').'&secret='.env('WECHAT_APPSECRET').'&code='.$req['code'].'&grant_type=authorization_code');
        $re=json_decode($result,1);
//        dd($re);
        //拉取用户信息（通过access_token和openid）
        $user_info=file_get_contents('https://api.weixin.qq.com/sns/userinfo?access_token='.$re['access_token'].'&openid='.$re['openid'].'&lang=zh_CN');
        $wechat_user_info=json_decode($user_info,1);
//            dd($wechat_user_info);
        $openid=$re['openid'];
        $wechat_info=DB::connection('wechat')->table('user_wechat')->where(['openid'=>$openid])->first();
//        dd($wechat_info);
        if(!empty($wechat_info)){
            //存在，登录
            $request->session()->put('uid',$wechat_info->uid);

            echo '欧了';
            return redirect('test/taglist');
        }else{
            //先注册，后登录
            DB::connection('wechat')->beginTransaction();//打开事务
            $uid=DB::connection('wechat')->table('user_info')->insertGetId([
                'name'=>$wechat_user_info['nickname'],
                'password'=>'',
                'reg_time'=>time()
            ]);
//            dd($uid);
            $insert_result=DB::connection('wechat')->table('user_wechat')->insert([
                'uid'=>$uid,
                'openid'=>$openid
            ]);
            //登录操作
            $request->session()->put('uid',$uid);
            echo '你是先注册了然后登录';
        }
    }

    public function tagList(){
        $url='https://api.weixin.qq.com/cgi-bin/tags/get?access_token='.$this->tools->get_access_token();
        $re=file_get_contents($url);
        $result=json_decode($re,1);
        return view('Test.tagList',['info'=>$result]);
    }

    public function add_tag(){
        return view('Test.addTag');
    }

    public function do_add_tag(Request $request){
        $req=$request->all();
//        dd($req);
        $data= [
            'tag' => [
                'name'=>$req['tag_name']
            ]
        ];
        $url='https://api.weixin.qq.com/cgi-bin/tags/create?access_token='.$this->tools->get_access_token();
        $re=$this->tools->curl_post($url,json_encode($data,JSON_UNESCAPED_UNICODE));
        $result=json_decode($re,1);
//        dd($result);
        if($result){
            return redirect('test/taglist');
        }
    }

    public function get_user_list(Request $request){
        $req=$request->all();
        $user_info=DB::connection('wechat')->table('user_info')->get();
//        dd($user_info);
        return view('Wechat.userList',['info'=>$user_info,'tagid'=>$req['tagid']]);
    }

    public function add_tag_user(Request $request){
        $req=$request->all();
        $url='https://api.weixin.qq.com/cgi-bin/tags/members/batchtagging?access_token='.$this->tools->get_access_token();
        $data=[
            'openid_list'=>$req['openid_list'],
            'tagid'=>$req['tagid']
        ];
        $re=$this->tools->curl_post($url,json_encode($data));
        $result=json_decode($re,1);
//        dd($result);
        if($result['errcode']==0){
            return redirect('test/taglist');
        }else{
            echo 'fuck';
        }
    }
    public function push_tag_message(Request $request){
        $req=$request->all();
//        dd($req);
        return view('test.pushTagMsg',['tagid'=>$request->all()['tagid']]);
    }

    public function do_push_tag_message(Request $request){
        $req=$request->all();
        $message=$req['message'];
        $re=$this->tools->redis->set('message',$message);
        $url='https://api.weixin.qq.com/cgi-bin/message/mass/sendall?access_token='.$this->tools->get_access_token();
        $data=[
            'filter'=>[
                'is_to_all'=>false,
                'tag_id'=>$req['tagid']
            ],
            'text'=>[
                'content'=>$req['message']
            ],
            "msgtype"=>"text"
        ];
        $re=$this->tools->curl_post($url,json_encode($data,JSON_UNESCAPED_UNICODE));
//        dd($re);
        $result=json_decode($re,1);
        dd($result);
    }
}
