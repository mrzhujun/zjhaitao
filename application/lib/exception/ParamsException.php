<?php
/**
 * Author: zhujun
 * DateTime: 2018/4/20 10:20
 */

namespace app\lib\exception;

/**
 * Class ParamsException
 * 通用参数类异常错误
 */
class ParamsException extends BaseException
{
    public $code = 400;
    public $errorCode = 10000;
    public $msg = "invalid parameters";

}