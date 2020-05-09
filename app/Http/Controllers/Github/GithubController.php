<?php

namespace App\Http\Controllers\Github;

use App\Jobs\FirstProcess;
use Cache;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Mockery\Exception;
use Socialite;
use GuzzleHttp\Client;
use App\User;

class GithubController extends Controller
{
    private $GITHUB_CLIENT_ID = 'e0dcea40a90ff58b7ce3';
    private $GITHUB_SECRET = 'f4cc13c89cceab9e9917370fbbf2586c33744320';
    //返回github Code
    public function getToken(Request $request)
    {
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
          ]);
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
        $weiboInfo = $this->getWeiboInfo($response);
        $weiboUser = User::where('weibo_id',$weiboInfo->id)->first();
        if($weiboUser == null) {
            $weiboUser = new User();
            $weiboUser->weibo_id = intval($weiboInfo->id);
            $weiboUser->email = '';
            $weiboUser->password = '';
            $weiboUser->weibo_name = $weiboInfo->name;
            $weiboUser->name = $weiboInfo->name;
            $weiboUser->weibo_image = $weiboInfo->profile_image_url;
            $weiboUser->weibo_description = $weiboInfo->description;
            $weiboUser->save();
        }
        Auth::login($weiboUser);
        //写入登录日志
        $pams = [
            'ip' => $request->ip(),
            'login_time' => time(),
            'name' => $weiboInfo->name,
            'uid' => $weiboUser->id,
            'type' => 'weibo',
        ];
        FirstProcess::dispatch($pams);
        return redirect('/');
    }
    /**
     * @param $info
     * @return mixed
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
        $weiboToken =  json_decode($res->getBody());
        return $weiboToken;

    }
    //发送验证码
    public function mobile(Request $request)
    {
        $mobile = $request->input('mobile');
        $code = random_int(100000,999999);
         Cache::store('redis')->set($mobile, $code, 300);
        $content = "登录验证码：{$code}，如非本人操作，请忽略此短信。";
        $appkey = 'f39dcfbdcb6faa6937bea28973cfef08';
        $client = new Client();
        $url = 'http://api01.monyun.cn:7901/sms/v2/std/single_send';
//        /*
        try{
            $res = $client->request('POST',$url,[
                'form_params' => [
                            'content' => iconv('UTF-8','GBK',$content),
                            'mobile' => $mobile,
                            'apikey' => $appkey,
                ],
            ]);
        } catch (Exception $e) {
            print_r($e->getMessage());  //输出捕获的异常消息
        }
        if(json_decode($res->getBody()->getContents())->result == 0) {
            return  response()->json(['message'=> 'success'],200);
        }

        return response()->json(['message'=>'success'],200);
    }
    //验证码注册/登录
    public function codeLogin(Request $request)
    {
        $mobile = $request->input('mobile');
        $code= $request->input('code');
        if(empty($mobile) || empty($code)) {
            return response()->json(['code'=>'error','message'=>'参数有误']);
        }
        if($code != Cache::store('redis')->get($mobile)) {
            return response()->json(['code'=>'error','message'=>'验证码错误']);
        }
        $user = User::where('mobile',$mobile)->first();
        if($user == null ) {
            $newUser = new User();
            $newUser->name = $mobile;
            $newUser->mobile = (int)$mobile;
            $newUser->email = '';
            $newUser->password = '';
            $newUser->save();
            $user = $newUser;
        }
        Auth::login($user);
        $pams = [
            'ip' => $request->ip(),
            'login_time' => time(),
            'name' => $user->name,
            'uid' => $user->id,
            'type' => 'mobileCode'
        ];
        FirstProcess::dispatch($pams);
        return redirect('/');
//        return response()->json(['code' => 'success','message' => '登录成功']);
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
