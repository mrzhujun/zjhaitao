<?php
/**
 * Author: zhujun
 * DateTime: 2018/4/19 10:41
 */

namespace app\api\validate;


class User extends BaseValidate
{
    protected $rule = [
      'user_id' => 'require|isPositiveInreger',
    ];

}