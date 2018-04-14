<?php

namespace app\api\model;

use think\Model;

class MallBanner extends BaseModel
{
    protected $visible = ['banner_image','goods_id','is_special','link'];

    public function getBannerImageAttr($value,$data)
    {
        return self::returnImageAttr($value,$data['from']);
    }
}
