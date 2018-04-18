<?php
/**
 * Author: zhujun
 * DateTime: 2018/4/17 16:44
 */

namespace app\lib\exception;


use think\Exception;

class CartException extends Exception
{
    public $code = 400;
    public $message = '不允许修改别人的购物车';

}