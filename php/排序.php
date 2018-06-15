<?php
/**
 * |--------------------------------------------------------------------------
 * |
 * |--------------------------------------------------------------------------
 * Created by PhpStorm.
 * User: weaving
 * Date: 8/6/2018
 * Time: 11:30 AM
 */
/**
 * @param array $arr
 * @return array
 * 冒泡排序 前一个和后一个进行排序
 */
function bubbleSort( array $arr )
{
    $len = count($arr);
    for ( $i = 1; $i < $len; $i++ ) {
        for ( $j = 0; $j < $len - $i; $j++ ) {
            if ($arr[ $j ] > $arr[ $j + 1 ]) {
                $tmp           = $arr[ $j + 1 ];
                $arr[ $j + 1 ] = $arr[ $j ];
                $arr[ $j ]     = $tmp;
            }
        }
    }
    return $arr;
}


//$arr=array(1,43,54,62,21,66,32,78,36,76,39);
//var_dump(bubbleSort($arr));

/**
 * @param array $arr
 * @return array
 * 选择排序 选择第一个认为最小
 */
function selectSort( array $arr )
{
    $len = count($arr);
    for ( $i = 0; $i < $len; $i++ ) {
        $p = $i;
        for ( $j = $i + 1; $j < $len; $j++ ) {
            if ($arr[ $p ] > $arr[ $j ]) {
                $p = $j;
            }
        }
        if ($p != $i) {
            $tmp       = $arr[ $p ];
            $arr[ $p ] = $arr[ $i ];
            $arr[ $i ] = $tmp;
        }
    }
    return $arr;
}

/**
 * @param array $arr
 * @return array
 * 从第一个元素开始，该元素可以认为已经被排序
 * 取出下一个元素，在已经排序的元素序列中从后向前扫描
 * 如果该元素（已排序）大于新元素，将该元素移到下一位置
 * 重复步骤3，直到找到已排序的元素小于或者等于新元素的位置
 * 将新元素插入到该位置中
 * 重复步骤2
 */
function insertSort( array $arr )
{
    $len = count($arr);
    for ( $i = 1; $i < $len; $i++ ) {
        $tmp = $arr[ $i ];
        for ( $j = $i - 1; $j >= 0; $j-- ) {
            if ($tmp < $arr[ $j ]) {
                $arr[ $j + 1 ] = $arr[ $j ];
                $arr[ $j ]     = $tmp;
            }
            else {
                break;
            }
        }
    }
    return $arr;
}

function quickSort( array $arr )
{
    if (!is_array($arr)) return false;
    $length = count($arr);
    if ($length <= 1) return $arr;
    $left = $right = [];
    for ( $i = 1; $i < $length; $i++ ) {
        if ($arr[ $i ] < $arr[ 0 ]) {
            $left[] = $arr[ $i ];
        }
        else {
            $right[] = $arr[ $i ];
        }
    }
    $left  = quickSort($left);
    $right = quickSort($right);
    return array_merge($left, [ $arr[ 0 ] ], $right);

}

$arr = [ 1, 43, 54, 62, 21, 66, 32, 78, 36, 76, 39, 2, 0.5 ];
var_dump(insertSort($arr));

//https://blog.csdn.net/Aaroun/article/details/79131987lara