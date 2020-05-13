<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
use Illuminate\Support\Facades\Auth;
Route::get('/login', function () {
    return view('login');
})->name('login');
Route::get('/outlogin',function (){
    Auth::logout();
    return redirect('/login');
});
Route::get('weibo/callback','Github\GithubController@getCode');
Route::get('/github/callback','Github\SocGithubController@getGithub');
Route::get('/mobile','Github\GithubController@mobile');
Route::post('/response','Github\GithubController@response');
Route::get('/codeLogin','Github\GithubController@codeLogin');
Route::get('/loginlog','Login\LoginLog@loginlog');
Route::middleware(['auth'])->group(function () {
    //首页
    Route::get('/',function() {
        return view('index');
    });
    //获取用户登录日志
    Route::get('/loginlog','Login\LoginLog@loginlog');
    //获取登录用户信息
    Route::get('/userinfo','Login\LoginLog@UserInfo');

});