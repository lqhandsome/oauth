<?php

namespace App\Http\Controllers\Github;

use Cache;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Mockery\Exception;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use Socialite;
use GuzzleHttp\Client;
use Whoops\Exception\ErrorException;
use App\User;
use Redis;

class GithubController extends Controller
{
    private $GITHUB_CLIENT_ID = 'e0dcea40a90ff58b7ce3';
    private $GITHUB_SECRET = 'f4cc13c89cceab9e9917370fbbf2586c33744320';
    //返回Code
    public function getToken(Request $request)
    {
        dd(config('services.github'));
        $code = $request->input('code');
        $client = new Client();
        $res = $client->request('POST', 'https://github.com/login/oauth/access_token',
          [
              'form_params' => [
                  'client_id' => $this->GITHUB_CLIENT_ID,
                  'client_secret'=>$this->GITHUB_SECRET,
                  'code'=>$code,
              ],

              'headers' => [
                  "Accept"=>"application/json"
              ]
          ]
);
        $token =  json_decode($res->getBody()->getContents())->access_token;
        $getGithub = new Client();
        $githubUser = $getGithub->request('GET','https://api.github.com/user',
            [
                'headers' => [
                    'Authorization' => 'token '.$token,
               ]
            ]);
        echo '</pre>';
        $userInfo = json_decode($githubUser->getBody()->getContents());
        var_dump($userInfo);

    }
    //微博登录
    public  function getCode(Request $request)
    {
        $code =  $request->input('code');
        $client = new Client();
        $res = $client->request('POST','https://api.weibo.com/oauth2/access_token',
            [
                'form_params' => [
                    'client_id' => Config('services.weibo')['client_id'],
                    'client_secret' => Config('services.weibo')['client_secret'],
                    'redirect_uri' => Config('services.weibo')['redirect'],
                    'code' => $code,
                ],
            ]);
       $response = json_decode($res->getBody()->getContents());

       dd($this->getWeiboInfo($response));

    }
    /**
     * @param $info
     * @return mixed
     * {#318 ▼
    +"access_token": "2.00YyA6QB02nrU80e2849b2b2bLmA2C"
    +"remind_in": "157679999"
    +"expires_in": 157679999
    +"uid": "1158516162"
    +"isRealName": "true"
    }
     */
    //返回微博登录用户信息
    public  function getWeiboInfo($info)
    {
        $token = $info->access_token;
        $uid   = $info->uid;
        if(empty($token) || empty($uid)) {
            return '参数有误！';
        }
        $api = 'https://api.weibo.com/2/users/show.json';
        $client = new Client();
        $res = $client->request('GET',$api,[
            'query' => [
                'access_token' => $token,
                'uid' => $uid,
            ]
        ]);
        $weiboToken =  \GuzzleHttp\json_decode($res->getBody());
        return $weiboToken;

    }
    //发送验证码
    public function mobile()
    {
        $mobile = '17538386243';
        $code = random_int(000000,999999);
        $use = User::where('id',2)->get();
        Cache::store('redis')->put('code', $use, 600);
        dd(Cache::store('redis')->get('code'));
        $content = "登录验证码：{$code}，如非本人操作，请忽略此短信。";
        $appkey = 'f39dcfbdcb6faa6937bea28973cfef08';
        $client = new Client();
        $url = 'http://api01.monyun.cn:7901/sms/v2/std/single_send';
        try{
            $res = $client->request('POST',$url,[
                'form_params' => [
                    'content' => iconv('UTF-8','GBK',$content),
                    'mobile' => $mobile,
                    'apikey' => $appkey,
                ],

            ]);
        } catch (Exception $e)
        {
            print_r($e->getMessage());  //输出捕获的异常消息
        }

        dd(json_decode($res->getBody()->getContents()));


    }
    public function response(Request $request)
    {
        $newUser = new User();
        $newUser->name = iconv('GBK','UTF-8',($request->input('content')));
        $newUser->github_name = $request->input('name');
        $newUser->email = '@@@';
        $newUser->password = md5('1291752135');
        $newUser->save();
         response()->json( ['content'=>(iconv('GBK','UTF-8',($request->input('content')))),'mobile'=>$request->except('content'),'name'=>(($request->input('name'))) ]);
    }
}
