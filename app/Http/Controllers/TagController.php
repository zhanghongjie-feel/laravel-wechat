<?php

namespace App\Http\Controllers;
use DB;
use Illuminate\Http\Request;
use App\Tools\Tools;
class TagController extends Controller
{
//    public $tools;
    public function __construct(Tools $tools)
    {
        $this->tools=$tools;
    }


    /**
     * 公众号的tag管理
     */
    public function tag_list(){
//        dd(1111);
        $url='https://api.weixin.qq.com/cgi-bin/tags/get?access_token='.$this->tools->get_access_token();
        $re=file_get_contents($url);
//        dd($re);
        $result=json_decode($re,1);
//dd($result);
        return view('Wechat.tagList',['info'=>$result]);
    }

    public function add_tag(){
        return view('Tag.addTag');
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
            return redirect('tag_list');
        }
    }
    public function delete_tag(Request $request){
        $req=$request->all();
//        dd($req);
        $url='https://api.weixin.qq.com/cgi-bin/tags/delete?access_token='.$this->tools->get_access_token();
        $data=[
            'tag'=>[
                'id'=>$req['id']
            ]
        ];
        $res=$this->tools->curl_post($url,json_encode($data));
        $result=json_decode($res,1);
//        dd($result['errcode']);
        if($result['errcode']==0){
            return redirect('tag_list');
        }
    }

    public function update_tag(Request $request){
        $req=$request->all();
//        $id=$request->all(['id']);
//        $tag_name=$request->all(['name']);
//        dd($id);
     return view('Tag.updateTag',['req'=>$req]);
    }

    public function do_update_tag(Request $request){
        $req=$request->all();
//        dd($req);
        $url='https://api.weixin.qq.com/cgi-bin/tags/update?access_token='.$this->tools->get_access_token();

        $data=[
            'tag'=>[
                'id'=>$req['id'],
                'name'=>$req['tag_name']
            ]
        ];
        $res=$this->tools->curl_post($url,json_encode($data,JSON_UNESCAPED_UNICODE));
        $result=json_decode($res,1);
        if($result['errcode']==0){
            return redirect('tag_list');
        }
    }
    /**
     * 标签下粉丝列表
     */
    public function tag_openid_list(Request $request){
        $req=$request->all();
//        dd($req);
        $url='https://api.weixin.qq.com/cgi-bin/user/tag/get?access_token='.$this->tools->get_access_token();
        $data=[
          'tagid'=>$req['tagid']
        ];
        $re=$this->tools->curl_post($url,json_encode($data));
        $result=json_decode($re,1);
//        dd($result);
        $openid=$result['data']['openid'];
//        dd($d);
        $db=DB::connection('wechat')->table('user_info')->whereIn('openid',$openid)->get();
        dd($db);
        return view('Tag.openidList',['openid'=>$openid]);
    }

    public function add_tag_openid(Request $request){
        $req=$request->all();

        $url='https://api.weixin.qq.com/cgi-bin/tags/members/batchtagging?access_token='.$this->tools->get_access_token();
        $data=[
            'openid_list'=>$req['openid_list'],
            'tagid'=>$req['tagid']
        ];
        $re=$this->tools->curl_post($url,json_encode($data));
        $result=json_decode($re,1);
        if($result['errcode']==0){
            return redirect('tag_list');
        }

    }
    /**
     * 推送标签群发消息
     */
    public function push_tag_message(Request $request){
        $req=$request->all();
//        dd($req);
        return view('Tag.pushTagMsg',['tagid'=>$request->all()['tagid']]);
    }

    public function  do_push_tag_message(Request $request){
        $req=$request->all();
//        dd($req);
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
