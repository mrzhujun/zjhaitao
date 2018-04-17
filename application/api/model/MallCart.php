<?php
/**
 * Author: zhujun
 * DateTime: 2018/4/17 10:32
 */

namespace app\api\model;


class MallCart extends BaseModel
{
    protected $hidden = ['user_id'];
    public function goods()
    {
        return $this->belongsTo('MallGoods','goods_id','goods_id');
    }

    public function attr()
    {
        return $this->belongsTo('MallAttr','spec_id','attr_id');
    }

    public static function oneDetail($goods_id,$spec_id)
    {
        $goodsDetail = MallGoods::get($goods_id)->toArray();
        if ($goodsDetail['promote_start_time'] < time() && $goodsDetail['promote_end_time'] > time()) {
            $return['price'] = $goodsDetail['promote_price'];
        }else{
            $return['price'] = $goodsDetail['shop_price'];
        }

        $return['goods_name'] = $goodsDetail['goods_name'];
        $return['goods_image'] = $goodsDetail['goods_images'][0];
        if ($spec_id) {
            $specDetail = MallAttr::get($spec_id)->toArray();
            $return['spec'] = $specDetail['attr_name'];
        }
        return $return;
    }

}