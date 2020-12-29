<?php
namespace App\Http\Utilities;
use GuzzleHttp\Client;
class GaodeMaps
{
    /**
     * 通过真实地址获取对应的经纬度
     * @param $address
     * @param $city
     * @param $state
     * @param $zip
     * @return mixed
     */
    public static function geocodeAddress($address, $city, $state)
    {
        //是否批量获取
        $addressA ='';
        if(is_array($address)) {
            for ($i = 0;$i < count($address)  ;$i++) {
                $addressA .= $state[$i] . $city[$i] . $address[$i] . '|';
            }
        } else {
            // 省、市、区、详细地址
            $addressA = urlencode($state . $city . $address);
        }
        // Web 服务 API Key
        $apiKey = config('services.gaode.ws_api_key');
        // 构建地理编码 API 请求 URL，默认返回 JSON 格式响应
        $url = 'https://restapi.amap.com/v3/geocode/geo?address=' . $addressA . '&key=' . $apiKey .'&batch=true';
        // 创建 Guzzle HTTP 客户端发起请求
        $client = new Client();
        // 发送请求并获取响应数据
        $geocodeResponse = $client->get($url)->getBody();
        $geocodeData = json_decode($geocodeResponse);

        // 初始化地理编码位置
        $coordinates[0]['lat'] = null;
        $coordinates[0]['lng'] = null;

        // 如果响应数据不为空则解析出经纬度
        if (!empty($geocodeData)
            && $geocodeData->status  // 0 表示失败，1 表示成功
            && isset($geocodeData->geocodes)
            && !empty($geocodeData->geocodes[0]->location)
        ) {
            if( !is_array($address)) {
                    list($latitude, $longitude) = explode(',', $geocodeData->geocodes[0]->location);
                    $coordinates[0]['lat'] = $latitude;  // 经度
                    $coordinates[0]['lng'] = $longitude; // 纬度
            } else {
                foreach ($geocodeData->geocodes as $kev => $value) {
                    list($latitude, $longitude) = explode(',', $value->location);
                    $coordinates[$kev]['lat'] = $latitude;  // 经度
                    $coordinates[$kev]['lng'] = $longitude; // 纬度
                }
            }
        }
        // 返回地理编码位置数据
        return $coordinates;
    }

    /**
     * 返回两个坐标的货车行驶距离
     */
    public static function distance($origin,$destination,$sites)
    {
        // Web 服务 API Key
        $apiKey = config('services.gaode.ws_api_key');
        $waypoints = '';

        //拼凑waypoints字符串 文档https://lbs.amap.com/api/webservice/guide/api/direction#t9
        if(!empty($sites)) {
            foreach ($sites as $key => $value) {
                $waypoints .= $value['lat'] . ',' . $value['lng'] . ';';
            }
        }
        $api = 'https://restapi.amap.com/v4/direction/truck';
        $client = new Client();
        $res = $client->request('GET',$api,[
            'query' => [
                'key' => $apiKey,
                'origin'=> $origin,//起点坐标
                'destination' => $destination,//终点坐标
                'strategy' => 10,//计算方式
                'size' => 1,//车辆类型
                'waypoints' => $waypoints,//途经点
            ]
        ]);
//        dd(json_decode($res->getBody()));
        $res =  json_decode($res->getBody());
        return $res;

    }
}
