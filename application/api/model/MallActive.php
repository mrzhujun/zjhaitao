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


   public function getActiveImageAttr($value,$data)
   {
       return self::returnImageAttr($value,$data['from']);
   }




}