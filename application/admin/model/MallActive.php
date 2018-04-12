<?php

namespace app\admin\model;

use think\Model;

class MallActive extends Model
{
    // 表名
    protected $name = 'mall_active';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    
    // 追加属性
    protected $append = [
        'active_type_text',
        'start_time_text',
        'end_time_text'
    ];
    

    
    public function getActiveTypeList()
    {
        return ['0' => __('Active_type 0'),'1' => __('Active_type 1'),'2' => __('Active_type 2'),'3' => __('Active_type 3'),'4' => __('Active_type 4')];
    }     


    public function getActiveTypeTextAttr($value, $data)
    {        
        $value = $value ? $value : $data['active_type'];
        $list = $this->getActiveTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getStartTimeTextAttr($value, $data)
    {
        $value = $value ? $value : $data['start_time'];
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getEndTimeTextAttr($value, $data)
    {
        $value = $value ? $value : $data['end_time'];
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    protected function setStartTimeAttr($value)
    {
        return $value && !is_numeric($value) ? strtotime($value) : $value;
    }

    protected function setEndTimeAttr($value)
    {
        return $value && !is_numeric($value) ? strtotime($value) : $value;
    }


}
