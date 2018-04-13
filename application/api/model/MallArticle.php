<?php
/**
 * Author: zhujun
 * DateTime: 2018/4/11 18:03
 */

namespace app\api\model;


use think\Model;

class MallArticle extends Model
{
    public function goodss()
    {
        return $this->hasOne('MallGoods','goods_id','goods_id')->field('goods_id,shop_price,goods_images');
    }

    public function getContentAttr($value)
    {
        return str_replace('/uploads/',config('setting.img_prefix').'/uploads/',$value);
    }

    public function getImageAttr($value)
    {
        return add_url($value);
    }

}