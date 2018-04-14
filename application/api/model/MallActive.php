<?php
/**
 * Author: zhujun
 * DateTime: 2018/4/12 16:53
 */

namespace app\api\model;



class MallActive extends BaseModel
{
    protected $visible = ['active_id','goods_id','active_title','active_image'];
    public function goodss()
    {
        return $this->hasMany('MallGoods','goods_id','goods_id');
    }

   public static function getGoodsById($goods_id)
   {
       $goods_list = self::with('goodss')->select($goods_id);
       return $goods_list;
   }

   public function getActiveImageAttr($value,$data)
   {
       return self::returnImageAttr($value,$data['from']);
   }

   //专场获取商品列表
   public static function get_goods_list($goods_id_list)
   {
       $arr = MallGoods::get($goods_id_list);
       dump($arr->getData());
   }

}