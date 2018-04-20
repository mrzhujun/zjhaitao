<?php
/**
 * Author: zhujun
 * DateTime: 2018/4/20 11:25
 */

namespace app\lib\exception;


class GoodsException extends BaseException
{
    public $code = 404;
    public $msg = '商品不存在';
    public $errorCode = 20000;
}