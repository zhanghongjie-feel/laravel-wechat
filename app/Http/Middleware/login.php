<?php

namespace App\Http\Middleware;

use Closure;

class login
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
//        dd($request->session()->all());
        //前置
//        $result=$request->session()->has('uid');
////         dd($result);
//        if($result==false){
//            dd('请先登录');
//        }
//        $request->session()->put(['uid'=>16]);//线下自己写的
        $result=$request->session()->has('uid');
        if(!$result){
            dd('请先登录');
        }
//        $request->session()->put(['uid'=>$request->session()->all()['uid']]);//这是自己设置的uid
        $response = $next($request);

        //后置
//        echo 222;
        return $response;
//        return $next($request);
    }
}
