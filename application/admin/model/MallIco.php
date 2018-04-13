<?php

namespace app\admin\model;

use think\Model;

class MallIco extends Model
{
    // 表名
    protected $name = 'mall_ico';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    
    // 追加属性
    protected $append = [
        'ico_type_text'
    ];
    

    
    public function getIcoTypeList()
    {
        return ['0' => __('Ico_type 0'),'1' => __('Ico_type 1'),'2' => __('Ico_type 2')];
    }     


    public function getIcoTypeTextAttr($value, $data)
    {        
        $value = $value ? $value : $data['ico_type'];
        $list = $this->getIcoTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }




}
