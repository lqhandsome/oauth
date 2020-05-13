<?php

namespace App\Http\Controllers\Login;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Model\loginLogs;
use Illuminate\Support\Facades\Auth;
use Mockery\Exception;
use App\Jobs\FirstProcess;

class LoginLog extends Controller
{
    //
    public  function index(Request $request)
    {

    }

    /**
     * 返回登录用户信息
     */
    public function UserInfo()
    {

        return response()->json(['userName' => Auth::user()->name,'success' => true],200);
    }
    public function loginLog(Request $request)
    {
            $perPage = $request->input('perPage') ?? 10;
            $id = Auth::id();
            $logs = loginLogs::where('uid',$id)->orderBy('id','desc')->paginate($perPage)->toArray();
            $count = count($logs);
           return response()->json(['logs' => $logs,'count' =>$count, 'success' => true]);
    }
}
