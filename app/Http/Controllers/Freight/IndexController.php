<?php

namespace App\Http\Controllers\Freight;

use App\Http\Controllers\Controller;
use App\Http\Model\loginLogs;
use Illuminate\Http\Request;
use App\Http\Utilities\GaodeMaps;
use App\Http\Model\storeAddress;
use GuzzleHttp\Client;

class IndexController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 计算两点之间的距离可以添加途经点
     */
    public function responseFreight (Request $request)
    {
        $address = $request->input('address');
        $city = $request->input('city');
        $state = $request->input('state');
        $addressTwo = $request->input('addressTwo');
        $cityTwo = $request->input('cityTwo');
        $stateTwo = $request->input('stateTwo');
        $siteState = $request->input('siteState');
        $siteCity = $request->input('siteCity');
        $siteAddress = $request->input('siteAddress');
        //途经点坐标
        $sites ='';
        if(isset($siteAddress[0]) ) {
            $sites = GaodeMaps::geocodeAddress($siteAddress,$siteCity,$siteState);
        }
        //起点坐标
        $origin = GaodeMaps::geocodeAddress($address, $city, $state);
        //终点坐标
        $destination = GaodeMaps::geocodeAddress($addressTwo, $cityTwo, $stateTwo);
        $originString = $origin[0]['lat'] .','.$origin[0]['lng'];
        $destinationString = $destination[0]['lat'] .','.$destination[0]['lng'];
        //获取距离
        $res = GaodeMaps::distance($originString,$destinationString,$sites);
        if($res->errcode){
            //错误
            return response()->json(['message' => '地点有误请重新输入','success' => false]);
        }
        //起点
        $origin = $res->data->route->origin;
        $destination = $res->data->route->destination;
        //距离厘米
        $distance = $res->data->route->paths['0']->distance;
        //时间秒
        $duration = $res->data->route->paths[0]->duration;
        return response()->json([
            'distance' => $distance,
            'duration' => $duration,
            'origin' => explode(',',$origin),
            'destination' => explode(',',$destination),
            'sites' =>$sites,
            'success' => true
        ]);
    }
    /**
     * 返回供应商和用户的收货地址
     */
    public  function  getAddress ()
    {
        $storeAddresses  = storeAddress::all()->toArray();
        $client = new Client();
       $res =  $client->request('GET', 'https://mall.yuncut.com/distance/getadress.html',
            [
            'query' => [
                'sign' => md5('yuncut')
                ]
            ]);
        $res =  (string)$res->getBody();

        $userAddress = \GuzzleHttp\json_decode($res);
        foreach ($userAddress->list as $key => $value) {

        }
        return response()->json(['success' => true,'data' => ['storeAddress' => $storeAddresses , 'userAddress' => $userAddress ] ]);
    }

    public function getIp(Request $request)
    {
//        $ip = $request->ip();
//        $login = new loginLogs();
//        $login->ip = $ip;
//        $login->name = $request->header('user-agent');
//        $login->login_time = time();
//        $login->uid =  '2';
//        $login->type =  '1';
//        $check = $login->save();
//        return "已经获取到你的IP并保存,你的IP是：".$ip;
//        $arr = [1,2,2,5,6,6,7,7,8,9];
        $arr = [0,7,5,19,8,4,1,20,13,16];
        $n = 9;
        dd($this->heapSort($arr,$n));
    }
    //建堆
    private function createHeap($arr,$n)
    {
        $num = 0;
        for ($i = $n>>1;$i >= 1;$i--){
                $this->heapify($arr,$n,$i);
        }
        return $arr;

    }
    //堆排序 O(nlogn)
    private  function heapSort($arr,$n)
    {
        $arr = $this->createHeap($arr,$n);
        $i  = $n;
        while ($i >1){
            $tmp = $arr[1];
            $arr[1] = $arr[$i];
            $arr[$i] = $tmp;
            $i--;
//            dd($arr);
            $this->heapify($arr,$i,1);
        }
        return $arr;
    }

    /**
     * @param $arr 数组
     * @param $n 可以多少个节点
     * @param $i 当前的节点位置,从此节点从上往下堆化
     */
    private function heapify(&$arr,$n,$i)
    {
        while(true){

            $maxPos = $i;
            if($i <<1 <=$n && $arr[$i] < $arr[$i * 2] ){
                $maxPos = $i *2;
            }
            if(($i <<1) +1 <= $n && $arr[$maxPos] < $arr[$i * 2 +1]){
                $maxPos = $i * 2 +1;
            }
            if($maxPos == $i){
                break;
            }
            $tmp = $arr[$i];
            $arr[$i] = $arr[$maxPos];
            $arr[$maxPos] = $tmp;
            $i = $maxPos;
        }

    }
    private function  midSort($arr,$int)
    {
        $count = count($arr);
        $low = 0 ;
        $high = $count -1;
        while($low <= $high){
            $mid = $low + (($high - $low) >>1);//取mid
            if($arr[$mid] > $int){
                $high = $mid -1;

            }else if($arr[$mid] < $int){
                $low = $mid +1;
            } else{
//                if($mid == 0 || $arr[$mid -1] != $int) return $mid;  查找第一个等于给定值
//                else $high = $mid -1;
                /**
                 * 查找最后一个等于给定值得元素
                 */
                if($mid == ($count -1) || $arr[$mid +1] != $int) return $mid;
                    else $low = $mid +1;
            }
        }

        return -1 ;

    }
    private function  findFirst($arr,$int)
    {
        $count = count($arr);
        $low = 0 ;
        $high = $count -1;
        while($low <= $high){
            $mid = $low + (($high - $low) >>1);//取mid
            if($arr[$mid] > $int){
//                if($mid ==0 || $arr[$mid -1] < $int) return $mid;
//                else $high = $mid -1;
                $high = $mid-1;

            }else {
                if($mid == $count - 1 || $arr[$mid + 1] > $int) return $mid;
                else $low = $mid + 1;

            }
        }

        return -1 ;

    }
}
