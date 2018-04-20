<?php
/**
 * Author: zhujun
 * DateTime: 2018/4/20 11:15
 */

namespace app\lib\exception;


/**
 * 404时抛出此异常
 */
class MissException extends BaseException
{
    public $code = 404;
    public $msg = '你所请求的资源不存在';
    public $errorCode = 10001;
}