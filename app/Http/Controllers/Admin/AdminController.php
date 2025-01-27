<?php

namespace App\Http\Controllers\Admin;
use App\Tools\Tools;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use App\model\Openid;
class AdminController extends Controller
{
    public function __construct(Tools $tools)
    {

        $this->tools=$tools;
    }
    public function a(){
        session(['open'=>111]);
    }
    public function b(){
        $a=session('open');
        dd($a);
    }
    public function index(){
        $openid=session('openid');
        return view('Admin.index');
    }

    public function bangding(){
        $openid=Openid::getOpenid();
        return view('Admin.bangding');
    }

    public function do_bangding(Request $request){
        $openid=session('openid');
            $data=$request->all();
            $name=$data['name'];
            $password=$data['password'];
            $db=DB::connection('wechat')->table('admin')->where(['name'=>$name,'password'=>$password])->update([
                'openid'=>$openid
            ]);
            dd($db);

    }


    public function do_code(Request $request){
//        $code=rand(1000,9999);  //8613
//        $this->tools->redis->set('code',$code,180);
//        echo $code;
//        die;
        $data=$request->all();

        $db_info=DB::connection('wechat')->table('admin')->where(['name'=>$data['name'],'password'=>$data['password']])->first();
        $openid=$db_info->openid;
//        if(!$db_info->name){
//            $result=DB::connection('wechat')->table('user_info')->where(['openid'=>$openid])->update([
//                'name'=>$data['name'],'password'=>$data['password'],'reg_time'=>time(),'tel'=>$data['tel']
//            ]);
//            if($result){
//                echo json_encode(['status'=>1,'content'=>'添加成功']);
//            }else{
//                echo json_encode(['status'=>0,'content'=>'添加失败']);
//            }
//        }
        $code=rand(1000,9999);
        if($db_info){
                $this->tools->redis->set('code',$code,180);
                $this->send_template_message($code,$openid);
        }else{
            return json_encode(['ret'=>0,'content'=>'数据错误']);
        }

    }

    public function send_code(Request $request){
        $data=$request->all();
        $code=$data['code'];
//        dd($data);
        $db=$this->tools->redis->get('code');
//        dd($db);
        if($code==$db){
            return json_encode(['ret'=>1,'content'=>'登陆成功']);
        }else{
            return json_encode(['ret'=>0,'content'=>'登陆失败']);
        }
    }


    public function send_template_message($code,$openid){
        $oid=$openid;
        $url='https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$this->tools->get_access_token();
        $data=[
            'touser'=>$oid,
            'template_id'=>'YtgWCD6YehDpaF6Bh29JDSc85np73EsQh-Nfy77rpmI',
            'url'=>'www.laravel.com',
            'data'=>[
                'first'=>[
                    'value'=>"二维码",
                    'color'=>''
                ],
                'keyword1'=>[
                    'value'=>$code
                ],
                'keyword2'=>[
                    'value'=>date('Y-m-d H:i:s')
                ],
            ]
        ];
        $re=$this->tools->curl_post($url,json_encode($data,JSON_UNESCAPED_UNICODE));
        $result=json_decode($re,1);
        dd($result);
    }

    //二维码登录
    public function getOpenid(){

        $openid = Openid::getOpenid();
//        dd($openid);
        $id=time().rand(1000,9999); //用户标识
        Cache::put('wechatLogin_'.$id,$openid,100);
        return '扫码成功';
//        $host = $_SERVER['HTTP_HOST'];  //域名
//        $uri = $_SERVER['REQUEST_URI']; //路由参数
//        $redirect_uri = urlencode($host.$uri);
//
//        dd($redirect_uri);
    }


    public function checkWechatLogin(){

    }

}
