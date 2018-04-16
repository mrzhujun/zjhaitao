<?php


namespace app\api\controller;


use app\common\controller\Api;


/**
 * swagger: 活动
 */
class Active extends Api
{
    // 无需验证登录的方法
    protected $noNeedLogin = ['*'];
    // 无需要判断权限规则的方法
    protected $noNeedRight = ['*'];


}