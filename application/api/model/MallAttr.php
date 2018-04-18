<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/10
 * Time: 15:35
 */

namespace app\api\model;


class MallAttr extends BaseModel
{
    protected $visible = ['attr_id','attr_name','attr_price','attr_image','goods_number'];

    public function getAttrImageAttr($value,$data)
    {
        return self::returnImageAttr($value,$data['from']);
    }
}