<?php


namespace app\api\controller;


use app\api\model\MallGoods;
use app\common\controller\Api;
use think\Request;


/**
 * swagger: 商品
 */
class Goods extends Api
{
    // 无需验证登录的方法
    protected $noNeedLogin = ['*'];
    // 无需要判断权限规则的方法
    protected $noNeedRight = ['*'];

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        if (!input('goods_id') ||!is_numeric(input('goods_id'))) {
            $this->error('参数错误','',403);
        }
    }

    /**
     * get: 商品详细信息获取页面
     * path: goods_detail
     * param: goods_id - {int} 商品id
     */
    public function goods_detail($goods_id)
    {
        $goodsDetail = MallGoods::with('mallattrs')->field('goods_id,goods_name,goods_brief,goods_desc,shop_price,goods_images,sell_count,is_onsale,from')->find($goods_id);

        if ($goodsDetail) {
            $goodsDetail = $goodsDetail->toArray();
            $goodsDetail['active'] = MallGoods::active($goods_id);
            if ($goodsDetail['active']['status']) {
                $goodsDetail['promote_price'] = $goodsDetail['active']['promote_price'];
                foreach ($goodsDetail['mallattrs'] as $k => $v){
                    $goodsDetail['mallattrs'][$k]['promote_price'] = $goodsDetail['active']['promote_price'];
                }
            }
            $this->success('返回成功',$goodsDetail,200);
        } else{
            $this->error('商品不存在','',404);
        }
    }


    /**
     * get: 相似商品推荐
     * path: recommend_similar
     * param: goods_id - {int} 商品id
     */
    public function recommend_similar($goods_id)
    {
        $goodsObj = db('mall_goods')->where("goods_id={$goods_id}")->field('cat_id')->find();
        if (!$goodsObj) {
            $this->error('商品不存在','',404);
        }
        $list = db('mall_goods')->where("goods_id!={$goods_id} and cat_id={$goodsObj['cat_id']}")->field('goods_id,goods_name,shop_price,goods_images')->select();
        if (!$list) {
            $this->error('没有推荐信息',$list,400);
        }
        $this->success('返回成功',$list,200);
    }


    /**
     * get: 品牌推荐
     * path: recommend_brand
     * param: goods_id - {int} 商品id
     */
    public function recommend_brand($goods_id)
    {
        $goodsObj = db('mall_goods')->where("goods_id={$goods_id}")->field('brand_id')->find();
        if (!$goodsObj) {
            $this->error('商品不存在','',404);
        }
        $list = db('mall_goods')->where("goods_id!={$goods_id} and brand_id={$goodsObj['brand_id']}")->field('goods_id,goods_name,shop_price,goods_images')->select();
        if (!$list) {
            $this->error('没有推荐信息',$list,400);
        }

        $this->success('返回成功',$list,200);
    }
    
}