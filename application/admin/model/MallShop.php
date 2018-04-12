<?php

namespace app\admin\model;

use think\Model;

class MallShop extends Model
{
    // 表名
    protected $name = 'mall_shop';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    
    // 追加属性
    protected $append = [
        'temai_start_time_text',
        'temai_end_time_text'
    ];
    

    



    public function getTemaiStartTimeTextAttr($value, $data)
    {
        $value = $value ? $value : $data['temai_start_time'];
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getTemaiEndTimeTextAttr($value, $data)
    {
        $value = $value ? $value : $data['temai_end_time'];
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    protected function setTemaiStartTimeAttr($value)
    {
        return $value && !is_numeric($value) ? strtotime($value) : $value;
    }

    protected function setTemaiEndTimeAttr($value)
    {
        return $value && !is_numeric($value) ? strtotime($value) : $value;
    }


}
