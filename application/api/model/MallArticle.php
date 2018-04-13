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
}