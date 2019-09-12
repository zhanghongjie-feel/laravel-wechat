<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * \
     * 接收用户发送的消息【与用户互动，用户->微信端->开发者】
     */
    public function event(){
        echo '这是微信访问开发者接口';
    }
}
