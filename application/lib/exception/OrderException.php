<?php
/**
 * Author: zhujun
 * DateTime: 2018/4/17 18:05
 */

namespace app\lib\exception;


use think\Exception;

class OrderException extends Exception
{
    public $code = 0;
    public $message = '商品库存不足';
}