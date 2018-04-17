<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/10
 * Time: 10:18
 */

namespace app\api\model;


class MallGoods extends BaseModel
{
    protected $hidden = ['add_time','from'];

    public function mallattrs()
    {
        return $this->hasMany('MallAttr','goods_id','goods_id');
    }

    public function getGoodsDescAttr($value)
    {
        return self::returnContentAttr($value);
    }

    public function getGoodsImagesAttr($value,$data)
    {
        $arr = explode(',',$value);
        if ($data['from'] == 0) {
            foreach ($arr as $k => $v){
                $arr[$k] = add_url($v);
            }
        }
        return $arr;
    }



    /**
     * 返回商品活动信息
     * @param $goods_id
     * @return mixed
     * @throws \think\exception\DbException
     */
    public static function active($goods_id)
    {
        $goodsObj = self::get($goods_id);
        if ($goodsObj->active_id == 0 || $goodsObj->promote_start_time>time() || $goodsObj->promote_end_time<time())
        {
            $return['msg'] = '该商品不在活动中';
            $return['status'] = false;
            return $return;
        }
        $return['msg'] = '该商品正在活动中';
        $return['status'] = true;
        $return['promote_price'] = $goodsObj->promote_price;
        $return['promote_end_time'] = $goodsObj->promote_end_time;
        $activeObj = MallActive::get($goodsObj->active_id);
        switch ($activeObj->active_type){
            case 0:
                $return['active_name'] = '秒杀';
                break;
            case 1:
                $return['active_name'] = '新人专享';
                break;
            case 3:
                $return['active_name'] = '限时特卖';
                break;
        }
        $return['active_type'] = $activeObj->active_type;
        return $return;
    }

    public function checkGoodsStock($goods_id,$num)
    {
        if (self::get($goods_id)->goods_count>=$num) {
            return true;
        }else{
            return false;
        }
    }
}