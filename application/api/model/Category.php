<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/9
 * Time: 16:40
 */

namespace app\api\model;


class Category extends BaseModel
{
    protected $visible = ['id','name','image','big_image','goodss','description'];

    public function getImageAttr($value,$data)
    {
        return self::returnImageAttr($value,$data['image_from']);
    }
    public function getBigImageAttr($value,$data)
    {
        return self::returnImageAttr($value,$data['big_image_from']);
    }

    public function goodss()
    {
        return $this->hasMany('MallGoods','cat_id','id')->field('goods_id,goods_name,goods_images,shop_price,sell_count,cat_id,from');
    }
}