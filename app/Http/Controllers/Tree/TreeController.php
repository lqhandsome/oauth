<?php

namespace App\Http\Controllers\Tree;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Mockery\Exception;

class TreeController extends Controller
{
    public function index()
    {
//     return $this->manyNums('1122336554545445');
//        return $this->countPrimes(8); //统计小于某个数的质数个数之和
//        return $this->Kahn(); //确定代码源文件的编译依赖关系
//        return $this->insertSort();//插入排序
        /*
        $a = sprintf("%b",97);
        dd($a);
        $a= sprintf("%ss",'sprintf');
        echo $a;
        die();*/
        $arr = [3,1,2,4,66,32,45,10];
        return trim("aabcdaba",'abc');
       $arr =  [1,6142,8192,10239];
        $target = 18431;
        dd($this->twoSum($arr,$target));
        $this->quickSort($arr,0,7);
        print_r($arr);
        return $arr;

    }

    function twoSum($nums, $target) {
        $res = [];
        for($i=0;$i<count($nums);$i++){

            $tmp = $target - $nums[$i];
            if (isset($res[$tmp])){
                return [$res[$tmp],$i];
            }
            $res[$nums[$i]] = $i;
        }
        return [];
    }
    /**
     * @param $nums
     * @return array
     * 找出出现最多的数
     * O(N)
     */
    public function manyNums($nums)
    {
        $arr = [];
        $key = [];
        $maxLeng = 0;
        $str  = is_array($nums)? count($nums):strlen($nums);
        for ($i = 0;$i < $str;$i++){
            if (!isset($arr[$nums[$i]])){
                $arr[$nums[$i]] = 0;
            }
            $arr[$nums[$i]]++;
            if ($arr[$nums[$i]] >= $maxLeng){
                $maxLeng = $arr[$nums[$i]];
                $key = $nums[$i];
            }
        }
        return $key;
    }

    /**
     * @param $n
     * @return int
     * 统计小于某个数的质数个数之和
     */
    protected function countPrimes($n)
    {
        $num = 0;
        $arr = [];
        for ($i=0;$i<$n;$i++){
            $arr[$i] = 1;
        }
        for ($i = 2;$i < $n;$i++){
            if ($arr[$i]){
                $num++;
                //如果某个数是质数那么他的倍数肯定不是质数,例如5是质数 5,10,15,20, ...,5 * n 肯定不是质数
                for ($j = $i *2;$j < $n;$j+=$i){
                    $arr[$j] = false;
                }
            }
        }
        return $num;
    }
    /**
     * 确定代码源文件的编译依赖关系
     */
    protected function Kahn()
    {
        //2,3依赖于1,1要比2,3先输出,如果某个定点的入度为0(没有指向这个顶点的)那么这个顶点就可以输出了
        $arr = [
            '1' => ['2', '3'],
            '2' => ['4'],
            '3' => ['4', '6'],
            '4' => ['8', '5'],
            '7' => ['3'],
            '8' => ['9'],
            '6' => [],
            '5' => ['6'],
            '9' => ['5'],
        ];
        //初始化入度默认为0
        $v = [1=>0,0,0,0,0,0,0,0,0];
        //判断顶点的入度,指向他的个数例如这里面的7入度为0,没有人指向他,他不依赖于任何人
        for ($i = 1 ;$i <= count($arr);$i++){
            for ($j = 0;$j < count($arr[$i]);$j++){
                $v[$arr[$i][$j]]++;
            }
        }

        //将入度为0的压入数组
        $queue = [];
        for ($i =1;$i<count($v);$i++){
            if($v[$i] == 0){
                array_push($queue,$i);
                unset($v[$i]);
            }
        }
        while(count($queue)){
            $tmp = array_shift($queue);
            echo $tmp . '-';
            for ($j = 0;$j < count($arr[$tmp]);$j++){
                $v[$arr[$tmp][$j]]--;
                if ($v[$arr[$tmp][$j]] == 0) $queue[] = $arr[$tmp][$j];
            }
        }
    }

    /**
     * @return array
     * 插入排序
     */
    protected function insertSort()
    {
        $arr = [3,1,2,4,66,32,45,10];
        $len = count($arr);
        for ($i =1;$i <$len;$i++){
            $tmp = $arr[$i];
            for ($j = $i;$j > 0 && $arr[$j -1] > $tmp;$j--){
                $arr[$j] = $arr[$j -1];
            }
            $arr[$j] = $tmp;
        }
        return $arr;
    }

    /**
     * @param $arr
     * @param $l = 0
     * @param $n = count() -1
     * 快速排序
     */
    function quickSort(&$arr,$l,$n)
    {
        if ($l > $n) return;
        $low = $l;
        $high = $n;
        $pivot = $arr[$l];//从左边选主元
        while($low != $high){
            while ($low < $high && $arr[$high] >= $pivot){
                $high--;
            }
            while ($low < $high && $arr[$low] <= $pivot){
                $low++;
            }
            if ($low < $high){
                $tmp = $arr[$low];
                $arr[$low] = $arr[$high];
                $arr[$high] = $tmp;
            }
        }
        $arr[$l] = $arr[$low];
        $arr[$low] = $pivot;
        $this->quickSort($arr,$l,$low -1);
        $this->quickSort($arr,$l+1,$n);
     }
}

