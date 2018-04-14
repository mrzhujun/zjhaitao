<?php

namespace app\admin\model;

use think\Model;

class MallArticle extends Model
{
    // 表名
    protected $name = 'mall_article';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    
    // 追加属性
    protected $append = [
        'from2_text',
        'from_text'
    ];
    

    
    public function getFrom2List()
    {
        return ['0' => __('From2 0'),'1' => __('From2 1')];
    }     

    public function getFromList()
    {
        return ['0' => __('From 0'),'1' => __('From 1')];
    }     


    public function getFrom2TextAttr($value, $data)
    {        
        $value = $value ? $value : $data['from2'];
        $list = $this->getFrom2List();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getFromTextAttr($value, $data)
    {        
        $value = $value ? $value : $data['from'];
        $list = $this->getFromList();
        return isset($list[$value]) ? $list[$value] : '';
    }




}
