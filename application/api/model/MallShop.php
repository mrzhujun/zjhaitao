<?php
/**
 * Author: zhujun
 * DateTime: 2018/4/13 9:27
 */

namespace app\api\model;


use think\Model;

class MallShop extends Model
{
    public function getHeadImageAttr($value)
    {
        return add_url($value);
    }
    public function getXinImageAttr($value)
    {
        return add_url($value);
    }
    public function getTemaiImageAttr($value)
    {
        return add_url($value);
    }

}