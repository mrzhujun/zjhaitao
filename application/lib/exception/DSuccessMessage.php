<?php
/**
 * Author: zhujun
 * DateTime: 2018/4/20 10:37
 */

namespace app\lib\exception;


/**
 * 创建成功（如果不需要返回任何消息）
 * 204 删除成功
 */
class DSuccessMessage extends BaseException
{
    public $code = 204;
    public $msg = 'ok';
    public $errorCode = 0;
}