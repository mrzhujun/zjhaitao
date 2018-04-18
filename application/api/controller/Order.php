<?php

namespace app\api\controller;

use app\api\model\MallAddress;
use app\api\model\MallCouponsUser;
use app\api\model\MallGoods;
use app\api\model\MallOrder;
use app\api\service\Order as ServiceOrder;
use app\api\validate\ConfirmOrder;
use think\Db;
use think\Exception;
use think\Validate;

/**
 * swagger: 订单
 */
class Order extends Common
{
    /**
     * get: 订单列表
     * path: order_list
     * param: token - {string} token方法获取
     */
    public function order_list()
    {
        $userObj = $this->check_user();
        $orderList = MallOrder::where('user_id','=',$userObj->user_id)->select();
        $this->success('获取成功',$orderList);
    }



    /**
     * get: 确认订单页面
     * path: confirm_order
     * param: token - {string} token方法获取
     * param: goods_id - {int} 商品id
     * param: spec_id - {int} 规格id
     * param: coupons_id - {int} 优惠券id
     * param: num - {int} 商品数量
     */
    public function confirm_order()
    {
        (new ConfirmOrder())->goCheck();
        $userObj = $this->check_user();
        //默认地址
        $addressObj = MallAddress::getDefaultAddress($userObj->user_id);
        $return['address_detail'] = $addressObj;
        //商品信息
        $goodsObj = MallGoods::where('goods_id','=',input('goods_id'))
            ->field('goods_name,goods_images')
            ->find();
        $goodsObj->unit_price = (new ServiceOrder())->getUnitPriceOfGoods($goodsObj,input('spec_id'));
        $goodsObj->num = input('num');
        $return['goods_detail'] = $goodsObj;
        //优惠券信息
        if (!input('coupons_id')) {
            $return['coupons_detail'] = [];
        }else{
            $return['coupons_detail'] = $this->default_coupons();
        }

        $this->success('',$return);
    }




    /**
     * post: 单个商品添加订单
     * path: add
     * param: token - {string} token方法获取
     * param: address_id - {int} 地址id
     * param: goods_id - {int} 商品id
     * param: spec_id - {int} = '' 规格id
     * param: coupons_user_id - {int} = '' 优惠券id
     * param: num - {int} 商品数量
     * param: comment - {string} =  '' 备注
     */
    public function add()
    {
        //启动事务
        Db::startTrans();
        try{
            (new ConfirmOrder())->goCheck();
            if (!input('address_id')) {
                throw new Exception('地址id不能为空');
            }
            $userObj = $this->check_user();
            //1.生成订单总表
            $ServiceOrder = new ServiceOrder();
            $orderObj = $ServiceOrder->neworder(input('address_id'),input('comment'),$userObj->user_id);

            //2.添加商品信息到商品附加表
            $totalPrice = $ServiceOrder->goodstoorder($orderObj->order_num,input('goods_id'),input('num'),input('spec_id'));

            //3.优惠券
            if (input('coupons_id')) {
                $couponsObj = MallCouponsUser::get(input('coupons_user_id'));
                if ($couponsObj->man > $totalPrice) {
                    throw new Exception('优惠券不可用');
                }

                $orderObj->coupons_user_id = input('coupons_user_id');
                $orderObj->coupons_off = $couponsObj->jian;
                $orderObj->final_price = $totalPrice-$couponsObj->jian;
            }else{
                $orderObj->final_price = $totalPrice;
            }
            //4.保存订单信息
            $rs = $orderObj->save();

            if (!$rs) {
                throw new Exception('订单创建失败');
            }
            //提交事务
            Db::commit();
            $this->success('订单创建成功',['order_num'=>$orderObj->order_num]);
        }
        catch (Exception $e)
        {
            //如发生错误数据回滚
            Db::rollback();
            dump($e);
        }



    }

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