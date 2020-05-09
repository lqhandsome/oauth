<?php

namespace App\Http\Controllers\Github;

use App\Http\Controllers\Controller;
use Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\User;
use Mockery\Exception;
use App\Jobs\FirstProcess;

class SocGithubController extends Controller
{
    //通过Soc第三方社会登录登录github
   public function getGithub(Request $request)
   {
       try{
           $user = Socialite::driver('github')->stateless()->user();

       } catch ( Exception $exception){
           return 'github登录失败';
       }
       $userInfo = $user->user;
       $user = User::where( 'github_id', '=', $userInfo['id'] )->first();
       if ($user == null) {
           // 如果该用户不存在则将其保存到 users 表
           $newUser = new User();

           $newUser->github_name = $userInfo['login'];
           $newUser->name = $userInfo['login'];
           $newUser->email       = $userInfo['email'];
           $newUser->github_img = $userInfo['avatar_url'];
           $newUser->password    = '';
           $newUser->github_id = $userInfo['id'];

           $newUser->save();
           $user = $newUser;
       }

       // 手动登录该用户
       Auth::login( $user );
       $pams = [
           'ip' => $request->ip(),
           'login_time' => time(),
           'name' => $user->name,
           'uid' => $user->id,
           'type' => 'github'
       ];
       FirstProcess::dispatch($pams);
       return redirect('/');
   }

}
