<?php
/**
 * Author: zhujun
 * DateTime: 2018/4/12 16:53
 */

namespace app\api\model;


use think\Model;

class MallActive extends Model
{
    public function goodss()
    {
        return $this->hasMany('MallGoods','goods_id');
    }

   public static function getGoodsById($goods_id)
   {
       $goods_list = self::with('goodss')->select($goods_id);
       return $goods_list;
   }

}