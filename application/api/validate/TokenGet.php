<?php
/**
 * Author: zhujun
 * DateTime: 2018/4/16 14:19
 */

namespace app\api\validate;


class TokenGet extends BaseValidate
{
    protected $rule = [
      'code' => 'require|isNotEmpty'
    ];

    protected $message = [
        'code' => '获取token需要code'
    ];

}