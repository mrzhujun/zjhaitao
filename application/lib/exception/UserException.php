<?php
/**
 * Author: zhujun
 * DateTime: 2018/4/16 18:29
 */

namespace app\lib\exception;


class UserException extends BaseException
{
    public $code = 404;
    public $message = '当前用户不存在';
}