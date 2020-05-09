<?php

namespace App\Http\Controllers\Login;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Model\loginLogs;
use Mockery\Exception;
use App\Jobs\FirstProcess;

class LoginLog extends Controller
{
    //
    public  function index(Request $request)
    {
        $login = new loginLogs();
        $login->ip = $request->ip();
        $login->name = '默认';
        $login->login_time =  time();
        $login->uid =  '1';
        try {
            $check = $login->save();
        }catch (Exception $exception) {
            throw error_log(1);
        }
        if( $check) {
            return response()->json(['message' => '写入成功','success' => true]);
        } return response()->json(2);
    }
    public function queue(Request $request)
    {
        $pams = [
            'ip' => $request->ip(),
            'login_time' => time(),
            'name' => 'default',
            'uid' => '1133',
        ];
        FirstProcess::dispatch($pams);
    }
}
