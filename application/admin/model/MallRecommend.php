<?php

namespace app\admin\model;

use think\Model;

class MallRecommend extends Model
{
    // 表名
    protected $name = 'mall_recommend';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    
    // 追加属性
    protected $append = [
        'from_text',
        'size_text'
    ];
    

    
    public function getFromList()
    {
        return ['0' => __('From 0'),'1' => __('From 1')];
    }     

    public function getSizeList()
    {
        return ['0' => __('Size 0'),'1' => __('Size 1')];
    }     


    public function getFromTextAttr($value, $data)
    {        
        $value = $value ? $value : $data['from'];
        $list = $this->getFromList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getSizeTextAttr($value, $data)
    {        
        $value = $value ? $value : $data['size'];
        $list = $this->getSizeList();
        return isset($list[$value]) ? $list[$value] : '';
    }




}
