<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/10
 * Time: 10:18
 */

namespace app\api\controller;


use app\api\model\MallGoods;
use app\common\controller\Api;
use think\Request;


/**
 * 商品
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
        $goodsObj = MallGoods::where('goods_id',$goods_id)->field('goods_id,goods_name,goods_brief,goods_desc,cat_id,brand_id,shop_price,goods_images,sell_count,is_onsale')->find();
        if (!$goodsObj->is_onsale) {
            $this->error('该商品暂未出售');
        }else{
            $this->goodsObj = $goodsObj;
        }
    }

    /**
     * 商品详细信息获取页面
     * @ApiParams   (name="goods_id", type="int", required=true, description="商品id")
     * @ApiReturn   (data="{
     *     'code':'1/0',
     *     'msg':'返回成功/失败',
     *     'time':'1523173262',
     *     'data':'商品+商品规格+商品活动信息'
     *     }")
     */
    public function goods_detail()
    {
        if ($this->goodsObj) {
            $this->goodsObj->attr = $this->goodsObj->mallattrs()->where('goods_id',$this->goodsObj->goods_id)->select();
            $this->success('返回成功',$this->goodsObj);
        }else{
            $this->error('商品不存在');
        }
    }

    
}