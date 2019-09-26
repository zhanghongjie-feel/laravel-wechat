<?php

namespace App\Http\Controllers;

use App\Tools\Tools;
use Illuminate\Http\Request;
use DB;

class EventController extends Controller
{
    /**
     * \
     * 接收用户发送的消息【与用户互动，用户->微信端->开发者】
     */
    public function event()
    {
//        dd($_POST);微信传过来的数据是postman->raw的格式，大POST是拿不到的
//        echo $_GET['echostr'];//这个玩意是在第一次设置接口配置信息时需要在url里echo一下？echostr=111以表示微信返回给开发者的接口可以用
        $xml_string=file_get_contents('php://input');//微信返回的格式是xml字符串，用它来获取到
//        dd($xml_string);
        $wechat_log_path=storage_path('logs/wechat/'.date('Y-m-d').'.log');
        file_put_contents($wechat_log_path,"<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<\n",FILE_APPEND);
        file_put_contents($wechat_log_path,$xml_string,FILE_APPEND);
        file_put_contents($wechat_log_path,"\n<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<\n\n",FILE_APPEND);
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //$re=file_put_contents(storage_path('logs/wechat/'.date('Y-m-d').'.log'),'123234545',FILE_APPEND);//
        //注释;咱用这个做，咱们每次接收的数据写入这里面。如果没有这个文件，他是会自己生成的。他的执行模式是覆盖写入。若写入第三个参数mode,会执行相关操作，file_append是追加（不覆盖之前内容），这是一个文件指针(这里指向了末尾)，相关内容：指针和偏移
        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////
        /// 咱们做的效果，就是做的跟laravel日志一样，蛋直接放在storage->logs里有些许不合适,所以咱创建一个wechat,咱一天存一个,咱们往里面写的内容，就是$xml_string,不能在一个地方写，咱们应该换行，所以写一个标记
        $xml_obj=simplexml_load_string($xml_string,'SimpleXMLElement',LIBXML_NOCDATA);   //他是把xml转成咱们php能识别的对象.LIBXML_NOCDATA,将 CDATA 设置为文本节点(咱接收的xml解析不了cdata,所以直接百度出来这个的东西)，还差第二个参数classname，规定新对象的 class。,根据定义simplexml_load_string() 函数转换形式良好的 XML 字符串为 SimpleXMLElement 对象，应该就是SimpleXMLElement
//        dd($xml_obj);
        $xml_arr=(array)$xml_obj;//强制类型转换:obj->array
//        dd($xml_arr);
        \Log::Info(json_encode($xml_arr,JSON_UNESCAPED_UNICODE));//又写了一个laravel日志，他会不会与别的混了呢
        //业务逻辑

            //$d=date('Y-m-d,H:i:s',$start);今天凌晨时间

        /////判 断 是 否 应 该 签 到
        $openid=$xml_arr['FromUserName'];
        $u_info=DB::connection('test')->table('user_info')->where(['openid'=>$openid])->first();
        if(empty($u_info)){
            //根据openid和access-token拿到信息，存入table
        }
        $pre_time=$u_info->signin;
//        $d=date('Y-m-d H:i:s',$pre_time);
//        $start=strtotime('0:00:00');//今天的0：00
//          dd($start);
        $today=date('Y-m-d',time());
            ///////////////////////////////////////////////////  带参数二维码关注/取关/SCAN 事件  /////////////////////////////////////////////////////////////////////////////////

        if($xml_arr['MsgType']=='event' && $xml_arr['Event']=='subscribe') {
            ///////////////////////////////////////////////////////////////////根据openid和access_token获取关注者的名字


//            if(empty($xml_arr['EventKey'])){
//                $message = '欢迎关注，' . $user_name;
//                $xml_str = '<xml><ToUserName><![CDATA[' . $xml_arr['FromUserName'] . ']]></ToUserName><FromUserName><![CDATA[' . $xml_arr['ToUserName'] . ']]></FromUserName><CreateTime>' . time() . '</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA[' . $message . ']]></Content></xml>';
//                echo $xml_str;
//                dd();
//            }
            //////////////////   如 果 收 到 关 注  ,回 复 欢 迎 关 注，然后存一条
            $share_code = explode('_', $xml_arr['EventKey']);
            dd($xml_arr['EventKey']);
            $user_openid = $xml_arr['FromUserName'];//粉丝openid
            $tools = new Tools();
            $user_info = file_get_contents('https://api.weixin.qq.com/cgi-bin/user/info?access_token=' . $tools->get_access_token() . '&openid=' . $user_openid . '&lang=zh_CN');
            $user_in = json_decode($user_info, 1);
            $user_name = $user_in['nickname'];
            $_user = DB::connection('test')->table('user_info')->where(['openid' => $user_in['openid']])->first();
            if (empty($_user)) {
                DB::connection('test')->table('user_info')->insert([
                    'openid' => $user_in['openid'],
                    'nickname' => $user_name,
                    'add_time' => time()
                ]);
            }

            /////////////////////////////////////////////////////////////////////////////////////
            //  判 断 是 否 曾 经 关 注 过
            $wechat_openid = DB::connection('wechat')->table('wechat_openid')->where(['openid' => $user_openid])->first();
            if (empty($wechat_openid)) {
                DB::connection('wechat')->table('user')->where(['id' => $share_code])->increment('share_num', 1);
                DB::connection('wechat')->table('wechat_openid')->insert([
                    'openid' => $user_openid,
                    'add_time' => time()
                ]);

            }
            ///欢迎关注一下子
            $message = '欢迎关注，' . $user_name;
            $xml_str = '<xml><ToUserName><![CDATA[' . $xml_arr['FromUserName'] . ']]></ToUserName><FromUserName><![CDATA[' . $xml_arr['ToUserName'] . ']]></FromUserName><CreateTime>' . time() . '</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA[' . $message . ']]></Content></xml>';
            echo $xml_str;
        }elseif($xml_arr['MsgType']=='event'){
            if($xml_arr['Event']=='SCAN') {
                //欢迎回来
                $xml_str = '<xml><ToUserName><![CDATA[' . $xml_arr['FromUserName'] . ']]></ToUserName><FromUserName><![CDATA[' . $xml_arr['ToUserName'] . ']]></FromUserName><CreateTime>' . time() . '</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA[欢迎回来]]></Content></xml>';
                echo $xml_str;
            }elseif($xml_arr['Event']=='SCAN' && $today!==$pre_time)   {
//                $xml_str = '<xml><ToUserName><![CDATA[' . $xml_arr['FromUserName'] . ']]></ToUserName><FromUserName><![CDATA[' . $xml_arr['ToUserName'] . ']]></FromUserName><CreateTime>' . time() . '</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA[欢迎回来]]></Content></xml>';
//                echo $xml_str;
                $message = '请签到';
                $xml_str = '<xml><ToUserName><![CDATA[' . $xml_arr['FromUserName'] . ']]></ToUserName><FromUserName><![CDATA[' . $xml_arr['ToUserName'] . ']]></FromUserName><CreateTime>' . time() . '</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA[' . $message . ']]></Content></xml>';
                echo $xml_str;
            }




                ////    如 若 是 回 复 给 我 们 文 本 消 息
        }elseif($xml_arr['MsgType']=='text'){
                $message = '嘤嘤嘤';
                $xml_in = '<xml><ToUserName><![CDATA[' . $xml_arr['FromUserName'] . ']]></ToUserName><FromUserName><![CDATA[' . $xml_arr['ToUserName'] . ']]></FromUserName><CreateTime>' . time() . '</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA[' . $message . ']]></Content></xml>';
                echo $xml_in;
        }


        /////////////////////////////////判断时间修改课程   Route::get('course/admin','test\CourseController@admin');



        ////////////////////////////////////////////////判 断 是 否 签 到
//        $time=strtotime('-1 days');//昨天的这个点
//        $t=date('Y-m-d',$time);//把昨天这个点转成普通时间


            //   用 户 点 击 签 到（连for循环都用不到）
//        for($num=0,$score=0;$num<5;$num++){
//            $score=$score+5;
//            $num+1;
//        }
            $sign_num=$u_info->sign_num;
            $score=$u_info->score;
            if($xml_arr['MsgType']=='event'){
                if($xml_arr['Event']=='CLICK'){
                    if($xml_arr['EventKey']=='dudu'){
                        if($today==$pre_time){
                            $message='已签到';
                            $xml_str='<xml><ToUserName><![CDATA['.$xml_arr['FromUserName'].']]></ToUserName><FromUserName><![CDATA['.$xml_arr['ToUserName'].']]></FromUserName><CreateTime>'.time().'</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA['.$message.']]></Content></xml>';
                            echo $xml_str;
                        }elseif($today!==$pre_time) {
                            if($sign_num<5){
                                DB::connection('test')->table('user_info')->where(['openid'=>$openid])->update([
                                    'sign_num'=>$sign_num+1
                                ]);
                                DB::connection('test')->table('user_info')->where(['openid'=>$openid])->update([
                                    'score'=>$score+5
                                ]);
                                DB::connection('test')->table('user_info')->where(['openid'=>$openid])->update([
                                    'signin'=>date('Y-m-d',time())
                                ]);
                            }else{
                                DB::connection('test')->table('user_info')->where(['openid'=>$openid])->update([
                                    'sign_num'=>0
                                ]);
                                DB::connection('test')->table('user_info')->where(['openid'=>$openid])->update([
                                    'score'=>5
                                ]);
                                DB::connection('test')->table('user_info')->where(['openid'=>$openid])->update([
                                    'signin'=>date('Y-m-d',time())
                                ]);
                            }
                            $message='签到成功';
                            $xml_sign='<xml><ToUserName><![CDATA['.$xml_arr['FromUserName'].']]></ToUserName><FromUserName><![CDATA['.$xml_arr['ToUserName'].']]></FromUserName><CreateTime>'.time().'</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA['.$message.']]></Content></xml>';
                            echo $xml_sign;
                        }
                        ////// 让 用 户 查 看 当前积分
                    }elseif($xml_arr['EventKey']=='duduo'){
                        $info=DB::connection('test')->table('user_info')->where(['openid'=>$openid])->first();
                        $score=$info->score;
                        $message='你的积分为'.$score;
                        $xml_sign='<xml><ToUserName><![CDATA['.$xml_arr['FromUserName'].']]></ToUserName><FromUserName><![CDATA['.$xml_arr['ToUserName'].']]></FromUserName><CreateTime>'.time().'</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA['.$message.']]></Content></xml>';
                        echo $xml_sign;
                    }
                }



        }






    }
}
