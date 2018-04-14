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

    protected $goodsObj;

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $goods_id = $request->param()['goods_id'];
        if (!$goods_id ||!is_numeric($goods_id)) {
            $this->error('参数错误');
        }
        $goodsObj = MallGoods::where('goods_id',$goods_id)->field('goods_id,goods_name,goods_brief,goods_desc,cat_id,brand_id,shop_price,goods_images,sell_count,is_onsale')->find();
        if (!$goodsObj->is_onsale) {
            $this->error('该商品暂未出售');
        }else{
            $this->goodsObj = $goodsObj;
        }
    }


    /**
     * get: 商品详细信息获取页面
     * path: goods_detail
     * param: goods_id - {int} 商品id
     */
    public function goods_detail()
    {
        if ($this->goodsObj) {
            $goodsDetail = MallGoods::with('mallattrs')->find();
            $this->success('返回成功',$goodsDetail);
        }else{
            $this->error('商品不存在');
        }
    }


    /**
     * get: 相似商品推荐
     * path: recommend_similar
     * param: goods_id - {int} 商品id
     */
    public function recommend_similar()
    {
        $list = MallGoods::where('cat_id',$this->goodsObj->cat_id)->where('goods_id','<>',$this->goodsObj->goods_id)->field('goods_name,shop_price,goods_images')->select();
        if (!$list) {
            $this->error('没有推荐信息');
        }
        foreach ($list as $k => $v){
            $arr = explode(',',$v->goods_images);
            $list[$k]->goods_images = add_url($arr[0]);
        }
        $this->success('返回成功',$list);
    }


    /**
     * get: 品牌推荐
     * path: recommend_brand
     * param: goods_id - {int} 商品id
     */
    public function recommend_brand()
    {
        $list = MallGoods::where('brand_id',$this->goodsObj->brand_id)->where('goods_id','<>',$this->goodsObj->goods_id)->field('goods_name,shop_price,goods_images')->select();
        if (!$list) {
            $this->error('没有推荐信息');
        }
        foreach ($list as $k => $v){
            $arr = explode(',',$v->goods_images);
            $list[$k]->goods_images = add_url($arr[0]);
        }
        $this->success('返回成功',$list);
    }
    
}