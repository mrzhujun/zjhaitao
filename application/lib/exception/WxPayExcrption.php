<?php
/**
 * Author: zhujun
 * DateTime: 2018/4/19 14:22
 */

namespace app\lib\exception;


class WxPayExcrption extends BaseException
{
    public $code = 400;
    public $msg = 'wechat unknown error';
    public $errorCode = 999;
}