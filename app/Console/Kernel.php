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
            \Log::Info('任务调度');
            $url='https://api.weixin.qq.com/cgi-bin/message/mass/send?access_token='.$tools->get_access_token();
            $data=[
                'touser'=>['oJMd0weUXJppG4bt4GaqSKRw9Ct4','oJMd0wXzAhg5HiK7gF7aHfMxi2AQ'],
                'msgtype'=>'text',
                'text'=>[
                    'content'=>'嘟嘟~嘟嘟'
                ]
            ];
            $tools->curl_post($url,json_encode($data));
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
