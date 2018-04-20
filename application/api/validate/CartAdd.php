<?php
/**
 * Author: zhujun
 * DateTime: 2018/4/20 14:45
 */

namespace app\api\validate;


class CartAdd extends BaseValidate
{
    protected $rule = [
        'goods_id' => 'require|isPositiveInreger',
        'spec_id' =>'isPositiveInreger',
        'num' => 'require|isPositiveInreger',
    ];

}