<?php

namespace app\admin\controller;

use app\common\controller\Backend;

/**
 * 控制台
 *
 * @icon fa fa-dashboard
 * @remark 用于展示当前系统中的统计数据、统计报表及重要实时数据
 */
class Dashboard extends Backend
{

    /**
     * 查看
     */
    public function index()
    {
        //获取今日开始时间戳和结束时间戳
        $beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
        $endToday=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
        //获取昨日起始时间戳和结束时间戳
        $beginYesterday=mktime(0,0,0,date('m'),date('d')-1,date('Y'));
        $endYesterday=mktime(0,0,0,date('m'),date('d'),date('Y'))-1;

        $seventtime = \fast\Date::unixtime('day', -7);
        $paylist = $createlist = [];
        for ($i = 0; $i < 7; $i++)
        {
            $day = date("Y-m-d", $seventtime + ($i * 86400));
            $beginTodayTime = $beginToday - ((7-$i) * 86400);
            $endTodayTime = $endToday - ((7-$i)  * 86400);
            $createlist[$day] = db('mall_order')->where("create_time>{$beginTodayTime} and create_time<{$endTodayTime}")->count();
            $paylist[$day] = db('mall_order')->where("status!='0' and status!='4' and create_time>{$beginTodayTime} and create_time<{$endTodayTime}")->count();
        }
        $hooks = config('addons.hooks');
        $uploadmode = isset($hooks['upload_config_init']) && $hooks['upload_config_init'] ? implode(',', $hooks['upload_config_init']) : 'local';


        //开始统计
        $date = date('Ymd',time());
        $yesterdayDate = date("Ymd",strtotime("-1 day"));
        //今日访客数
        $todayUserLoginCount = db('mall_loginlog')->where("login_time='{$date}'")->count();
        $yesterdayCountObj = db('mall_loginlog')->where("login_time='{$yesterdayDate}'")->count();
        //昨日访客数
        isset($yesterdayCountObj['user_count'])?$yesterdayUserCount = $yesterdayCountObj['user_count']:$yesterdayUserCount = 0;
        //昨日浏览量
        isset($yesterdayCountObj['user_view'])?$yesterdayUserView = $yesterdayCountObj['user_view']:$yesterdayUserView = 0;
        $todayUserView = db('mall_loginlog')->where("login_time='{$date}'")->sum('tips');
        //付款订单数
        $todayOrderPayOrderCount = db('mall_order')->where("status!='0' and create_time>{$beginToday} and create_time<{$endToday}")->count();
        $yestodayOrderPayOrderCount = db('mall_order')->where("status!='0' and create_time>{$beginYesterday} and create_time<{$endYesterday}")->count();
        //付款人数
        $todayOrderPayUserCount = db('mall_order')->where("status!='0' and create_time>{$beginToday} and create_time<{$endToday}")->group('user_id')->count();
        $yestodayOrderPayUserCount = db('mall_order')->where("status!='0' and create_time>{$beginYesterday} and create_time<{$endYesterday}")->group('user_id')->count();

        //待发货
        $needSend = db('mall_order')->where("status='1'")->count();
        $needPay = db('mall_order')->where("status='0'")->count();

        //总会员数
        $totalUser = db('mall_user')->count();
        //新增会员数
        $newUser = db('mall_user')->where("create_time='{$date}'")->count();

        //七日下单逼数
        $seventtimebegin = $beginToday-7*86400;
        $sevenOrder = db('mall_order')->where("create_time>{$seventtimebegin} and create_time<{$endToday}")->count();
        $sevenMoney = db('mall_order')->where("create_time>{$seventtimebegin} and create_time<{$endToday}")->sum('final_price');

        //商品统计
        $goodsCount = db('mall_goods')->count();

        //客单价 客单价=销售总额（除去打折等优惠之后的算下来的钱）÷顾客总数
        //今日付款总金额
        $todayMoney = db('mall_order')->where("status!='0' and status!='4' and create_time>{$beginToday} and create_time<{$endToday}")->sum('final_price');
        if ($todayUserLoginCount == 0) {
            $kedanjia = 0;
        }else{
            $kedanjia = round($todayMoney/$todayUserLoginCount,'2');
        }


        //已完成订单总数目
        $orderTotal = db('mall_order')->where("status='3'")->count();
        //已完成订单总金额
        $orderTotalMoney = db('mall_order')->where("status='3'")->sum('final_price');

        $this->view->assign([
            'todayUserLoginCount'        => $todayUserLoginCount,
            'yesterdayUserCount'        => $yesterdayUserCount,
            'todayUserView'        => $todayUserView,
            'yesterdayUserView'        => $yesterdayUserView,
            'todayOrderPayOrderCount'       => $todayOrderPayOrderCount,
            'yestodayOrderPayOrderCount'       => $yestodayOrderPayOrderCount,
            'todayOrderPayUserCount'       => $todayOrderPayUserCount,
            'yestodayOrderPayUserCount' => $yestodayOrderPayUserCount,
            'needSend'   => $needSend,
            'needPay'  => $needPay,
            'newUser'       => $newUser,
            'totalUser'    => $totalUser,
            'sevenOrder'         => $sevenOrder,
            'sevenMoney'         => $sevenMoney,
            'paylist'          => $paylist,
            'createlist'       => $createlist,
            'uploadmode'       => $uploadmode,
            'goodscount'       => $goodsCount,
            'todaymoney'       => $todayMoney,
            'kedanjia'         => $kedanjia,
            'orderTotal'     =>$orderTotal,
            'orderTotalMoney' =>$orderTotalMoney,
        ]);


        return $this->view->fetch();

    }

}
