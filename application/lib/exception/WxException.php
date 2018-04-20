<?php
/**
 * Author: zhujun
 * DateTime: 2018/4/20 16:49
 */

namespace app\lib\exception;


class WxException extends BaseException
{
    public $code = 422;
    public $msg = '微信服务器获取信息失败';
    public $errorCode = 10007;
}