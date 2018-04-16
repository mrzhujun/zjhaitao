<?php
/**
 * Author: zhujun
 * DateTime: 2018/4/16 17:25
 */

namespace app\api\validate;


class Address extends BaseValidate
{
    protected $rule = [
        'phone' => 'require|number|length:11|isNotEmpty',
        'p' => 'require|isNotEmpty',
        'c' => 'require|isNotEmpty',
        't' => 'require|isNotEmpty',
        'address_detail' => 'require|isNotEmpty',
        'is_default' => 'in:0,1'
    ];

}