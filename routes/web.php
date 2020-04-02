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
Route::get('/',function() {
    return view('index');
})->middleware('auth');
Route::get('weibo/callback','Github\GithubController@getCode');
//Route::get('/github/callback','Github\GithubController@getToken');
Route::get('/github/callback','Github\SocGithubController@getGithub');
Route::get('/mobile','Github\GithubController@mobile');
Route::post('/response','Github\GithubController@response');
