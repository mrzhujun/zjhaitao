<?php
/**
 * Author: zhujun
 * DateTime: 2018/4/20 11:48
 */

namespace app\api\validate;


class Goods extends BaseValidate
{
    protected $rule = [
        'goods_id'  => 'require|isPositiveInreger'
    ];

}