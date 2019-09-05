<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
class WechatController extends Controller
{
    public function post_test(){
        dd($_POST);
    }

    /***
     * @param $url
     * @param $path
     * @return mixed
     * 这是使用下面方法参数的返回值的方法
     */
    public function curl_upload($url,$path)
    {
        $curl=curl_init($url);
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);//将curl_exec()获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($curl,CURLOPT_POST,true);//发送一个常规的post请求
        $form_data=[
          'media'=> new \CURLFile($path)
        ];
//        dd($form_data);只是media-》CURLFile{}
        curl_setopt($curl,CURLOPT_POSTFIELDS,$form_data);//这是文件传输最重要东西，全部数据使用HTTP协议中的"POST"操作来发送
        $data=curl_exec($curl);
        curl_close($curl);
        return $data;
    }
    /***
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     *
     * 线上上传
     */

    public function upload_wechat(){
//        $token=$this->get_wechat_access_token();
//        dd($token);
        return view('Wechat.upload_wechat');
    }

    /***
     * @param Request $request
     *  (--临时素材--)   image
     */
    public function do_upload_wechat_image(Request $request){
//        echo storage_path();//这是绝对路径  C:\wnmp\www\laravel-wechat\storage

        $name='file_name';
        if(!empty(request()->hasFile($name))){
            $size=$request->file($name)->getClientSize()/ 1024 / 1024;
//            dd($size);
            if($size > 2){
                echo 'the file is too big';
            }

            $path=request()->file($name)->store('wechat/image');//存入本地storage
//            dd($path);
            $_path='/storage/'.$path;
//            dd($_path);

            //拿到图片绝对路径
//            echo storage_path('app\public\wechat'.$path);//不要这个
            $url='https://api.weixin.qq.com/cgi-bin/media/upload?access_token='.$this->get_wechat_access_token().'&type=image';//新增临时素材
            $path = realpath('./storage/'.$path);//realpath() 函数返回绝对路径。
            $result=$this->curl_upload($url,$path);//调用上面方法
//            dd($result);
            $res=json_decode($result,1);
//            dd($res);
            $db=DB::connection('wechat')->table('wechat_file')->insert([
               'media_id'=>$res['media_id'],
                'path'=>$_path,
                'add_time'=>time()
            ]);
        }
    }
    /***
     * @param Request $request
     *  (--临时素材--)  video
     */
    public function do_upload_wechat_video(Request $request){
//        echo storage_path();//这是绝对路径  C:\wnmp\www\laravel-wechat\storage

        $name='file_name';
        if(!empty(request()->hasFile($name))){
            $size=$request->file($name)->getClientSize()/ 1024 / 1024;
//            dd($size);
            if($size > 10){
                echo 'the file is too big';
            }

            $path=request()->file($name)->store('wechat/video');
//            dd($path);
            //拿到图片绝对路径
//            echo storage_path('app\public\wechat'.$path);//不要这个
            $url='https://api.weixin.qq.com/cgi-bin/media/upload?access_token='.$this->get_wechat_access_token().'&type=video';
            $path = realpath('./storage/'.$path);
            $result=$this->curl_upload($url,$path);
            dd($result);
        }
    }
    /***
     * @param Request $request
     * (--临时素材--)  voice
     */
    public function do_upload_wechat_voice(Request $request){
//        echo storage_path();//这是绝对路径  C:\wnmp\www\laravel-wechat\storage

        $name='file_name';
        if(!empty(request()->hasFile($name))){
            $size=$request->file($name)->getClientSize()/ 1024 / 1024;
            $ext=$request->file($name)->getClientOriginalExtension();//弄出类型
            $file_name=time().rand(100000,999999).'.'.$ext;
//            dd($file_name);
//            dd($size);
            if($size > 10){
                echo 'the file is too big';
            }

            $path=request()->file($name)->storeAs('wechat/voice',$file_name);
//            dd($path);
            //拿到图片绝对路径
//            echo storage_path('app\public\wechat'.$path);//不要这个
            $url='https://api.weixin.qq.com/cgi-bin/media/upload?access_token='.$this->get_wechat_access_token().'&type=voice';
            $path = realpath('./storage/'.$path);
            $result=$this->curl_upload($url,$path);
            dd($result);
        }
    }




    /*
     * 线下上传
     */
    public function upload(){
//        $token=$this->get_wechat_access_token();
//        dd($token);
        return view('Wechat.upload');
    }
    public function do_upload(Request $request){
        $name='image';
//        dd($request->hasFile($name));//这个是false
//        dd($request->file($name));//如果没有返回null
//        dd($request->file($name)->isValid());//这个如果没有文件就会报错
        if(!empty($request->hasFile($name)) && request()->file($name)->isValid()){
            $path=request()->file($name)->store('goods');
            dd('/storage/'.$path);
        }else{
            echo '嘟嘟';
        }
    }


    /**
     * 获取access_token
     * @return bool|string
     */

    public function get_wechat_access_token(){
        return $this->get_access_token();
    }

    /**
     * @return bool|string
     * 获取用户列表
     */
    public function get_user_list(){
//        //获取用户openid
//        $openid="oJMd0wZtgTLURJ3hS1OaiZTd_ZvE";
//        //获取用户信息
//            $user_info=file_get_contents('https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$this->get_wechat_access_token().'&openid='.$openid.'&lang=zh_CN');
//        $user=json_decode($user_info,1);
//        $db=DB::connection('wechat')->table('user_info')->insert([
//            'openid'=>$user['openid'],
//            'nickname'=>$user['nickname'],
//            'subscribe'=>$user['subscribe'],
//            'city'=>$user['city'],
//            'country'=>$user['country'],
//            'headimgurl'=>$user['headimgurl'],
//            'subscribe_time'=>$user['subscribe_time']
//        ]);
//        die();
        //获取用户openid
        $result=file_get_contents('https://api.weixin.qq.com/cgi-bin/user/get?access_token='.$this->get_wechat_access_token().'&next_openid=');
        $re=json_decode($result,1);
//        dd($re);
        //获取用户信息
        $last_info=[];
        foreach($re['data']['openid'] as $k=>$v){
            $user_info=file_get_contents('https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$this->get_wechat_access_token().'&openid='.$v.'&lang=zh_CN');
            $user=json_decode($user_info,1);
//            dd($user);
            $last_info[$k]['nickname']=$user['nickname'];
            $last_info[$k]['openid']=$v;
            $last_info[$k]['subscribe']=$user['subscribe'];
            $last_info[$k]['city']=$user['city'];
            $last_info[$k]['country']=$user['country'];
            $last_info[$k]['headimgurl']=$user['headimgurl'];
            $last_info[$k]['subscribe_time']=$user['subscribe_time'];
//            dd($user);
        }
//        dd($last_info);

//        dd($db);
//        dd($last_info);
//        dd($re['data']['openid']);
        return view('Wechat.userList',['info'=>$last_info]);
    }
    public function get_detailed_info(){
        $data=$this->get_user_list();
//        $data=json_decode($this->get_user_list(),1);
        dd($data);
//        $dat=json_decode($data,1);
//        dd($dat);
        return view('Wechat.userInfo');
    }

    public function get_access_token(){
//        $result=file_get_contents('./web.config');
//        dd($result);

//        dd($re);
        //这是php的redis
        $redis=new \Redis();
        $redis->connect('127.0.0.1','6379');

//        dd($redis);
        //加入缓存
        $access_token_key='wechat_access_token';
        if($redis->exists($access_token_key)){
            //存在
            return $redis->get($access_token_key);
        }else{
            //不存在
            $result=file_get_contents('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.env('WECHAT_APPID').'&secret='.env('WECHAT_APPSECRET'));
//        dd($result);
            $re=json_decode($result,1);
//            dd($re);
            $redis->set($access_token_key,$re['access_token'],$re['expires_in']);//加入缓存
            return $re['access_token'];
        }

    }





}
