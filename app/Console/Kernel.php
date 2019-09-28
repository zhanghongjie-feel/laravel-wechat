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
            //在linux中写一个定时任务，每天晚上8点，发送一个模板消息给学生，写的课程
//            //1.查出所有openid
//            $openid=file_get_contents('https://api.weixin.qq.com/cgi-bin/user/get?access_token='.$tools->get_access_token().'&next_openid=');
//            $res=json_decode($openid,1);
//            $openid_list=$res['data']['openid'];
////        dd($openid_list);
//            foreach($openid_list as $k=>$v){
//                $url='https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$this->tools->get_access_token();
//                $data=[
//                    'touser'=>$v,
//                    'template_id'=>'gDsIyl1h_elVHIzk_V2txsZhno_jspfhZwISvAbukEY',
//                    'url'=>'wechat.distantplace.vip',
//                    'data'=>[
//                        'first'=>[
//                            'value'=>'我丢',
//                            'color'=>''
//                        ],
//                        'keyword1'=>[
//                            'value'=>'没事'
//                        ],
//                        'keyword2'=>[
//                            'value'=>'我就发个玩玩'
//                        ],
//                        'remark'=>[
//                            'value'=>'喝喽，艾瑞巴蒂',
//                            'color'=>''
//                        ]
//                    ]
//                ];
//                $re=$this->tools->curl_post($url,json_encode($data,JSON_UNESCAPED_UNICODE));
//            }

            ///////////  签    到  之任务调度（判断是否签到，然后群发wechat-test->user_info）
//            $u_info=DB::connection('test')->table('user_info')->get()->toArray();
//            $today=date('Y-m-d',time());
////        dd($today);
//            foreach ($u_info as $k=>$v){
////            print_r($v);
//                if($today!==$u_info[$k]->signin){
//                    $url='https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$this->tools->get_access_token();
//                    $data=[
//                        'touser'=>$u_info[$k]->openid,
//                        'template_id'=>'gDsIyl1h_elVHIzk_V2txsZhno_jspfhZwISvAbukEY',
//                        'url'=>'www.laravel.com',
//                        'data'=>[
//                            'first'=>[
//                                'value'=>'签到提醒',
//                                'color'=>''
//                            ],
//                            'keyword1'=>[
//                                'value'=>'今日未签到'
//                            ],
//                            'keyword2'=>[
//                                'value'=>'积分是'.$u_info[$k]->score
//                            ],
//                            'remark'=>[
//                                'value'=>'祝你签到快乐',
//                                'color'=>''
//                            ]
//                        ]
//                    ];
//                    $re=$this->tools->curl_post($url,json_encode($data,JSON_UNESCAPED_UNICODE));
//                }elseif($today!==$u_info[$k]['signin']){
//                    $url='https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$this->tools->get_access_token();
//                    $data=[
//                        'touser'=>$u_info[$k]->openid,
//                        'template_id'=>'gDsIyl1h_elVHIzk_V2txsZhno_jspfhZwISvAbukEY',
//                        'url'=>'www.laravel.com',
//                        'data'=>[
//                            'first'=>[
//                                'value'=>'签到提醒',
//                                'color'=>''
//                            ],
//                            'keyword1'=>[
//                                'value'=>'今日已经签到'
//                            ],
//                            'keyword2'=>[
//                                'value'=>'积分是'.$u_info[$k]->score
//                            ],
//                            'remark'=>[
//                                'value'=>'祝你赶紧签到',
//                                'color'=>''
//                            ]
//                        ]
//                    ];
//                    $re=$this->tools->curl_post($url,json_encode($data,JSON_UNESCAPED_UNICODE));
//                }
//            }
            ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//            $u_info=DB::connection('test')->table('user_info')->get();
//            $sign_num=$u_info->sign_num;
//            $score=$u_info->score;
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
