<?php
/**
 * Author: zhujun
 * DateTime: 2018/4/20 11:44
 */

namespace app\lib\exception;


/**
 * token验证失败时抛出此异常
 */
class ForbiddenException extends BaseException
{
    public $code = 403;
    public $msg = '非法操作';
    public $errorCode = 10003;
}