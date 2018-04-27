<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/18
 * Time: 12:01
 */

namespace app\admin\controller\order;

use app\common\controller\Backend;
use think\Db;
use think\Exception;

class All extends Backend
{
    public function index()
    {
        return $this->fetch('index');
    }

    //搜索页面返回
    public function search()
    {
        $where = ' where ';
        //订单号，外部单号，收货人姓名，收货人手机
        if (!empty($_POST['order_number'])) {
            switch ($_POST['order_number_type']){
                case '订单号':
                    $where .= " fa_mall_order_goodslist.order_num='{$_POST['order_number']}' ";
                    break;
                case '微信支付订单号':
                    $where .= " wxpay_order_num='{$_POST['order_number']}' ";
                    break;
                case '收货人姓名':
                    $where .= " rec_name='{$_POST['order_number']}' ";
                    break;
                case '收货人手机后四位':
                    $where .= " rec_phone like '%{$_POST['order_number']}%' ";
                    break;
            }
            $and = ' and ';
        }else{
            $and = '';
        }

        //下单时间
        if (!empty($_POST['xiadan_date_begin'])) {
            $xiadan_date_begin = strtotime($_POST['xiadan_date_begin']);
            $where .= "{$and}".' '." create_time>{$xiadan_date_begin}";
            $and = ' and ';
        }else{
            $and = '';
        }
        //下单时间结束
        if (!empty($_POST['xiadan_date_end'])) {
            $xiadan_date_end = strtotime($_POST['xiadan_date_end']);
            $where .= "{$and}".' '." create_time<{$xiadan_date_end} ";
            $and = ' and ';
        }else{
            $and = '';
        }
        if ($_POST['order_status'] != '') {
            $where .= "{$and}".' '." status='{$_POST['order_status']}'";
            $and = ' and ';
        }else{
            $and = '';
        }
        if (!empty($_POST['goods_name'])) {
            $where .= "{$and}".' '." fa_mall_order_goodslist.goods_name like '%{$_POST['goods_name']}%'";
        }

        //如果条件为空则where为空;
        if ($where == ' where ') {
            $where = '';
        }
        try {
            $limit = $_POST['limit'];
            $page = $_POST['page']-1;
            $start = $page*$limit;
            //将超时未支付订单关闭
            $closeTime = time()-24*3600;
            db('mall_order')->where("status='0' and create_time<{$closeTime}")->update(['status'=>4]);
            //将已发货超过十天未确认收货的确认收货
            $completeTime = time()-10*24*3600;
            db('mall_order')->where("status='2' and send_time<{$completeTime}")->update(['status'=>3]);

            $sql = "SELECT fa_mall_order.order_num FROM fa_mall_order LEFT JOIN fa_mall_order_goodslist ON fa_mall_order.order_num=fa_mall_order_goodslist.order_num {$where}";
            $result = Db::query($sql);
            if (!$result) {
                return '';
            }
            $orderArr = [];
            foreach ($result as $k => $v){
                $orderArr[] = '\''.$v['order_num'].'\'';
            }

            $orderStr = implode(',',array_unique($orderArr));
            //总条数
            $totalCount = count(array_unique($orderArr));
            $totalPage = ceil($totalCount/$limit);
            $return['totalpage'] = $totalPage;
            $return['totalcount'] = $totalCount;
            $return['page'] = $_POST['page'];

            $sql = "SELECT order_num,status,wxpay_order_num,final_price,create_time,rec_name,comment,rec_phone FROM fa_mall_order  WHERE order_num IN ({$orderStr}) ORDER BY create_time DESC LIMIT {$start},{$limit}";


            $result = Db::query($sql);

        }catch (Exception $e){
            return ($e->getMessage());
        }
//        $result = Db::query("SELECT order_num,wxpay_order_num,final_price,create_time,rec_name,rec_phone FROM fa_mall_order ");
        foreach ($result as $k => $v){
            $date = date('Y-m-d',$v['create_time']);
            $time = date('H:i:s',$v['create_time']);


            //商品信息
            $orderGoodsObj = db('mall_order_goodslist')->field('goods_name,price,image,num,spec_info')->where("order_num='{$v['order_num']}'")->select();
            $result[$k]['goods_info'] = $orderGoodsObj;
            $result[$k]['goods_num'] = db('mall_order_goodslist')->field('goods_name,price,image,num,spec_info')->where("order_num='{$v['order_num']}'")->count();
        }
        $html = '';
        try {
            //最外层循环所有订单
            foreach ($result as $k => $v) {
                $html .= " <!-- 商品列表顶部开始 -->
                <div style=\"border:1px solid #e9e9e9;height: 48px;margin-top:10px;background:#F8F8F8;\">
                    <div class=\"layui-col-md6\"  style=\"padding-left:12px;\">
                        订单号:{$v['order_num']}  <font color=\"#999\">微信安全支付</font>
                    </div>
                    <div class=\"layui-col-md6\" style=\"text-align:right;padding-right: 12px;color: #3388FF;\">
                        <a href='/admin/order/all/order_detail?order_num={$v['order_num']}'>查看详情</a> <!-- 备注-->
                    </div>
                    <div class=\"layui-col-md12\"  style=\"padding-left:12px;\">
                        微信付款单号:  <font color=\"#999\" style=\"margin-right: 16px;\">{$v['wxpay_order_num']}</font> <!--支付流水号: <font color=\"#999\"></font>-->
                    </div>
                </div>
                <!-- 商品列表顶部结束 -->";

                //第二层循环商品，订单信息只在第一个显示
                foreach ($v['goods_info'] as $kk => $vv) {
                    $html .= "     
                    <!--第二层循环开始-->
                    <!-- 商品列表底部开始 -->
                    <div class=\"layui-row\" style=\" border-top:none;height: auto;clear: both;\">
    
                        <div class=\"layui-col-md7\" style=\"padding-left:12px;height: 95px;clear: both;border-left:1px solid #e9e9e9;\">
    
                            <div class=\"layui-col-md1\">
                            <img src=\"{$vv['image']}\"   style=\"width: 60px;height: 60px;padding-top: 10px;\">  
                            </div> 
                            <div class=\"layui-col-md9\">
                                <div class=\"layui-row-md6 font-bule\" style=\"padding-top:10px;\"> {$vv['goods_name']}</div> 
                                 <div class=\"layui-row-md6\">{$vv['spec_info']} </div>
                            </div> 
                            <div class=\"layui-col-md2 goods-right-style\" >
                            <div class=\"layui-row-md6 \">{$vv['price']} </div> 
                                 <div class=\"layui-row-md6\">({$vv['num']}件) </div>    
                            </div> 
    
    
                        </div>
                        <!--买家 下单时间 订单状态 实付金额-->
                        <div class=\"layui-col-md5\" style=\"\">
                            
                            <div class=\"layui-col-md2 goods-right-center\" style=\"width: 20%;\">
                            
                            </div>
                            <div class=\"layui-col-md2 goods-right-center\" style=\"width: 20%;\">
                                ";
                    if ($kk == 0) {
                        $html .= " <!--<div class=\"layui-row-md4\">＊谁在哭泣中笑场</div>-->
                                <div class=\"layui-row-md4\">{$v['rec_name']}</div>
                                <div class=\"layui-row-md4\">{$v['rec_phone']}</div> ";
                    }
                    $html .= "</div>
                            <div class=\"layui-col-md2 goods-right-center\" style=\"width: 20%;\">
                             ";

                    if ($kk == 0) {
                        $html .= " <div class=\"layui-row-md4\">{$date} </div>
                                <div class=\"layui-row-md4\"> {$time}</div>";
                    }
                    $html .= "</div>
                            <div class=\"layui-col-md2 goods-right-center\" style=\"width: 20%;\">
                             ";

                    if ($kk == 0) {
                        //待发货
                        if($v['status'] == 1){
                            $html .= " <div class=\"layui-row-md4\">等待商家发货 </div>
                                <div class=\"layui-row-md4 layui-btn\" id=\"fahuo\" onclick=\"fahuo('{$v['order_num']}')\"> 发 货 </div>";
                        }
                        //已发货
                        if($v['status'] == 2){
                            $html .= " <div class=\"layui-row-md4\">已发货 </div>
                            <div class=\"layui-row-md4 layui-btn\" id=\"wuliu\" onclick=\"wuliu('{$v['order_num']}')\"> 修改物流 </div>";
                        }
                        //待付款
                        if($v['status'] == 0){
                            $html .= " <div class=\"layui-row-md4\">等待付款 </div>
                            <div class=\"layui-row-md4 layui-btn layui-btn-danger\" id=\"cancel_order\" onclick=\"cancel_order('{$v['order_num']}')\"> 取消订单 </div>";
                        }
                        //已完成
                        if($v['status'] == 3){
                            $html .= " <div class=\"layui-row-md4\">已完成 </div>";
                        }
                        //已关闭
                        if($v['status'] == 4){
                            $html .= " <div class=\"layui-row-md4\">已关闭</div>";
                        }
                    }
                    $html .= "</div>
                            <div class=\"layui-col-md2 goods-right-center\" style=\"width: 20%;\">
                            ";

                    if ($kk == 0) {
                        $html .= " {$v['final_price']} ";
                    }

                    $html .= "</div>
                        </div>";
                }
                if (isset($v['comment'])) {
                    $html .= "  <div style=\"height: 27px;line-height: 27px;padding-left:10px;clear:both;background:#FDEEEE;color: #ED5050;\">买家备注： {$v['comment']} </div>";
                }else{
                    $html .=  "<div style=' clear:both;border-top:1px solid #e9e9e9;'></div>";
                }
            }
        }catch (Exception $e){
            return $e->getMessage();
        }
        $return['html'] = $html;
        return json($return);
    }

    //发货页面返回
    public function fahuo()
    {
        $order_num = input('order_num');
        $return  = "
        <div class=\"layui-row zfc-top\" style=\"padding-bottom:15px;\">
            <div class=\"layui-col-md5\">商品</div>
            <div class=\"layui-col-md1\">数量</div>
        </div>
        ";
        $orderGoodsList = db('mall_order_goodslist')->field('goods_name,num,spec_info')->where("order_num='{$order_num}'")->select();
        //订单商品信息
        foreach ($orderGoodsList as $k =>$v){
            $return .= "
        <div class=\"layui-row zfc-mid\" style=\"border:1px solid #ccc;border-top:none;\">
            <div class=\"layui-col-md5\" style=\"color:#3388FF;padding-right:20px;\"><div>{$v['goods_name']}</div><div style=\"color:#ccc;\">{$v['spec_info']}</div></div>
            <div class=\"layui-col-md1\">{$v['num']}</div>
        </div>  ";
        }
        //订单信息
        $orderObj = db('mall_order')->field('rec_name,rec_address,rec_phone')->where("order_num='{$order_num}'")->find();
        $return .= "
        <div style=\"padding-left:20px;padding-top:20px;color:#666;font-size:14px;\">收货地址：<font style=\"margin-left:12px;\">{$orderObj['rec_address']}, {$orderObj['rec_name']}, {$orderObj['rec_phone']}</font></div>
        <div style=\"padding-left:20px;padding-top:16px;color:#666;font-size:14px;\">发货方式：<input type=\"radio\" name=\"wl\" value=\"1\" title=\"物流发货\" checked style=\"margin-left:12px;\">物流发货<input type=\"radio\" name=\"wl\" value=\"0\" title=\"无须物流\" style=\"margin-left:12px;\" >无须物流</div>
        <div style=\"padding-left:20px;padding-top:20px;color:#666;font-size:14px;\">

        <div style=\"display:-webkit-flex;flex-direction:row;line-height:28px;\">
        <font>物流方式：</font>
        <select id='wuliu' name=\"wl_cp\" lay-verify=\"\"  style=\"margin-left:12px;width:200px;\">
            <option value=\"yzkd\">请选择一个物流公司</option>";
        //查询物流
        $wuliu = db('mall_kuaidi')->field('id,name')->select();
        foreach ($wuliu as $k => $v){
            $return .= "
                <option value=\"{$v['id']}\">{$v['name']}</option>";
        }
        $return .= "
        </select>
        <font style=\"margin-left:70px;\">快递单号:</font> <input type=\"text\" id='danhao' name=\"title\" required lay-verify=\"required\" placeholder=\"\" autocomplete=\"off\" class=\"layui-input\" style=\"width:200px;height:28px;margin-left:15px;\"> 
        </div>
        <div style=\"font-size:12px;color:#ccc;margin-top:14px;\">*请仔细填写物流公司及快递单号，发货后24小时内仅支持做多次更正，逾期可以修改</div>
        <button id=\"btnOKFahuo\" onclick=\"submit('{$order_num}')\" class=\"layui-btn\" style=\"margin-top:30px;float:right;margin-right:20px;margin-bottom:20px;\" >提交</button>
        </div>";
        return json($return);
    }

    //更改发货页面返回
    public function fahuo_change()
    {
        $order_num = input('order_num');
        $orderObj = db('mall_order')->field('wuliu,wuliu_num')->where("order_num='{$order_num}'")->find();
        $return =
            "<div style=\"margin-top: 20px;height: 38px;line-height: 38px;background: #FFF6CC;margin-left:20px;margin-right: 20px;\">
            <p class=\"update-express-tips\">
            <span class=\"layui-badge-dot\" style=\"margin-left:10px;margin-right: 10px;\"></span> 物流信息支持多次更正，请仔细填写并核对</p>   
            </div>
            <!--<p style=\"margin-left: 20px;padding-top: 20px;\"><font style=\"font-weight: bold;\">包裹1</font> 共2件商品</p>-->
            <div style=\"padding-left:20px;padding-top:16px;color:#666;font-size:14px;\">
            发货方式：
            <input type=\"radio\" name=\"wl\" value=\"1\" title=\"物流发货\" checked style=\"margin-left:12px;\">
            物流发货
            <input type=\"radio\" name=\"wl\" value=\"0\" title=\"无须物流\" style=\"margin-left:30px;\" >无须物流
            <div style=\"display:-webkit-flex;flex-direction:row;line-height:28px;margin-top: 15px;\">
                <font>物流方式：</font><select id='wuliu'  name=\"wl_cp\" lay-verify=\"\"  style=\"margin-left:12px;width:200px;\">
                 <option value=\"\">请选择一个物流公司</option>";
        $wuliu = db('mall_kuaidi')->field('id,name')->select();
        foreach ($wuliu as $k => $v){
            if ($orderObj['wuliu']==$v['id']) {
                $return .= "
                        <option value=\"{$v['id']}\"  selected=\"true\">{$v['name']}</option>";
            }else{
                $return .= "
                        <option value=\"{$v['id']}\">{$v['name']}</option>";
            }

        }
        $return .= "
                </select>
                <font style=\"margin-left:10px;\">快递单号:</font> <input id='danhao' type=\"text\" name=\"title\" required lay-verify=\"required\" placeholder=\"{$orderObj['wuliu_num']}\" autocomplete=\"off\" class=\"layui-input\" style=\"width:200px;height:28px;margin-left:15px;\"> 
            </div>
        </div>

        <button id=\"btnOKCancle\" onclick=\"close_window()\" class=\"layui-btn layui-btn-primary\" style=\"margin-top:45px;float:right;margin-right:20px;margin-bottom:20px;\" >取消</button>
        <button id=\"btnOKChange\" onclick=\"submit('{$order_num}')\" class=\"layui-btn\" style=\"margin-top:45px;float:right;margin-right:20px;margin-bottom:20px;\" >提交</button>";
        return json($return);
    }

    //订单详情页面
    public function order_detail()
    {
        $order_num = input('order_num');
        $orderObj = db('mall_order')->where("order_num='{$order_num}'")->field('order_num,coupons_off,final_price,status,user_id,wuliu,wuliu_num,rec_name,comment,rec_address,rec_phone,create_time,pay_time,send_time,wuliu_price')->find();
        switch ($orderObj['status']){
            case 0:
                $orderObj['status2'] = 1;
                $orderObj['message'] = '等待买家付款';
                break;
            case 1:
                $orderObj['status2'] = 2;
                $orderObj['message'] = '等待卖家发货';
                break;
            case 2:
                $orderObj['status2'] = 3;
                $orderObj['message'] = '等待确认收货，等待交易成功';
                break;
            case 3:
                $orderObj['status2'] = 4;
                $orderObj['message'] = '订单已完成';
                break;
            case 4:
                $orderObj['status2'] = 5;
                $orderObj['message'] = '订单已关闭';
                break;
        }
        $wuliu = db("mall_kuaidi")->where("id='{$orderObj['wuliu']}'")->field('name')->find();
        $orderObj['wuliu'] = $wuliu['name'];
        $orderObj['create_time'] = date('Y-m-d H:i:s',$orderObj['create_time']);
        if ($orderObj['pay_time']) {
            $orderObj['pay_time'] = date('Y-m-d H:i:s',$orderObj['pay_time']);
        }
        if ($orderObj['send_time']) {
            $orderObj['send_time'] = date('Y-m-d H:i:s',$orderObj['send_time']);
        }
        $goodsList = db('mall_order_goodslist')->field('goods_name,spec_info,price,num,image')->where("order_num='{$order_num}'")->select();
        $orderObj['goods_count'] = db('mall_order_goodslist')->where("order_num='{$order_num}'")->count();
        $this->assign('goodslist',$goodsList);
        $this->assign('orderdetail',$orderObj);
        return $this->fetch('order_detail');
    }

    //发货
    public function fahuo_success()
    {
        $order_num = input('order_num');
        $is_wuliu = input('is_wuliu');
        $wuliu = input('wuliu');
        $danhao = input('danhao');
        if ($is_wuliu == 1) {
            $rs = db('mall_order')->where("order_num='{$order_num}'")->update(['status'=>2,'wuliu'=>$wuliu,'wuliu_num'=>$danhao,'send_time'=>time()]);
        }else{
            $rs = db('mall_order')->where("order_num='{$order_num}'")->update(['status'=>2,'send_time'=>time()]);
        }
        if ($rs) {
            $return['code'] = true;
        }else{
            $return['code'] = false;
        }
        return $return;
    }

    //取消订单
    public function order_cancle()
    {
        $order_num = input('order_num');
        $orderObj = db('mall_order')->where("order_num='{$order_num}'")->field('status')->find();
        if ($orderObj['status'] != 0) {
            $return['code'] = true;
            $return['message'] = '订单已经支付,请确认';
            return json($return);
        }
        $rs = db('mall_order')->where("order_num='{$order_num}'")->update(['status'=>4]);
        if ($rs) {
            $return['code'] = true;
            $return['message'] = '取消成功';
        }else{
            $return['code'] = false;
            $return['message'] = '取消失败';
        }
        return json($return);
    }
}