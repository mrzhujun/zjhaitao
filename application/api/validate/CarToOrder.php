<?php
/**
 * Author: zhujun
 * DateTime: 2018/4/19 9:23
 */

namespace app\api\validate;


class CarToOrder extends BaseValidate
{
    protected $rule = [
        'cart_id_arr' => 'array',
        'coupons_user_id' => 'isPositiveInreger',
        'address_id' => 'require|isPositiveInreger'
    ];
}