<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Tools\Tools;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */

    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
        $schedule->call(function(){
            $tools=new Tools();
            \Log::Info('执行了任务调度-推送签到模板');
            $u_info=DB::connection('test')->table('user_info')->get();
            $sign_num=$u_info->sign_num;
            $score=$u_info->score;
//            $url='https://api.weixin.qq.com/cgi-bin/message/mass/send?access_token='.$tools->get_access_token();
//           //根据openid列表群发
//           $message='hi,今天是：'.date('Y-m-d H:i');
////           dd($message);
//            $data=[
//                'touser'=>['oJMd0weUXJppG4bt4GaqSKRw9Ct4','oJMd0wXzAhg5HiK7gF7aHfMxi2AQ'],
//                'msgtype'=>'text',
//                'text'=>[
//                    'content'=>$message
//                ]
//            ];
            //$tools->curl_post($url,json_encode($data,JSON_UNESCAPED_UNICODE));
        })->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
