<?php
/**
 * Author: zhujun
 * DateTime: 2018/4/23 14:13
 */

namespace app\api\model;


class MallRecommend extends BaseModel
{
    protected $hidden = ['from','size'];

    public function getImageAttr($value,$data)
    {
        return self::returnImageAttr($value,$data['from']);
    }
}