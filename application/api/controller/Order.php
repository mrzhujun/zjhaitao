<?php

namespace app\api\controller;

use app\api\model\MallAddress;
use app\api\model\MallCart;
use app\api\model\MallCouponsUser;
use app\api\model\MallGoods;
use app\api\model\MallOrder;
use app\api\service\Order as ServiceOrder;
use app\api\service\WeixinPay;
use app\api\validate\CarToOrder;
use app\api\validate\ConfirmOrder;
use think\Db;
use think\Exception;

/**
 * swagger: 订单
 */
class Order extends Common
{
    /**
     * get: 订单列表
     * path: order_list
     * param: token - {string} token方法获取
     * param: type - {int} = ['0','2','3','5'] (待付款，待收货，已完成，全部订单)
     */
    public function order_list()
    {

        $userObj = $this->check_user();
        $orderList = MallOrder::where('user_id','=',$userObj->user_id)
            ->with(['order_goodslists'=>function($query){$query->field('order_num,goods_id,goods_name,price,spec_info,image');}])
            ->select();
        $this->success('获取成功',$orderList);
    }



    /**
     * get: 确认订单页面
     * path: confirm_order
     * param: token - {string} token方法获取
     * param: goods_id - {int} 商品id
     * param: spec_id - {int} = '' 规格id
     * param: coupons_id - {int} = '' 优惠券id
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
            $userObj = $this->check_user();
            //1.生成订单总表
            $ServiceOrder = new ServiceOrder();
            $orderObj = $ServiceOrder->neworder(input('address_id'),input('comment'),$userObj->user_id);

            //2.添加商品信息到商品附加表
            $totalPrice = $ServiceOrder->goodstoorder($orderObj->order_num,input('goods_id'),input('num'),input('spec_id'));

            //3.优惠券
            $couponsObj = ServiceOrder::selectCoupons($totalPrice);
            $orderObj->coupons_user_id = input('coupons_user_id');
            $orderObj->coupons_off = $couponsObj['jian'];
            $orderObj->final_price = $totalPrice-$couponsObj['jian'];
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
            $this->error($e->getMessage());
        }
    }

    /**
     * post: 购车车转订单
     * path: carttoorder
     * param: token - {string} token方法获取
     * param: address_id - {int} 地址id
     * param: cart_id_arr - {array} 购物车id列表数组
     * param: coupons_user_id - {int} = '' 优惠券id
     * param: comment - {string} =  '' 备注
     */
    public function carttoorder()
    {
        (new CarToOrder())->goCheck();
        $userObj = $this->check_user();
        $cartArr = input('cart_id_arr');
        Db::startTrans();
        try{
            //1.生成订单总表
            $ServiceOrder = new ServiceOrder();
            $orderObj = $ServiceOrder->neworder(input('address_id'),input('comment'),$userObj->user_id);
            //2.添加商品信息到商品附加表
            $totalPrice = 0;
            foreach ($cartArr as $k => $v)
            {
                $cartObj = MallCart::where('cart_id','=',$v);
                $totalPrice += $ServiceOrder->goodstoorder($orderObj->order_num,$cartObj->goods_id,$cartObj->num,$cartObj->spec_id);
            }
            //3.优惠券
            $couponsObj = ServiceOrder::selectCoupons($totalPrice);
            $orderObj->coupons_user_id = input('coupons_user_id');
            $orderObj->coupons_off = $couponsObj['jian'];
            $orderObj->final_price = $totalPrice-$couponsObj['jian'];
            //4.保存订单信息
            $rs = $orderObj->save();

            if (!$rs) {
                throw new Exception('订单创建失败');
            }
            //提交事务
            Db::commit();
            $this->success('订单创建成功',['order_num'=>$orderObj->order_num]);
        }catch (Exception $e)
        {
            Db::rollback();
        }
    }

    /**
     * post: 关闭订单
     * path: order_close
     * param: token - {string} token方法获取
     * param: order_num - {string} 订单号
     */
    public function order_close()
    {
        echo __DIR__;exit();
        $this->check_user();
        Db::startTrans();
        try{
            ServiceOrder::order_close(input('order_num'));
            $this->success('关闭成功');
        }catch (Exception $e)
        {
            $this->error($e->getMessage());
        }
    }

    /**
     * post: 订单发起支付
     * path: pay
     * param: token - {string} token方法获取
     * param: order_num - {string} 订单号
     */
    public function pay()
    {
        $userObj = $this->check_user();
        $orderDetail = MallOrder::get(input('order_num'));
        $money = $orderDetail->final_price;
        $weixinpay = new WeixinPay(config('wx.app_id'), $userObj->open_id, config('wx.mch_id'), config('wx.mch_secret'), $money ,config('wxpay_notify_url') , input('order_num'));
        $return = $weixinpay -> pay();
        //返回5个支付参数和sign
        return json($return);
    }


    /**
     * 微信支付回调接口
     */
    public function paycallback()
    {
        $xml = file_get_contents('php://input');
        // 这句file_put_contents是用来查看服务器返回的XML数据 测试完可以删除了
        file_put_contents(__DIR__.'/public/statics/log/log.txt',$xml,FILE_APPEND);

        $arr = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);

        //用户http_build_query()将数据转成URL键值对形式
        $sign = http_build_query($arr);
        //md5处理
        $sign = md5($sign);
        //转大写
        $sign = strtoupper($sign);
        //验签名。默认支持MD5
        if ( $sign === $arr['sign']) {
            //获取服务器返回的数据
            $order_sn = $arr['out_trade_no'];          //订单单号
            $openid = $arr['openid'];                  //付款人openID
            $total_fee = $arr['total_fee'];            //付款金额
            $transaction_id = $arr['transaction_id'];  //微信支付订单号
        }else{
            $result = false;
        }
        // 返回状态给微信服务器
        if ($result) {
            $str='<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
        }else{
            $str='<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA[签名失败]]></return_msg></xml>';
        }
        echo $str;
    }
}