<?php
/**
 * Author: zhujun
 * DateTime: 2018/4/20 11:18
 */

namespace app\api\validate;


class Banner extends BaseValidate
{
    protected $rule = [
        'in' => 'in:0'
    ];
}