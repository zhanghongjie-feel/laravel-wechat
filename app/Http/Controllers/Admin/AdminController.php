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

    public function index(){
        return view('Admin.index');
    }

    public function bangding(){
        return view('Admin.bangding');
    }

    public function do_bangding(Request $request){

        $data=$request->all();
            $openid=$this->getOpenid();
            dd($openid);
            DB::connection('wechat')->table('admin')->where(['name'=>$data['name'],'password'=>$data['password']])->update([
               'openid'=>$openid
            ]);

    }


    public function do_code(Request $request){
//        $code=rand(1000,9999);  //8613
//        $this->tools->redis->set('code',$code,180);
//        echo $code;
//        die;
        $data=$request->all();

        $db_info=DB::connection('wechat')->table('user_info')->where(['name'=>$data['name'],'password'=>$data['password']])->first();
        $openid=$db_info['openid'];
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

        $db=$this->tools->redis->get('code');
        if($data==$db){
            echo 'yes';
        }else{
            echo 'fail';
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

    public function test(){

        $openid = Openid::getOpenid();
        var_dump($openid);die;
//        $host = $_SERVER['HTTP_HOST'];  //域名
//        $uri = $_SERVER['REQUEST_URI']; //路由参数
//        $redirect_uri = urlencode($host.$uri);
//
//        dd($redirect_uri);
    }
}
