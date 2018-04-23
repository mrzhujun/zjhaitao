<?php
/**
 * Author: zhujun
 * DateTime: 2018/4/13 9:27
 */

namespace app\api\model;


use think\Model;

class MallShop extends BaseModel
{
    public function getHeadImageAttr($value)
    {
        return self::returnImageAttr($value,0);
    }
    public function getXinImageAttr($value)
    {
        return self::returnImageAttr($value,0);
    }
    public function getTemaiImageAttr($value)
    {
        return self::returnImageAttr($value,0);
    }

    public function getBoutUsContentAttr($value)
    {
        return self::returnContentAttr($value);
    }



}