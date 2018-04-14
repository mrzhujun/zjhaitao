<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/10
 * Time: 10:18
 */

namespace app\api\model;


class MallGoods extends BaseModel
{
    protected $visible = ['goods_id','goods_name','goods_brief','goods_desc','cat_id','brand_id','shop_price','goods_images','sell_count','is_onsale'];

    public function mallattrs()
    {
        return $this->hasMany('MallAttr','goods_id','goods_id');
    }

    public function getGoodsDescAttr($value)
    {
        return self::returnContentAttr($value);
    }

    public function getGoodsImagesAttr($value)
    {
        $arr = explode(',',$value);
        foreach ($arr as $k => $v){
            $arr[$k] = add_url($v);
        }
        return $arr;
    }
}