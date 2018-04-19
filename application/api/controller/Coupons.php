<?php

namespace app\api\controller;
use app\api\model\MallCouponsUser;
use app\api\model\MallGoods;
use app\api\validate\ConfirmOrder;
use app\api\service\Order as ServiceOrder;

/**
 * swagger: 优惠券
 */
class Coupons extends Common
{
    /**
     * get: 取出默认优惠券id
     * path: default_coupons
     * param: token - {string} token方法获取
     * param: goods_id - {int} 商品id
     * param: spec_id - {int} 规格id
     * param: num - {int} 商品数量
     */
    public function get_default_coupons()
    {
        $userCouponsObj = $this->default_coupons();
        if (!$userCouponsObj) {
            $this->error('暂无优惠券可用');
        }
        return $this->success('获取成功',$userCouponsObj);
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
        $this->success('获取成功',$couponsObj);
    }



    public function default_coupons()
    {
        (new ConfirmOrder())->goCheck();
        $userObj = $this->check_user();
        $goodsObj = MallGoods::get(input('goods_id'));
        $serviceOrder = new ServiceOrder();
        $unitPrice = $serviceOrder->getUnitPriceOfGoods($goodsObj,input('spec_id'));
        $userCouponsObj = MallCouponsUser::where('start_time','<',time())
            ->where('end_time','>',time())
            ->where('is_use','=',0)
            ->where('user_id','=',$userObj->user_id)
            ->where('man','<',$unitPrice*input('num'))
            ->order('jian DESC')
            ->select();
        return $userCouponsObj;
    }
}