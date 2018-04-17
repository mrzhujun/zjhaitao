<?php
/**
 * Author: zhujun
 * DateTime: 2018/4/17 10:39
 */

namespace app\api\validate;


class Cart extends BaseValidate
{
    protected $rule = [
        'goods_id' => 'require|isPositiveInreger',
        'spec_id' =>'isPositiveInreger',
        'num' => 'require|isPositiveInreger',
    ];

}