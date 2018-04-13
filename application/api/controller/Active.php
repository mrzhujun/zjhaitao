<?php
/**
 * Author: zhujun
 * DateTime: 2018/4/13 9:15
 */

namespace app\api\controller;


use app\common\controller\Api;


/**
 * 活动
 */
class Active extends Api
{
    // 无需验证登录的方法
    protected $noNeedLogin = ['*'];
    // 无需要判断权限规则的方法
    protected $noNeedRight = ['*'];

    /**
     * 首页新人
     * @ApiMethod   (PUT)
     * @ApiParams   (name="address_id", type="integer", required=true, description="地址id")
     * @ApiParams   (name="name", type="string", required=true, description="收货人")
     * @ApiParams   (name="phone", type="integer", required=true, description="手机号")
     * @ApiParams   (name="p", type="string", required=true, description="省")
     * @ApiParams   (name="c", type="string", required=true, description="市")
     * @ApiParams   (name="t", type="string", required=true, description="区")
     * @ApiParams   (name="address_detail", type="string", required=true, description="具体地址")
     * @ApiParams   (name="is_default", type="int", required=true, description="[0/1]是否设为默认")
     */
}