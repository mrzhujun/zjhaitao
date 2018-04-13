<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/10
 * Time: 10:18
 */

namespace app\api\model;


use think\Model;

class MallGoods extends Model
{
    protected $visible = ['goods_id','goods_name','goods_brief','goods_desc','cat_id','brand_id','shop_price','goods_images','sell_count','is_onsale'];

    public function mallattrs()
    {
        return $this->hasMany('MallAttr','goods_id','goods_id');
    }

    public function getGoodsDescAttr($value)
    {
        return str_replace('/uploads/',config('setting.img_prefix').'/uploads/',$value);
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