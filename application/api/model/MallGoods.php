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
    public function mallattrs()
    {
        return $this->hasMany('MallAttr','goods_id')->field('attr_id,attr_name,attr_price,attr_image,goods_number');
    }
}