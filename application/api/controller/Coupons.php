<?php

namespace app\api\controller;
use app\api\model\MallCouponsUser;
use app\api\model\MallGoods;
use app\api\validate\ConfirmOrder;
use app\api\service\Order as ServiceOrder;
use app\api\validate\DefaultCoupons;
use app\lib\exception\MissException;

/**
 * swagger: 优惠券
 */
class Coupons extends Common
{
    /**
     * get: 取出默认优惠券id
     * path: get_default_coupons
     * param: token - {string} token方法获取
     * param: goods_id - {int} 商品id
     * param: spec_id - {int} 规格id
     * param: num - {int} 商品数量
     */
    public function get_default_coupons()
    {
        (new DefaultCoupons())->goCheck();
        $userObj = $this->check_user();
        $goodsObj = MallGoods::get(input('goods_id'));
        $serviceOrder = new ServiceOrder();
        $unitPrice = $serviceOrder->getUnitPriceOfGoods($goodsObj,input('spec_id'));
        $userCouponsObj = ServiceOrder::default_coupons($userObj->user_id,$unitPrice*input('num'));
        if (!$userCouponsObj) {
            throw new MissException([
                'msg' => '暂无优惠券可用'
            ]);
        }

       return json($userCouponsObj);
    }

    /**
     * get: 用户优惠券列表
     * path: coupons_list
     * param: token - {string} token方法获取
     */
    public function coupons_list()
    {
        $userObj = $this->check_user();
        $couponsObj = MallCouponsUser::where('user_id','=',$userObj->user_id)->select();
        return json($couponsObj);
    }


}