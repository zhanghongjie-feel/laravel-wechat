<?php

namespace App\model;

class Openid
{
    /**
     * 网页授权获取用户openid
     * @return [type] [description]
     */
    public static function getOpenid()
    {

        //echo 1;die;
        //先去session里取openid
        $openid = session('openid');
        //var_dump($openid);die;
        if(!empty($openid)){
            return $openid;
        }
        //微信授权成功后 跳转咱们配置的地址 （回调地址）带一个code参数
        $code = request()->input('code');
        if(empty($code)){
            //没有授权 跳转到微信服务器进行授权
            $host = $_SERVER['HTTP_HOST'];  //域名
            $uri = $_SERVER['REQUEST_URI']; //路由参数
            $redirect_uri = urlencode("http://".$host.$uri);  // ?code=xx
            $url='https://open.weixin.qq.com/connect/oauth2/authorize?appid='.env('WECHAT_APPID').'&redirect_uri='.$redirect_uri.'&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect';
            header("location:".$url);die;
        }else{
            //通过code换取网页授权access_token
//            $url =  "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".self::appid."&secret=".self::secret."&code={$code}&grant_type=authorization_code";
//            $data = file_get_contents($url);
//            $data = json_decode($data,true);
//            $openid = $data['openid'];

            //通过code换取网页授权access_token和openid,userinfo也可以获取openid,snsapi_base不出来"近期已经授权过，自动登录中"
            $result=file_get_contents('https://api.weixin.qq.com/sns/oauth2/access_token?appid='.env('WECHAT_APPID').'&secret='.env('WECHAT_APPSECRET').'&code='.$code.'&grant_type=authorization_code');
            $re=json_decode($result,1);
            $openid=$re['openid'];

            //获取到openid之后  存储到session当中
            session(['openid'=>$openid]);
            return $openid;
            //如果是非静默授权 再通过openid  access_token获取用户信息
        }
    }


}
