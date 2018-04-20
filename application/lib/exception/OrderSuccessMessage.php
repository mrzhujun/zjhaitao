<?php
/**
 * Author: zhujun
 * DateTime: 2018/4/20 12:02
 */

namespace app\lib\exception;

/**
 * 订单创建成功，返回订单号
 */
class OrderSuccessMessage extends BaseException
{
    public $code = 201;
    public $msg = '订单创建成功';
    public $errorCode = 0;
    public $order_num;
}