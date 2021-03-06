<?php
/**
 * Author: zhujun
 * DateTime: 2018/4/16 18:15
 */

namespace app\lib\exception;


class TokenException extends BaseException
{
    public $code = 401;
    public $msg = 'Token已过期或无效Token';
    public $errorCode = 10002;

}