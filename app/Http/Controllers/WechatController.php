<?php

namespace App\Http\Controllers;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use DB;
use App\Tools\Tools;
use Illuminate\Support\Facades\Storage;

class WechatController extends Controller
{
    /***
     * CURLFile
     *
     *
     *
     * echo empty(0);->1  empty(1)->0
     * download_source->voice无法转为数组json_decode::（null）,而video直接出来down_url,这不公平-------video根据返回的数据拿，其他的直接用
     * 文件名称是不是跟上传上去一样-------对
     *
     *
     *数据库为什么不存url,只是存本地路径
     * download路径判断为什么加点
     * guzzle，client  curl怎么用 getBody()
     * $request->file()->store()
     * 311
     */


//    public $tools;

    public function __construct(Tools $tools)
    {
        $this->tools=$tools;
    }


    /**
     * 调用频次清0
     * 96行url不对
     */
    public function clear_api(){
        $url='https://api.weixin.qq.com/cgi-bin/clear_quota?access_token='.$this->tools->get_access_token();
        $data=['appid'=>env('WECHAT_APPID')];
        $this->tools->curl_post($url,json_encode($data));

    }

    public function post_test(){
        dd($_POST);
    }

    /***
     * @param $url
     * @param $path
     * @return mixed
     * 这是被do_upload_wechat调用的方法，curl传输文件
     */
    public function curl_upload($url,$path,$title,$desc)
    {
        $curl=curl_init($url);
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);//将curl_exec()获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($curl,CURLOPT_POST,true);//声明我要发一个post
        $form_data=[
            'media'=> new \CURLFile($path),
            'description'=>json_encode(['title'=>$title,'introduction'=>$desc],JSON_UNESCAPED_UNICODE),
        ];

//        dd($form_data);//只是media-》CURLFile{}
        curl_setopt($curl,CURLOPT_POSTFIELDS,$form_data);//这是执行post发送的值
        $data=curl_exec($curl);
        curl_close($curl);
        return $data;
    }
    /***
     * guzzle传输文件
     */
    public function guzzle_upload($url,$path,$client,$is_video=0,$title='',$desc=''){
        $multipart=   [
            [
                'name'     => 'media',
                'contents' => fopen($path, 'r')//打开这个path找到本地资源（打开文件）
            ],
        ];
        if($is_video==1){
            $multipart[]=[
                'name'=> 'description',
                'contents'=>json_encode(['title'=>$title,'introduction'=>$desc],JSON_UNESCAPED_UNICODE)
            ];
        }
//        dd($multipart);
        $result=$client->request('POST',$url,[
            'multipart'=>$multipart
        ]);
//        dd($result);
        return $result->getBody();
    }

    /***
     *      拉取微信上传素材
     *      ----通过media_id拿到相应的资源，这玩意有方法分支
     *          --1.直接拿资源存储:（非视频）
     *          --2.拿链接，通过链接拿资源，再存（视频给个down_url,）
     */
    public function download_source(Request $request){
        $req=$request->all();
//        dd($req);
        $source_info=DB::connection('wechat')->table('wechat_source')->where(['id'=>$req['id']])->first();
        $source_arr=[1=>'image',2=>'voice',3=>'video',4=>'thumb'];
        $source_type=$source_arr[$source_info->type];
//        dd($source_type);

                //$source_type='image';
                //----------------------------------------测试，同下面的$media_id----------------------------
                //素材media_id
                //$media_id='kdM6VuFxL37ulrA2XmsLX7q6uXZO4A9iQBii7_htUdA';//视频
                //$media_id='kdM6VuFxL37ulrA2XmsLX3zw1ALr5G3ex88mulypOZU';//音频
        //---------------------------------------------------------------------
        $media_id=$source_info->media_id;
//        dd($media_id);
        //通过获取永久素材接口拿链接，通过链接发送curl_post请求拿资源，再存
        $url='https://api.weixin.qq.com/cgi-bin/material/get_material?access_token='.$this->get_wechat_access_token();
        $re=$this->tools->curl_post($url,json_encode(['media_id'=>$media_id]));
//        dd($re);
        if($source_type != 'video'){
            Storage::put('/wechat/'.$source_type.'/'.$source_info->file_name,$re);
            DB::connection('wechat')->table('wechat_source')->where(['id'=>$req['id']])->update([
                'path'=>'/storage/wechat/'.$source_type.'/'.$source_info->file_name
            ]);
           dd('可以，下载了非视频path');
        }
        //dd($re);//出来一堆乱七八糟
        //Storage::put('/wechat/voice/file1234.mp3', $re);//这玩意就能存进本地,put 方法可用于将原始文件内容保存到磁盘上
//        dd();
        $result=json_decode($re,1);//mp4直接能出来数据(down_url),而voice是null(肯定啊，video是根据链接来的，其他类型)
        //------------------------------------------------------------------------------很快获取视频资源--
        //设置超时参数
        $opts=array(
            "http"=>array(
                "method"=>"GET",
                "timeout"=>3 //单位是秒
            ),
        );
        //创建数据流上下文
        $context=stream_context_create($opts);
        //url请求的地址，例如：
        $read=file_get_contents($result['down_url'],false,$context);
        //-----------------------------------------------------------------------------------------------
//        dd($read);//还是乱七八糟一群码！！


        Storage::put('/wechat/video/'.$source_info->file_name, $read);
        DB::connection('wechat')->table('wechat_source')->where(['id'=>$req['id']])->update([
            'path'=>'/storage/wechat/'.$source_type.'/'.$source_info->file_name
        ]);
        dd('ok,视频path弄好了');
    }
    /***
     * 微信素材列表管理页面,拉取微信服务器素材的视图
     */
    public function wechat_source(Request $request,Client $client){
        $req=$request->all();
//        dd($req);
        empty($req['source_type'])?$source_type='image':$source_type=$req['source_type'];
        if(!in_array($source_type,['image','voice','video','thumb'])){
            dd('文件类型错误');
        }
//        echo empty(0);
        empty($req['page'])?$page=1:$page=$req['page'];
        if($req['page']<=0){
            dd('你这个页数很猖狂');
        }

        $pre_page=$page-1;
        $pre_page <=0 && $pre_page =1;
        $next_page=$page+1;
//        dd($page);
        //获取素材列表接口
        $url='https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token='.$this->tools->get_access_token();
        $data=[
            'type'=>$source_type,
            'offset'=>$page==1?0:($page-1)*20,
            'count'=>20
        ];
        //-------打印出你的微信服务器端素材，这是curl方法-------------
//        $re=$this->tools->curl_post($url,json_encode($data));
//        dd($re);

        //-----------guzzle使用方法(将素材信息展示出来)------------
//        $r=$client->request('POST',$url,[
//            'body'=>json_encode($data)
//        ]);
//        $re=$r->getBody();
//        $info=json_decode($re,1);
//        dd($info);

        //--------将素材数据存入redis---------------------------------
//        $this->tools->redis->set('source_info_voice',$re);
//dd();
        //这是通过redis缓存拿
        $re=$this->tools->redis->get('source_info_voice');
//        dd($re);


/////////////------------------------------------------------curl需要用--------
//        $info=json_decode($re,1);
//        dd($info);
//        get_object_vars($info);
//        dd($info);//数组
////////////---------------------------------------拿出redis,将/删掉，但是并不能用
//    $redis='{"item":[{"media_id":"kdM6VuFxL37ulrA2XmsLX5R1ZJEIe6rw-1qnr0hrySA","name":"1567836726481700.jpg","update_time":1567836726,"url":"http://mmbiz.qpic.cn/mmbiz_jpg/trsbunEJN8xuHsibRE35DVfiag5ibcx2ZiciaP8M9N7Y3cMFMZoRgAVkl2g6hEQvgxvTR2gVXGWZEw9FOo262vqAnJQ/0?wx_fmt=jpeg"},{"media_id":"kdM6VuFxL37ulrA2XmsLXy85nlOMycniPUy4NjX16U0","name":"1567826579932128.png","update_time":1567826581,"url":"http://mmbiz.qpic.cn/mmbiz_png/trsbunEJN8xuHsibRE35DVfiag5ibcx2Zicia9xj7jQ9fvs6Z2lNTic1ToqksJpAtFTeXYOGkVueLYV62yzYVGV24Ysg/0?wx_fmt=png"},{"media_id":"kdM6VuFxL37ulrA2XmsLX8x2TybZBhHHOkTpgK_Crwc","name":"1567826285357165.png","update_time":1567826293,"url":"http://mmbiz.qpic.cn/mmbiz_png/trsbunEJN8xuHsibRE35DVfiag5ibcx2Zicia9xj7jQ9fvs6Z2lNTic1ToqksJpAtFTeXYOGkVueLYV62yzYVGV24Ysg/0?wx_fmt=png"},{"media_id":"kdM6VuFxL37ulrA2XmsLX42SOaaWQ8zVU07Lv0qMN3A","name":"1567786634179501.jpg","update_time":1567786636,"url":"http://mmbiz.qpic.cn/mmbiz_jpg/trsbunEJN8zCpOIuHOVYU7RhbgZ1d8QA7QsHK9gRu8woYbdLia5TcABibx7xrDOXYias2EDXUdXcUQ38TKqJoW1FA/0?wx_fmt=jpeg"},{"media_id":"kdM6VuFxL37ulrA2XmsLX8JrhmvQkvN2t7jILyY37P4","name":"1567784045407087.jpg","update_time":1567784059,"url":"http://mmbiz.qpic.cn/mmbiz_png/trsbunEJN8zCpOIuHOVYU7RhbgZ1d8QAqvvlHglArUySq5ht3UocL6Mo9W0QS4wKAFAWw8vfP9JAjKEs9IFM3Q/0?wx_fmt=gif"},{"media_id":"kdM6VuFxL37ulrA2XmsLX1hy9LcV4OT2IGcbYN8e7NY","name":"1567783733979089.jpg","update_time":1567783741,"url":"http://mmbiz.qpic.cn/mmbiz_png/trsbunEJN8zCpOIuHOVYU7RhbgZ1d8QAqvvlHglArUySq5ht3UocL6Mo9W0QS4wKAFAWw8vfP9JAjKEs9IFM3Q/0?wx_fmt=gif"}],"total_count":6,"item_count":6}';
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//        $this->tools->redis->set('source_info_part',$redis);
        //$redis="{\"item\":[{\"media_id\":\"kdM6VuFxL37ulrA2XmsLX5R1ZJEIe6rw-1qnr0hrySA\",\"name\":\"1567836726481700.jpg\",\"update_time\":1567836726,\"url\":\"http:\\/\\/mmbiz.qpic.cn\\/mmbiz_jpg\\/trsbunEJN8xuHsibRE35DVfiag5ibcx2ZiciaP8M9N7Y3cMFMZoRgAVkl2g6hEQvgxvTR2gVXGWZEw9FOo262vqAnJQ\\/0?wx_fmt=jpeg\"},{\"media_id\":\"kdM6VuFxL37ulrA2XmsLXy85nlOMycniPUy4NjX16U0\",\"name\":\"1567826579932128.png\",\"update_time\":1567826581,\"url\":\"http:\\/\\/mmbiz.qpic.cn\\/mmbiz_png\\/trsbunEJN8xuHsibRE35DVfiag5ibcx2Zicia9xj7jQ9fvs6Z2lNTic1ToqksJpAtFTeXYOGkVueLYV62yzYVGV24Ysg\\/0?wx_fmt=png\"},{\"media_id\":\"kdM6VuFxL37ulrA2XmsLX8x2TybZBhHHOkTpgK_Crwc\",\"name\":\"1567826285357165.png\",\"update_time\":1567826293,\"url\":\"http:\\/\\/mmbiz.qpic.cn\\/mmbiz_png\\/trsbunEJN8xuHsibRE35DVfiag5ibcx2Zicia9xj7jQ9fvs6Z2lNTic1ToqksJpAtFTeXYOGkVueLYV62yzYVGV24Ysg\\/0?wx_fmt=png\"},{\"media_id\":\"kdM6VuFxL37ulrA2XmsLX42SOaaWQ8zVU07Lv0qMN3A\",\"name\":\"1567786634179501.jpg\",\"update_time\":1567786636,\"url\":\"http:\\/\\/mmbiz.qpic.cn\\/mmbiz_jpg\\/trsbunEJN8zCpOIuHOVYU7RhbgZ1d8QA7QsHK9gRu8woYbdLia5TcABibx7xrDOXYias2EDXUdXcUQ38TKqJoW1FA\\/0?wx_fmt=jpeg\"},{\"media_id\":\"kdM6VuFxL37ulrA2XmsLX8JrhmvQkvN2t7jILyY37P4\",\"name\":\"1567784045407087.jpg\",\"update_time\":1567784059,\"url\":\"http:\\/\\/mmbiz.qpic.cn\\/mmbiz_png\\/trsbunEJN8zCpOIuHOVYU7RhbgZ1d8QAqvvlHglArUySq5ht3UocL6Mo9W0QS4wKAFAWw8vfP9JAjKEs9IFM3Q\\/0?wx_fmt=gif\"},{\"media_id\":\"kdM6VuFxL37ulrA2XmsLX1hy9LcV4OT2IGcbYN8e7NY\",\"name\":\"1567783733979089.jpg\",\"update_time\":1567783741,\"url\":\"http:\\/\\/mmbiz.qpic.cn\\/mmbiz_png\\/trsbunEJN8zCpOIuHOVYU7RhbgZ1d8QAqvvlHglArUySq5ht3UocL6Mo9W0QS4wKAFAWw8vfP9JAjKEs9IFM3Q\\/0?wx_fmt=gif\"}],\"total_count\":6,\"item_count\":6}";
        ///////////////////////////////////////////////////////////

        $info=json_decode($re,1);
//        dd($info);
        $media_id_list=[];
        $source_arr=['image'=>1,'voice'=>2,'video'=>3,'thumb'=>4];
        foreach($info['item'] as $v){
//           dd($info['item']);

            //同步数据库
           $media_info = DB::connection('wechat')->table('wechat_source')->where(['media_id'=>$v['media_id']])->select(['id'])->first();
//           dd($media_info);
            if(empty($media_info)){
                DB::connection('wechat')->table('wechat_source')->insert([
                    'media_id'=>$v['media_id'],
                    'type'=>$source_arr[$source_type],
                    'add_time'=>$v['update_time'],
                    'file_name'=>$v['name'],
                ]);
            }

            $media_id_list[]=$v['media_id'];
        }
       $source_info=DB::connection('wechat')->table('wechat_source')->whereIn('media_id',$media_id_list)->where(['type'=>$source_arr[$source_type]])->get();
//        dd($source_info);

        foreach($source_info as $k=>$v){
            $is_download=0; // 0 无需下载 1 需要下载
            if(empty($v->path)){
                $is_download=1;
            }elseif(!empty($v->path) && !file_exists('.'.$v->path)){
                $is_download=1;
            }
            $source_info[$k]->is_download=$is_download;//设置一个参数
//            dd($is_download);
        }
//        dd($source_info);看本地数据库foreach数据，这是Collection集合类型，变成数组->toArray();
        return view('Wechat.source',['info'=>$source_info,'pre_page'=>$pre_page,'next_page'=>$next_page,'source_type'=>$source_type]);
    }


    /***
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     *
     * 线上上传
     */
    public function upload_wechat(){
        return view('Wechat.upload_wechat');
    }
    /***
     * @param Request $request
     *  (--临时素材--)
     */
    public function do_upload_wechat(Request $request,Client $client){
//        echo storage_path();//这是绝对路径  C:\wnmp\www\laravel-wechat\storage
        $type=$request->all()['type'];
        $source_type='';
        switch ($type){
            case 1;$source_type='image';break;
            case 2;$source_type='voice';break;
            case 3;$source_type='video';break;
            case 4;$source_type='thumb';break;
            default;
        }

        $name='file_name';
        if(!empty(request()->hasFile($name)) && request()->file($name)->isValid()){
            //大小 资源类型
            $ext=$request->file($name)->getClientOriginalExtension();//弄出文件类型
            $size=$request->file($name)->getClientSize()/ 1024 / 1024;
            if($source_type=='image'){
               if(!in_array($ext,['jpg','png','jpeg','gif'])){
                   dd('不是图片格式');
               }
               if($size > 2){
                   dd('图片过大');
               }
            }elseif($source_type=='voice'){
                if(!in_array($ext,['mp3','amr'])){
                    dd('非音频格式');
                }
                if($size>2){
                    dd('这个音频太大了');
                }
            }elseif($source_type=='video'){
                if(!in_array($ext,['mp4'])){
                    dd('非视频格式');
                }
                if($size>10){
                    dd('视频过大');
                }
            }elseif($source_type=='thumb'){
                if(!in_array($ext,['jpg'])){
                    dd('非jpg格式');
                }
                if($size>0.0625){
                    dd('缩略图太大');
                }
            }
            //$local_path=request()->file($name)->store('wechat/'.$source_type);//存入本地storage
//            dd($local_path);
            $file_name=time().rand(100000,999999).'.'.$ext;//随便用rand函数生成一个名字
            $path=request()->file($name)->storeAs('wechat/'.$source_type,$file_name);//storeAs,文件上传时，修改上传的文件名
//            dd($path);
            $_path='/storage/'.$path;
//            dd($_path);
            $path = realpath('./storage/'.$path);//realpath() 函数返回绝对路径。
//            dd($path);
            //新增临时素材接口
            //$url='https://api.weixin.qq.com/cgi-bin/media/upload?access_token='.$this->get_wechat_access_token().'&type='.$source_type;//新增临时素材。
            //新增其他类型永久素材
            $url='https://api.weixin.qq.com/cgi-bin/material/add_material?access_token='.$this->get_wechat_access_token().'&type='.$source_type;
            if($source_type=='video'){
                $title='标题';
                $desc='描述';
                $result=$this->guzzle_upload($url,$path,$client,1,$title,$desc);//guzzle上传video
            }else{
                $result=$this->guzzle_upload($url,$path,$client);//guzzle上传除video外素材
            }
            ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //这是curl上传视频方式(如果非video则只需要传$url,$path)  {"type":"image","media_id":"loT4fyrpRqfwAeDSwJ5oLqQi_bEUY48zE22tCgqIEnOnGrP2KWqIy2r1T1ZD2KgB","created_at":1567774545}
                //$title='标题';
                //$desc='描述';
                //$result=$this->curl_upload($url,$path,$title,$desc);//调用上面curl_upload方法
                    //dd($result);
            ///////////////////////////////////////////////////////////////////////////////////////////////////////////
            //这是guzzle方法(注yi：这个不能用！！！！用上边)
//            $result=$this->guzzle_upload($url,$path,$client);
//            dd($result);
            ////////////////////////////////////////////////

            $re=json_decode($result,1);
            dd($re);
//插入数据库

            DB::connection('wechat')->table('wechat_source')->insert([
                'media_id'=>$re['media_id'],
                'type'=>$type,
                'path'=>$_path,
                'add_time'=>time()
            ]);
                //拿到图片绝对路径
//            echo storage_path('app\public\wechat'.$path);//不要这个


        }
    }
    /***
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * 删除素材
     */
    public function del_wechat_source(Request $request){
        $req=$request->all();
        $media_id=$req['media_id'];
//        dd($media_id);
        $url='https://api.weixin.qq.com/cgi-bin/material/del_material?access_token='.$this->tools->get_access_token();
        $data=[
            'media_id'=>$media_id
        ];
        $re=$this->tools->curl_post($url,json_encode($data));
        $result=json_decode($re);
//        dd($result);
        $db=DB::connection('wechat')->table('wechat_source')->where([
            'media_id'=>$media_id
        ])->delete();

        dd($db);
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
        $path=request()->file('image')->store('goods');
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
        return $this->tools->get_access_token();
    }

    /**
     * @return bool|string
     * 获取用户列表
     */
    public function get_user_list(Request $request){
        ///////////////////EasyWechat/////////////////////////////////////////
        $app = app('wechat.official_account');
        $user_list=$app->user->list($nextOpenId = null);  // $nextOpenId 可选
        dd($user_list);
    ///////////////////////////////////////////////////////////////////////////////////
        //--------------------------------------------------我是这么存的
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
        ////----------------------------------这是通过openid拉去线上粉丝----------------------------------------------------------------------------------------------
        //获取用户openid
        $result=file_get_contents('https://api.weixin.qq.com/cgi-bin/user/get?access_token='.$this->get_wechat_access_token().'&next_openid=');
        $re=json_decode($result,1);
//        dd($re);
        //获取用户信息
        $last_info=[];
        foreach($re['data']['openid'] as $k=>$v){
            $user_info=file_get_contents('https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$this->get_wechat_access_token().'&openid='.$v.'&lang=zh_CN');
//            dd($user_info);
            $user=json_decode($user_info,1);//把json字符串转为数组
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
  ///-----------------------------------------------------------------------------------------------------------------------------------------
//        dd($last_info);

//        dd($db);
//        dd($last_info);
//        dd($re['data']['openid']);
        //数据库
        $req=$request->all();
        $db=DB::connection('wechat')->table('user_info')->where(['subscribe'=>1])->get();
//        dd($db);
        return view('Wechat.userList',['info'=>$db,'tagid'=>isset($req['tagid'])?$req['tagid']:'']);
    }
    public function get_detailed_info(){
        $data=$this->get_user_list();
//        $data=json_decode($this->get_user_list(),1);
        dd($data);
//        $dat=json_decode($data,1);
//        dd($dat);
        return view('Wechat.userInfo');
    }

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////









    /*
     * 发送模板消息
     */
    public function send_template_message(){
        $openid='oJMd0weUXJppG4bt4GaqSKRw9Ct4';
        $url='https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$this->tools->get_access_token();
        $data=[
            'touser'=>$openid,
            'template_id'=>'gDsIyl1h_elVHIzk_V2txsZhno_jspfhZwISvAbukEY',
            'url'=>'www.laravel.com',
            'data'=>[
                'first'=>[
                    'value'=>'签到提醒',
                    'color'=>''
                ],
                'keyword1'=>[
                    'value'=>'阿伟啊'
                ],
                'keyword2'=>[
                    'value'=>'你不操蛋了'
                ],
                'remark'=>[
                    'value'=>'假期快乐',
                    'color'=>''
                ]
            ]
        ];
        $re=$this->tools->curl_post($url,json_encode($data,JSON_UNESCAPED_UNICODE));
        $result=json_decode($re,1);
        dd($result);
    }














/////////////////////////////////////////////////////////////////////////////////////// 菜    单  ////////////////////////////////////////////////////////
    /**
     * 自定义菜单（最基础原始的发送）
     */
    public function menu(Request $request){
        $url='https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$this->tools->get_access_token();
        $data=[
            'button'=>[
                [
                    'type'=>'click',
                    'name'=>'看看谁骚',
                    'key'=>'dudu'
                ],

                [
                    'name'=>'一个菜单',
                    'sub_button'=>[
                        [
                            'type'=>'view',
                            'name'=>'要不点一下看看',
                            'url'=>'http://wechat.distantplace.vip/'
                        ],
                        [
                            'type'=>'click',
                            'name'=>'赶快赞我一下',
                            'key'=>'dianzan'
                        ]
                    ]
                ]

            ],
           ];
        //dd(json_encode($data));

        $res=$this->tools->curl_post($url,json_encode($data,JSON_UNESCAPED_UNICODE));
//        dd($res);
        $result=json_decode($res,1);
        dd($result);
    }


    /***
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * 自定义菜单列表
     */

    public function menuList(){
        $info=DB::connection('wechat')->table('wechat_menu')->orderBy('name1','desc','name2','desc')->get();
//        dd($info);
        return view('Wechat.menuList',['info'=>$info]);
    }

    /**
     * 删他么个自定义菜单
     */
        public function menu_del(Request $request){
            $id=$request->all()['id'];
//            dd($id);
            $del=DB::connection('wechat')->table('wechat_menu')->where(['id'=>$id])->delete();
            if(!$del){
                dd('fail of delete');
            }
            //根据表数据翻译成菜单结构
            $this->load_menu();
        }

    /***
     * @param Request $request
     * 把菜单给他入个库
     */
    public function create_menu(Request $request){
        $req=$request->all();
//        dd($req);
        $button_type= !empty($req['name2'])?2:1;
//        dd($button_type);
        $result=DB::connection('wechat')->table('wechat_menu')->insert([
            'name1'=>$req['name1'],
            'name2'=>$req['name2'],
            'type'=>$req['type'],
            'button_type'=>$button_type,
            'event_value'=>$req['event_value']
        ]);
        if(!$result){
            dd('插入菜单失败');
        }
        //根据表数据翻译成菜单结构
        $this->load_menu();
    }

    /****
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * 从数据库里拿数据然后生成个微信菜单
     */
    public function load_menu(){
        $data=[];
        $menu_list=DB::connection('wechat')->table('wechat_menu')->select(['name1'])->groupBy('name1')->get();//虽然将整个集合转成数组，但他里面的数据还是对象
//        dd($menu_info);
        foreach($menu_list as $vv){
            $menu_info=DB::connection('wechat')->table('wechat_menu')->where(['name1'=>$vv->name1])->get();
            //dd($menu_info);//先出来一个一级菜单
            $menu=[];
            foreach($menu_info as $v){
                $menu[]=(array)$v;
            }
            $arr=[];
            foreach($menu as $v){
                if($v['button_type']==1){ //一级菜单
                    if($v['type']==1){//click
                        $arr=[
                            'type'=>'click',
                            'name'=>$v['name1'],
                            'key'=>$v['event_value']
                        ];
//                    dd($v['type']);
                    }elseif($v['type']==2){//view
                        $arr=[
                            'type'=>'view',
                            'name'=>$v['name1'],
                            'url'=>$v['event_value']
                        ];
                    }
//                    $data['button'][]=$arr;//这里是会加一个button=>[2]
                }elseif($v['button_type']==2){//二级菜单
                    $arr['name']=$v['name1'];
                    if($v['type']==1){//click
                        $button_arr=[
                            'type'=>'click',
                            'name'=>$v['name2'],
                            'key'=>$v['event_value']
                        ];
//                    dd($v['type']);
                    }elseif($v['type']==2){//view
                        $button_arr=[
                            'type'=>'view',
                            'name'=>$v['name2'],
                            'url'=>$v['event_value']
                        ];
                    }
                    $arr['sub_button'][]=$button_arr;
                }
            }
        $data['button'][]=$arr;//把$arr都写在了这里面
        }
//        dd($data);

//        dd(json_encode($data,JSON_UNESCAPED_UNICODE));//这是全是一级菜单的数据


    $url='https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$this->tools->get_access_token();
    $res=$this->tools->curl_post($url,json_encode($data,JSON_UNESCAPED_UNICODE));
    $result=json_decode($res,1);
    dd($result);

    }


///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * jssdk
     */
    public function location(){
        $url='http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        $jsapi_ticket=$this->tools->get_wechat_jsapi_ticket();
        $timestamp=time();
        $noncestr=rand(1000,9999).'suii';
        $sign_str ='jsapi_ticket='.$jsapi_ticket.'&noncestr='.$noncestr.'&timestamp='.$timestamp.'&url='.$url;//把上边四个ascii码从小到大放进一个字符串
        $signature=sha1($sign_str);
        echo $signature;
        return view('location',['noncestr'=>$noncestr,'signature'=>$signature,'timestamp'=>$timestamp]);

    }

    public function send(){
        $tools=new Tools();
        $url='https://api.weixin.qq.com/cgi-bin/message/mass/send?access_token='.$tools->get_access_token();
        $message='hi,今天是'.date('Y-m-d H:i');
//        dd($message);
        $data=[
            'touser'=>['oJMd0weUXJppG4bt4GaqSKRw9Ct4','oJMd0wXzAhg5HiK7gF7aHfMxi2AQ'],
            'msgtype'=>'text',
            'text'=>[
                'content'=>$message
            ],
//            'clientmsgid'=>'send_tag_100'
        ];
//        dd($data);
        $res=$tools->curl_post($url,json_encode($data,JSON_UNESCAPED_UNICODE));
        $result=json_decode($res,1);
        dd($result);
    }
}
