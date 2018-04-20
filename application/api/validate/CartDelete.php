<?php
/**
 * Author: zhujun
 * DateTime: 2018/4/20 11:32
 */

namespace app\api\validate;


class CartDelete extends BaseValidate
{
    protected $rule = [
        'cart_id' => 'require|isPositiveInreger',
    ];
}