<?php
/**
 * Author: zhujun
 * DateTime: 2018/4/17 16:18
 */

namespace app\api\service;


use app\api\model\MallAddress;
use app\api\model\MallAttr;
use app\api\model\MallCouponsUser;
use app\api\model\MallGoods;
use app\api\model\MallOrder;
use app\api\model\MallOrderGoodslist;
use app\lib\exception\MissException;
use app\lib\exception\OrderException;
use app\lib\exception\ParamsException;
use think\Exception;

class Order
{
    //生成订单号
    public static function makeOrderNo()
    {
        $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
        $orderSn =
            $yCode[intval(date('Y')) - 2018] . strtoupper(dechex(date('m'))) . date(
                'd') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf(
                '%02d', rand(0, 99));
        return $orderSn;
    }

    /**
     * 生成订单--1
     * @param $address_id
     * @param $comment
     * @return 订单类
     */
    public function neworder($address_id,$comment,$user_id)
    {
        //地址信息
        $addressObj = MallAddress::get($address_id);
        $save['order_num'] = $this::makeOrderNo();
        $save['user_id'] = $user_id;
        $save['rec_name'] = $addressObj->name;
        $save['rec_address'] = $addressObj->address.' '.$addressObj->address_detail;
        $save['rec_phone'] = $addressObj->phone;
        $save['comment'] = $comment;

        $orderModel = new MallOrder($save);

        $rs = $orderModel->save();
        if ($rs) {
            return $orderModel;
        }else{
            throw new Exception('生成订单出错');
        }
    }

    /**
     * 添加商品信息到商品附加表--2
     * @return 订单信息
     */
    public function goodstoorder($order_num,$goods_id,$num,$spec_id=0)
    {
        $goodsObj = MallGoods::get($goods_id);
        $this->checkStockOfGoods($goodsObj,$spec_id,$num);

        if ($goodsObj->goods_count < $num) {
            throw new Exception('商品库存不足');
        }
        $save['goods_count'] = $goodsObj->goods_count-$num;
        $save['order_num'] = $order_num;
        $save['goods_id'] = $goods_id;
        $save['goods_name'] = $goodsObj->goods_name;
        $save['price'] = $this->getUnitPriceOfGoods($goodsObj);
        //如果有活动保存活动信息
        if ($goodsObj->active_id != 0 && $goodsObj->promote_start_time<time() && $goodsObj->promote_end_time>time()) {
            $save['active_off'] =  ($goodsObj->shop_price-$goodsObj->promote_price)*$num;
            $save['active_id'] = $goodsObj->active_id;
        }

        //判断如果有规格id传进来则取规格图片
        if ($spec_id) {
            $specObj = MallAttr::get($spec_id);
            $save['spec_info'] = $specObj->attr_name;
            $save['image'] = $specObj->attr_image;
            if ($specObj->goods_number < $num) {
                throw new Exception('所选商品规格库存不足');
            }
            $save['goods_number'] = $specObj->goods_number-$num;
        }else{
            $save['image'] = explode(',',$goodsObj->getData('goods_images'))[0];
        }

        $save['from'] = $goodsObj->from;
        $save['num'] = $num;

        $MallOrderGoodslist = new MallOrderGoodslist($save);
        $rs = $MallOrderGoodslist->allowField(true)->save();
        if ($rs) {
            return $save['price']*$num;
        }else{
            throw new Exception('添加商品信息出错');
        }
    }


    /**
     * 返回商品单价
     */
    public function getUnitPriceOfGoods($goodsObj,$spec_id=0)
    {
        if ($goodsObj->active_id != 0 && $goodsObj['promote_start_time']<time() && $goodsObj['promote_end_time']>time()) {
            return $goodsObj['promote_price'];
        }else{
            if ($spec_id) {
              $specObj = MallAttr::get($spec_id);
              return $specObj->attr_price;
            }
            return $goodsObj['shop_price'];
        }
    }

    /**
     * 判断商品库存
     */
    public function checkStockOfGoods($goodsObj,$spec_id = 0,$num)
    {
        if ($goodsObj->goods_count < $num) {
            throw new Exception('商品库存不足');
        }
        if ($spec_id != 0) {
            $specObj = MallAttr::get($spec_id);
            if ($specObj->goods_number < $num) {
                throw new Exception('商品该规格的库存不足');
            }
        }
    }

    /**
     * 检查优惠券是否可用--3
     */
    public static function checkCoupons($coupons_user_id,$money)
    {
        $couponsObj = MallCouponsUser::get($coupons_user_id);
        if ($couponsObj->start_time>time() || $couponsObj->end_time<time() || $couponsObj->is_use == 1 || $couponsObj->man>$money) {
            return false;
        }
        return true;
    }

    /**
     * 使用优惠券
     * @param $totalPrice
     * @throws Exception
     * @throws \think\exception\DbException
     */
    public static function selectCoupons($totalPrice)
    {
        if (input('coupons_user_id')) {
            $couponsObj = MallCouponsUser::get(input('coupons_user_id'));
            if (!$couponsObj) {
                throw new MissException([
                    'msg' => '优惠券不存在',
                    'errorCode' => 90000
                ]);
            }
            if ($couponsObj->man > $totalPrice || $couponsObj->is_use == 1) {
                throw new ParamsException([
                    'msg' => '优惠券不可用',
                    'errorCode' => 90001
                ]);
            }
            $couponsObj->is_use = 1;
            $couponsObj->save();

            $return['coupons_off'] = $couponsObj->jian;
            $return['final_price'] = $totalPrice-$couponsObj->jian;
        }else{
            $return['final_price'] = $totalPrice;
            $return['coupons_off'] = 0;
        }
        return $return;
    }

    /**
     * 关闭订单(需要开启事务)
     */
    public static function order_close($order_num)
    {
        $orderObj = MallOrder::get($order_num);
        if ($orderObj->status != 0) {
            throw new OrderException([
                'mag' => '已支付过订单暂不能关闭',
                'errorCode' => 80003
            ]);
        }
        if ($orderObj->coupons_user_id) {
            $couponsUserObj = MallCouponsUser::get($orderObj->coupons_user_id);
            $couponsUserObj -> is_use = 0;
            $couponsUserObj -> save();
        }
        $orderObj->status = 4;
        $orderObj->save();
    }

    /**
     * 取出默认优惠券信息
     */
    public static function default_coupons($user_id,$final_price)
    {
        $userCouponsObj = MallCouponsUser::where('start_time','<',time())
            ->where('end_time','>',time())
            ->where('is_use','=',0)
            ->where('user_id','=',$user_id)
            ->where('man','<',$final_price)
            ->order('jian DESC')
            ->select();
        return $userCouponsObj;
    }


    /**
     * 确认订单页面单个商品信息
     */
    public function confirm_goods_detail($goods_id,$spec_id)
    {
        //商品信息
        $goodsObj = MallGoods::where('goods_id','=',$goods_id)
            ->field('goods_name,goods_images,active_id,promote_start_time,promote_end_time,promote_price,from')
            ->find();
        $goodsObj->goods_images = $goodsObj->goods_images[0];

        $goodsObj->unit_price = $this->getUnitPriceOfGoods($goodsObj,$spec_id);
        $goodsObj->num = input('num');
        return $goodsObj;
    }

    /**
     * 计算运费
     */
    public function trans_price($totalmoney)
    {
        $transDetail = db('mall_trans')->find();
        $transMoney = $transDetail['default_price'];
        if ($transDetail['man'] && $transDetail['man'] <= $totalmoney) {
            $transMoney = 0;
        }
        return $transMoney;
    }
}