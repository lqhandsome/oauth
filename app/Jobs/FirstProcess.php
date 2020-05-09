<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Http\Model\loginLogs;
use Illuminate\Http\Request;
use Mockery\Exception;

class FirstProcess implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $request;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( $request)
    {
        //
        $this->request = $request;
    }

    /**
     * Execute the job.
     *
     * @return void
     * 保存用户的登录记录
     */
    //
    public function handle()
    {
        $pams = $this->request;
        $login = new loginLogs();
        $login->ip = $pams['ip'];
        $login->name = $pams['name'];
        $login->login_time = $pams['login_time'];
        $login->uid =  $pams['uid'];
        $login->type =  $pams['type'];
        try {
            $check = $login->save();
        }catch (Exception $exception) {
            throw new \Exception("Error Processing the job", 1);
        }
        if( $check) {
            echo 'check';
        } else{
            echo 'no';
        }
    }

}
