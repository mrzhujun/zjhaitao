<?php
/**
 * Author: zhujun
 * DateTime: 2018/4/17 17:26
 */

namespace app\api\model;


use traits\model\SoftDelete;

class MallOrder extends BaseModel
{
    use SoftDelete;
    protected $autoWriteTimestamp = true;

    protected $deleteTime = 'delete_time';

    protected $hidden = ['create_time','update_time','delete_time','wxpay_order_num','status'];

    public function orderGoodslists()
    {
        return $this->hasMany('MallOrderGoodslist','order_num','order_num');
    }
}