<?php
/**
 * Author: zhujun
 * DateTime: 2018/4/14 9:25
 */

namespace app\api\model;


use think\Model;

class BaseModel extends Model
{

    //富文本替换
    protected static function returnContentAttr($value)
    {
        return str_replace('/uploads/',config('setting.img_prefix').'/uploads/',$value);
    }
    //添加完整url
    protected static function returnImageAttr($value,$from)
    {
        if ($from === 0) {
            return add_url($value);
        }
        return $value;
    }

}