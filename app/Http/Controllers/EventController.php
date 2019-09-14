<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
        $wechat_log_path=storage_path('logs/wechat/'.date('Y-m-d').'.log');
        $re=file_put_contents($wechat_log_path,"<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<\n",FILE_APPEND);
        $re=file_put_contents($wechat_log_path,$xml_string,FILE_APPEND);
        $re=file_put_contents($wechat_log_path,"<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<\n\n",FILE_APPEND);
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //$re=file_put_contents(storage_path('logs/wechat/'.date('Y-m-d').'.log'),'123234545',FILE_APPEND);//
        //注释;咱用这个做，咱们每次接收的数据写入这里面。如果没有这个文件，他是会自己生成的。他的执行模式是覆盖写入。若写入第三个参数mode,会执行相关操作，file_append是追加（不覆盖之前内容），这是一个文件指针(这里指向了末尾)，相关内容：指针和偏移
        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////
        /// 咱们做的效果，就是做的跟laravel日志一样，蛋直接放在storage->logs里有些许不合适,所以咱创建一个wechat,咱一天存一个,咱们往里面写的内容，就是$xml_string,不能在一个地方写，咱们应该换行，所以写一个标记
        //$xml_obj=simplexml_load_string($xml_string);   //他是把xml转成咱们php能识别的对象

    }
}
