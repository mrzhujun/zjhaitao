<?php
/**
 * Author: zhujun
 * DateTime: 2018/4/11 18:03
 */

namespace app\api\model;


class MallArticle extends BaseModel
{
    public function goodss()
    {
        return $this->hasOne('MallGoods','goods_id','goods_id')->field('goods_id,shop_price,goods_images');
    }

    public function getContentAttr($value)
    {
        return self::returnContentAttr($value);
    }

    public function getImageAttr($value,$data)
    {
        return self::returnImageAttr($value,$data['from']);
    }

}