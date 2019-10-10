<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LoginController extends Controller
{
    public function login(){
        $id=time().rand(1000,9999); //用户标识
        $redirect_url="http://wechat.distantplace.vip/wechat_login";
        return view('Admin.login',['id'=>$id,'redirect_url'=>$redirect_url]);
    }

    public function wechat_login(){
        $id=request('id');//二维码唯一标识
        dd($id);
    }
}
