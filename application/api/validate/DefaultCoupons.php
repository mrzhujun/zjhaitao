<?php
/**
 * Author: zhujun
 * DateTime: 2018/4/20 14:51
 */

namespace app\api\validate;


class DefaultCoupons extends BaseValidate
{
    protected $rule = [
        'goods_id' => 'require|isPositiveInreger',
        'spec_id' =>'isPositiveInreger',
        'num' => 'require|isPositiveInreger',
    ];
}