<?php

namespace App\Http\Controllers\Freight;

use App\Http\Controllers\Controller;
use App\Http\Model\loginLogs;
use App\Imports\TestImportA;
use Illuminate\Http\Request;
use App\Http\Utilities\GaodeMaps;
use App\Http\Model\storeAddress;
use GuzzleHttp\Client;
use Excel;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Mockery\Exception;
use phpDocumentor\Reflection\Types\Boolean;
use App\Exports\InvoicesExport;
use App\Exports\TestExport;
use App\Exports\TestImportAA;
use Validator;
use App\Http\Model\gwy;
use DB;
class FreightDistance extends Controller
{
    protected  $allPoints;
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 计算两点之间的距离可以添加途经点
     */
    public function responseFreight(Request $request)
    {
//        dd(1);
        $address = $request->input('address');
        $city = $request->input('city');
        $state = $request->input('state');
        $addressTwo = $request->input('addressTwo','');
        $cityTwo = $request->input('cityTwo');
        $stateTwo = $request->input('stateTwo');
        $weights = $request->input('weights',[]);
        $waypoints = $addressTwo ;
        //将起点压入数组顶部
        array_unshift($waypoints,$state);
        array_unshift($weights,0);

        //将地址转化为坐标点
        $origin = GaodeMaps::geocodeAddress($waypoints, $city, $state);

        $redisKey = md5(json_encode($origin));
        //加入redis 加快速度,减少高德接口调用次数
        if (!$dijkstraArr =  Redis::get($redisKey)) {
            //获取各个坐标点的距离符合需要的数据节后
            $dijkstraArr = json_encode($this->getDistance($origin));
            Redis::setex($redisKey,5000,$dijkstraArr);
        }
        $dijkstraArr = json_decode($dijkstraArr,true);
        $firstArr = $dijkstraArr[0];

        array_multisort(array_column($firstArr,'dist'),SORT_ASC,$firstArr);
        $dijkstraArr[0] = $firstArr;
        $maxDistanceKey = $firstArr[count($firstArr) -1]['tid'];
        $road = $this->minRoad($dijkstraArr,$weights,$maxDistanceKey);
        //格式化路径
        $route= $this->route($road,$origin);
        $originReturn = [];
        $weightsReturn = [];
        foreach ($route as $key => $value) {
            $weightsReturn[$value] = $weights[$value];
            $originReturn[$value] = $origin[$value];
        }
        //起点坐标
        $originString = array_shift($originReturn);
        array_shift($weightsReturn);
        $originString = $originString['lat'] . ',' . $originString['lng'];
        $destinationString = array_pop($originReturn);
        $destinationString = $destinationString['lat'] . ',' . $destinationString['lng'];
        //获取距离
        $res = GaodeMaps::distance($originString, $destinationString, $originReturn);
        //获取是否错误
        if ($res->errcode) {
            $origin = "121.494270,31.371441";
            $distance = 155032;
            $duration = 11580;
            $destination = "120.803635,31.010906";

            //错误
//            return response()->json(['message' => '地点有误请重新输入', 'success' => false]);
        } else {
            //起点
            $origin = $res->data->route->origin;
            $destination = $res->data->route->destination;
            //距离厘米
            $distance = $res->data->route->paths['0']->distance;
            //时间秒
            $duration = $res->data->route->paths[0]->duration;
        }

        return response()->json([
            'distance' => $distance,
            'duration' => $duration,
            'origin' => explode(',', $origin),
            'destination' => explode(',', $destination),
            'sites' => $originReturn,
            'success' => true,
            'weightsReturn' => $weightsReturn
        ]);
    }

    /**
     * @param $arr
     * @return array
     * 用来还原路径
     */
    private function route($arr)
    {
        $route = [];
        $tmpArr = $arr;
        foreach ($tmpArr as $key => $value) {
            if ($value == 0) {
                $route[] = $key;
            } else {
                $searchKey = array_search($value, $route);
                array_splice($route, $searchKey + 1, 0, $key);
            }
        }
        array_unshift($route,0);
        return $route;
    }
    /**
     * @param $arr
     * @return mixed
     */
    /*
     * array:3 [
          0 => array:2 [
            "lat" => "120.333875"
            "lng" => "31.622461"
          ]
          1 => array:2 [
            "lat" => "120.838221"
            "lng" => "31.019899"
          ]
          2 => array:2 [
            "lat" => "120.838221"
            "lng" => "31.019899"
          ]
        ]
            $tu = [
            0 => [['sid' => 0,'tid' => 1,'dist' => 200],['sid' => 0,'tid' => 2,'dist' => 300],['sid' => 0,'tid' => 3,'dist' => 400]],
            1 => [['sid' => 1,'tid' => 0,'dist' => 200],['sid' => 1,'tid' => 2,'dist' => 50],['sid' => 1,'tid' => 3,'dist' => 120]],
            2 => [['sid' => 2,'tid' => 0,'dist' => 300],['sid' => 2,'tid' => 1,'dist' => 200],['sid' => 2,'tid' => 3,'dist' => 60],],
            3 => [['sid' => 3,'tid' => 0,'dist' => 400],['sid' => 3,'tid' => 1,'dist' => 450],['sid' => 3,'tid' => 2,'dist' => 450]]
        ];
     */
    private  function getDistance($arr)
    {
        //根据经纬度两个地方获取货车的形式距离
        $points = [];
        foreach ($arr as $key => $value){
            $points[$key] = [$value['lat'],$value['lng']];
        }
        $this->allPoints = $points;
        $res = [];
        foreach ($points as $key => $value) {
            $tmp = $points;
            unset($tmp[$key]);
            foreach ($tmp as $k => $v){
                $originString = $value[0] . ',' . $value[1];
                $destinationString = $v[0] . ',' . $v[1];
                sleep(1);
                $distance = GaodeMaps::distance($originString, $destinationString, '');
                try {
                    $res[$key][] = ['sid' => $key,'tid' => $k,'dist' => $distance->data->route->paths[0]->distance];
                } catch (\ErrorException $e) {
                    dd($distance);
                }
            }
        }
        return $res;
    }
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCompanyAddress(Request $request)
    {
        $storeAddresses = storeAddress::all()->toArray();
        $client = new Client();
        $res = $client->request('GET', 'https://mall.yuncut.com/distance/getadress.html',
            [
                'query' => [
                    'sign' => md5('yuncut')
                ],
            ]);
        $res = (string)$res->getBody();

        $userAddress = \GuzzleHttp\json_decode($res);
        foreach ($userAddress->list as $key => $value) {
                break;
        }
        return response()->json(['success' => true, 'data' => ['storeAddress' => $storeAddresses, 'userAddress' => $userAddress]]);
    }
    public function getCompanyInfo()
    {
        try {
            $client = new Client();
            $res =  $client->request('GET', 'http://ecitaxinfo.market.alicloudapi.com/ECICreditCode/GetCreditCodeNew',
                [
                    'query' => [
                        'keyWord' => '嘉兴云切供应链管理有限公司',
                    ],
                    'headers' => [
                        'Authorization' => 'APPCODE ' . 'c91ee21352bb4c22bfde7ff5943896d3'
                    ]
                ]);
        } catch (Exception $e) {
            return $this->splash('success', null,$e->getMessage());
        }
        dd(\GuzzleHttp\json_decode($res->getBody()->getContents(),true));
        dd((string)$res->getBody());
    }
    private  function BFS($map,$start,$end,$totalWeight = 1,$weightTrans = '')
    {
        $BSF = [
            0 => [1,3],
            1 => [0,2,4],
            2 => [1,5],
            3 => [0,4],
            4 => [1,3,5,6],
            5 => [2,4,7],
            6 => [4,7],
            7 => [5,6]
        ];
        $prev = $this->BFS($BSF,0,7);
        dd($prev);
        $this->printPrev($prev,0,7);
        dd($prev);
        if ($start == $end) return false;
        $visited = $map; //访问过的节点设置为true 防止进入循环
        $visited[$start] = true;
        $queue = []; //是一个队列，用来存储已经被访问、但相连的顶点还没有被访问的顶点
//        $queue =  array_merge($queue,$map[$start]);
        $queue =  array_merge($queue,[$start]);
        $prev = []; //走过的路径 key 节点值 value 上一个节点
        for($i = 0 ;$i < count($map);$i++){
            $prev[$i] = -1;
        }
        while (count($queue) != 0) {
            $w = array_shift($queue);
//            dd($w);//3
            //$map[$w] = w定点所连的边
            for ($i = 0 ;$i < count($map[$w]);$i++){
                $q = $map[$w][$i]; //0
                if($visited[$q] !== true) {
                    $prev[$q] = $w;
                    if($q == $end ) {
                        return $prev;
                    }
                }
                $visited[$q] = true;
                array_push($queue,$q);//讲这个节点相连的定点压入队列
            }
        }
        return '$prev';
    }
    public function minRoad($tu,$weight,$maxDistanceKey)
    {
        $weightTotal = array_sum($weight);
//        $tu = [
//            0 => [['sid' => 0,'tid' => 1,'dist' => 200],['sid' => 0,'tid' => 2,'dist' => 300],['sid' => 0,'tid' => 3,'dist' => 400]],
//            1 => [['sid' => 1,'tid' => 0,'dist' => 200],['sid' => 1,'tid' => 2,'dist' => 50],['sid' => 1,'tid' => 3,'dist' => 120]],
//            2 => [['sid' => 2,'tid' => 0,'dist' => 300],['sid' => 2,'tid' => 1,'dist' => 200],['sid' => 2,'tid' => 3,'dist' => 60],],
//            3 => [['sid' => 3,'tid' => 0,'dist' => 400],['sid' => 3,'tid' => 1,'dist' => 450],['sid' => 3,'tid' => 2,'dist' => 450]]
//        ];
//        $vertexDist = array_column($vertexD,'dist');
        $vertexMaxDist = [];
        //判断是否进入过队列
        $inqueue =[];
        for ($key = 0;$key <count($tu);$key++) {
            $vertexMaxDist[] = ['id' => $key,'dist' => 900*900];
            $inqueue[$key] = false;
        }
        $vertexMaxDist[0]['dist'] = 0;
        $queue[0] = ['id' => 0,'dist' => 0];
        //用来还原路径
        $predecessor = [];
        $inqueue[0] = true;
        while(count($queue) > 0){
            //取出一个队列中最小的
            $minVertex = array_shift($queue);
            $weightTotal -= $weight[$minVertex['id']];
            if ($minVertex['id'] == $maxDistanceKey) {
                return $predecessor;
            } //终点找到了
            if ($weightTotal == 0) {
                return $predecessor;
            }
            $count = count($tu[$minVertex['id']]);
            for ($i =0;$i < $count;$i++) {
                //取出一条相关的边真实的
                $edge = $tu[$minVertex['id']][$i];
                //这个顶点在事先设置好的数组中的值maxInt

                try {
                    $nextVertex = $vertexMaxDist[$edge['tid']];
                } catch (\ErrorException $exception) {
                    return [$vertexMaxDist,$edge['tid']];
                }

                if ($minVertex['dist'] + $edge['dist']  <  $nextVertex['dist'] ){
                    $vertexMaxDist[$edge['tid']]['dist'] = $edge['dist'] +$minVertex['dist'];
                    $nextVertex['dist'] = $edge['dist'] +$minVertex['dist'];
                    $predecessor[$edge['tid']] = $minVertex['id'];
                    if ($inqueue[$edge['tid']] == true) {
                        //如果存在过则更新队列中$nextVertex这个定点的值
                       $queue =  $this->updateHeap($queue,$nextVertex,$inqueue);
                    } else {
                        //不存在把这个定点标记加入
                        $inqueue[$edge['tid']] = true;
                        $queue = $this->addHeap($queue,$nextVertex);
                    }
                }
            }
        }
        return $predecessor;
    }
    //打印BFS路径0,7
    protected function printPrev($arr,$s,$t)
    {
        if($arr[$t] != -1 && $t != $s){
            $this->printPrev($arr,$s,$arr[$t]);
        }
        echo $t .'-';
    }

    /**忘堆中新增一个元素
     * @param $arr
     * @param $insert
     * @return mixed
     */
    private function addHeap($arr = [],$insert = '')
    {

        if (!empty($insert)) array_push($arr,$insert);
        //根据dist建立最小堆
        $dist = array_column($arr,'dist');
        //增加哨兵
        array_unshift($dist,-1);
        $distSort = $this->createHeap($dist,count($dist) -1);
        array_shift($distSort);
        $res = [];
        foreach ($arr as $key => $value) {
            $k = array_search($value['dist'],$distSort);
            if (!isset($value['id'])) dd($arr);
            $res[$k] = $value;
        }
        ksort($res);
        return $res;
    }

    /**
     * @param $arr
     * @param $n
     * @return mixed
     */
    private  function  updateHeap($arr,$update,$inqueue)
    {

//        if (!isset($arr[$update['id']]['id'])) dd($arr);
        $arr[$update['id']]['dist'] = $update['dist'];
        $arr[$update['id']]['id'] = $update['id'];
//        dd($arr);
        return $this->addHeap($arr,[]);
    }
    //建堆
    private function createHeap($arr,$n)
    {
        //$i是需要堆化的第几个元素 第一个($arr[0])不堆化 (>>1)
        /*
         * 因为叶子节点往下堆化只能自己跟自己比较，所以我们直接从最后一个非叶子节点开始，依次堆化就行了。
         */
        for ($i = $n >>1;$i >= 1;$i--){
            $this->heapify($arr,$n,$i);
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
            if($i << 1 <=$n && $arr[$i] > $arr[$i * 2] ){
                $maxPos = $i *2;
            }
            if(($i << 1) +1 <= $n && $arr[$maxPos] > $arr[$i * 2 +1]){
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
    //插入排序算法
    private function mixDui($arr)
    {
        for ($i =1;$i < count($arr);$i++){
            $tmp = $arr[$i];
            for ($j = $i;$j >0 && $arr[$j -1] > $tmp;$j--){
                $arr[$j] = $arr[$j-1];
            }
            $arr[$j] = $tmp;
        }
        return $arr;
    }

}

