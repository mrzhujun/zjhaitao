<?php
/**
 * Author: zhujun
 * DateTime: 2018/4/20 11:11
 */

namespace app\api\validate;


class Article extends BaseValidate
{
    protected $rule = [
      'article_id'  => 'require|isPositiveInreger'
    ];
}