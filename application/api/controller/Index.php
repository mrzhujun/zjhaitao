<?php

namespace app\api\controller;


use app\api\model\MallActive;
use app\api\model\MallGoods;
use app\api\model\MallRecommend;
use app\api\model\MallShop;
use app\common\controller\Api;
use app\lib\exception\MissException;

/**
 * swagger: 首页
 */
class Index extends Api
{
    // 无需验证登录的方法
    protected $noNeedLogin = ['*'];
    // 无需要判断权限规则的方法
    protected $noNeedRight = ['*'];

    /**
     * get: 顶部店标及关于我们图片获取
     * path: shop_image
     */
    public function shop_image()
    {
        $return = db('mall_shop')->field('head_image,intro_image')->find();
        $return['head_image'] = add_url($return['head_image']);
        $return['intro_image'] = add_url($return['intro_image']);
        return json($return);
    }

    /**
     * get: 关于我们页面详情
     * path: about_us
     */
    public function about_us()
    {
        $return = MallShop::field('bout_us_content')->find();
        return json($return);
    }


    /**
     * get: 新人活动
     * path: new_people
     */
    public function new_people()
    {
        $time = time();
        $goodsList = MallActive::where("active_type='1' and start_time<{$time} and end_time>{$time}")->field('goods_id')->limit(2)->select();
        if (!$goodsList) {
            throw new MissException([
                'msg' => '没有活动信息',
                'errorCode' => 30000
            ]);
        }
        $arr = [];
        foreach ($goodsList as $k => $v){
            $goodsDetail = MallGoods::where('goods_id',$v->goods_id)->field('goods_id,goods_name,shop_price,promote_price,goods_images,from')->find();
            $arr[] = $goodsDetail;
        }
        $return['image'] = MallShop::where(0)->field('xin_image')->find();
        $return['goods_list'] = $arr;
        return json($return);
    }

    /**
     * get: 尖货铺
     * path: the_best
     */
    public function the_best()
    {
        $goodsList = MallActive::where("active_type='2'")->field('goods_id')->limit(20)->select();
        if (!$goodsList) {
            throw new MissException([
                'msg' => '该活动没有商品',
                'errorCode' => 30002
            ]);
        }
        $arr = [];
        foreach ($goodsList as $k => $v){
            $goodsDetail = MallGoods::where('goods_id',$v->goods_id)->field('goods_id,goods_name,goods_images,from')->find();
            $arr[] = $goodsDetail;
        }
        $return['image'] = MallShop::where(0)->field('xin_image')->find();
        $return['goods_list'] = $arr;
        return json($return);
    }



    /**
     * get: 限时特卖
     * path: flash_sale
     */
    public function flash_sale()
    {
        $shopObj = MallShop::get(0);
        if ($shopObj->temai_start_time>time() || $shopObj->temai_end_time<time()) {
            throw new MissException([
                'msg' => '不在活动时间内',
                'errorCode' => 30004
            ]);
        }

        $goodsList = MallActive::where("active_type='3'")->field('goods_id')->select();
        if (!$goodsList) {
            throw new MissException([
                'msg' => '该活动没有商品',
                'errorCode' => 30002
            ]);
        }
        $arr = [];
        foreach ($goodsList as $k => $v){
            $goodsDetail = MallGoods::where('goods_id',$v->goods_id)->field('goods_id,goods_name,goods_images,shop_price,promote_price,goods_images,from')->find();
            $arr[] = $goodsDetail;
        }
        $return['image'] = MallShop::where(0)->field('temai_image')->find();
        $return['goods_list'] = $arr;
        return json($return);
    }


    /**
     * get: 专场
     * path: special
     * param: page - {int} = '1' 页数
     * param: limit - {int} = '5' 每页显示
     */
    public function special($page = 1,$limit = 5)
    {
        $page<1?$page = 1:$page = $page;
        $count = MallActive::where('active_type','=',4)->count();
        $maxPage = ceil($count/$limit);
        $pageDetail['total_page'] = $maxPage;
        $pageDetail['now_page'] = $page;

        if ($page > $maxPage) {
            throw new MissException([
                'msg' => '没有下一页了',
                'errorCode' => 30005
            ]);
        }

        $activeList = MallActive::where("active_type",4)->limit($page-1,$limit)->select();

        foreach ($activeList as $k => $v){
            $activeList[$k]['goods_id'] = MallGoods::where("goods_id in ({$v['goods_id']})")->field('goods_id,goods_name,shop_price,goods_images,from')->select();
        }
        $return['page_detail'] = $pageDetail;
        $return['active_list'] = $activeList;
        return json($return);
    }

    /**
     * get: 图片分类或品牌推荐
     * path: recommend
     */
    public function recommend()
    {
        $one = MallRecommend::where('size','=','0')->select();
        $two = MallRecommend::where('size','=','1')->select();
        $return['one']['data'] = $one;
        $return['one']['msg'] = '这个一行排一个';
        $return['two']['data'] = $two;
        $return['two']['msg'] = '这个一行排两个';
        return json($return);
    }


}