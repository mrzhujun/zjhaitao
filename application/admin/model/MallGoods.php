<?php

namespace app\admin\model;

use think\Model;

class MallGoods extends Model
{
    // 表名
    protected $name = 'mall_goods';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    
    // 追加属性
    protected $append = [
        'promote_start_time_text',
        'promote_end_time_text',
        'is_promote_text',
        'is_new_text',
        'is_onsale_text',
        'is_jifen_text',
        'add_time_text'
    ];
    

    
    public function getIsPromoteList()
    {
        return ['0' => __('Is_promote 0'),'1' => __('Is_promote 1')];
    }     

    public function getIsNewList()
    {
        return ['0' => __('Is_new 0'),'1' => __('Is_new 1')];
    }     

    public function getIsOnsaleList()
    {
        return ['0' => __('Is_onsale 0'),'1' => __('Is_onsale 1')];
    }     

    public function getIsJifenList()
    {
        return ['0' => __('Is_jifen 0'),'1' => __('Is_jifen 1')];
    }     


    public function getPromoteStartTimeTextAttr($value, $data)
    {
        $value = $value ? $value : $data['promote_start_time'];
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getPromoteEndTimeTextAttr($value, $data)
    {
        $value = $value ? $value : $data['promote_end_time'];
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getIsPromoteTextAttr($value, $data)
    {        
        $value = $value ? $value : $data['is_promote'];
        $list = $this->getIsPromoteList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsNewTextAttr($value, $data)
    {        
        $value = $value ? $value : $data['is_new'];
        $list = $this->getIsNewList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsOnsaleTextAttr($value, $data)
    {        
        $value = $value ? $value : $data['is_onsale'];
        $list = $this->getIsOnsaleList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsJifenTextAttr($value, $data)
    {        
        $value = $value ? $value : $data['is_jifen'];
        $list = $this->getIsJifenList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getAddTimeTextAttr($value, $data)
    {
        $value = $value ? $value : $data['add_time'];
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    protected function setPromoteStartTimeAttr($value)
    {
        return $value && !is_numeric($value) ? strtotime($value) : $value;
    }

    protected function setPromoteEndTimeAttr($value)
    {
        return $value && !is_numeric($value) ? strtotime($value) : $value;
    }

    protected function setAddTimeAttr($value)
    {
        return $value && !is_numeric($value) ? strtotime($value) : $value;
    }


}
