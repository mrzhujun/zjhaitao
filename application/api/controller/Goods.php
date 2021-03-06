<?php


namespace app\api\controller;


use app\api\model\MallGoods;
use app\common\controller\Api;
use app\lib\exception\GoodsException;
use app\lib\exception\MissException;
use think\Request;
use app\api\validate\Goods as ValidateGoods;


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
        (new ValidateGoods())->goCheck();
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
            return json($goodsDetail);
        } else{
            throw new GoodsException();
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
            throw new GoodsException();
        }
        $list = db('mall_goods')->where("goods_id!={$goods_id} and cat_id={$goodsObj['cat_id']}")->field('goods_id,goods_name,shop_price,goods_images')->select();
        if (!$list) {
            throw new MissException([
                'msg' => '没有推荐信息',
                'errorCode' => 20001
            ]);
        }
        return json($list);
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
            throw new GoodsException();
        }
        $list = db('mall_goods')->where("goods_id!={$goods_id} and brand_id={$goodsObj['brand_id']}")->field('goods_id,goods_name,shop_price,goods_images')->select();
        if (!$list) {
            throw new MissException([
                'msg' => '没有推荐信息',
                'errorCode' => 20001
            ]);
        }

        return json($list);
    }
    
}