<?php
/**
 * Author: zhujun
 * DateTime: 2018/4/20 11:04
 */

namespace app\lib\exception;



class NoChangeException extends BaseException
{
    public $code = 400;
    public $msg = '数据没有改变';
    public $errorCode = 10006;

}