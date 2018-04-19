<?php
/**
 * Author: zhujun
 * DateTime: 2018/4/18 11:19
 */

namespace app\api\validate;


class ConfirmOrder extends BaseValidate
{
    protected $rule = [
        'goods_id' => 'require|isPositiveInreger',
        'spec_id' =>'isPositiveInreger',
        'num' => 'require|isPositiveInreger',
        'coupons_user_id' => 'isPositiveInreger',
        'address_id' => 'require|isPositiveInreger'
    ];

}