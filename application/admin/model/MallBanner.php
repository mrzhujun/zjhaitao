<?php

namespace app\admin\model;

use think\Model;

class MallBanner extends Model
{
    // 表名
    protected $name = 'mall_banner';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    
    // 追加属性
    protected $append = [
        'banner_in_text',
        'is_special_text'
    ];
    

    
    public function getBannerInList()
    {
        return ['0' => __('Banner_in 0')];
    }     

    public function getIsSpecialList()
    {
        return ['0' => __('Is_special 0'),'1' => __('Is_special 1')];
    }     


    public function getBannerInTextAttr($value, $data)
    {        
        $value = $value ? $value : $data['banner_in'];
        $list = $this->getBannerInList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsSpecialTextAttr($value, $data)
    {        
        $value = $value ? $value : $data['is_special'];
        $list = $this->getIsSpecialList();
        return isset($list[$value]) ? $list[$value] : '';
    }




}
