<?php

namespace App\Http\Controllers\Mail;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
class MailController extends Controller
{
    //
    public function send()
    {
        Mail::raw('hello world1', function ($message) {
            $to = '1291752135@qq.com';
            $message ->to($to)->subject('CAM');
        });
        // 返回的一个错误数组，利用此可以判断是否发送成功
        dd(Mail::failures());
    }
}
