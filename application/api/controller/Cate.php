<?php

namespace app\api\controller;


use app\api\model\MallGoods;
use app\common\controller\Api;
use \app\api\model\Category;
use app\lib\exception\MissException;

/**
 * swagger: 分类
 */
class Cate extends Api
{
    // 无需验证登录的方法
    protected $noNeedLogin = ['*'];
    // 无需要判断权限规则的方法
    protected $noNeedRight = ['*'];
    /**
     * get: 获取分类列表
     * path: category
     */
    public function category()
    {
        //普通分类
        $list = Category::where("type='category' and pid='0'")->field('id,name,image,description,image_from')->select();
        if (!$list) {
            throw new MissException([
                'msg' => '未查询到分类'
            ]);
        }

        foreach ($list as $k => $v){
            $list[$k]->cate_child = Category::where('pid',$v['id'])->field('id,name,image,image_from')->select();
        }
        //品牌分类
        $list2 = Category::where("type='brand' and pid='0'")->field('id,name,image,description,image_from')->select();
        foreach ($list2 as $k => $v){
            $list2[$k]->cate_child = Category::where('pid',$v['id'])->field('id,name,image,image_from')->select();
        }
        $return['category'] = $list;
        $return['brand'] = $list2;

        return json($return);
    }

    /**
     * get: 获取分类商品列表
     * path: category_goods_list
     * param:  category_id - {int} 分类ID
     * param:  by - {int} = [0|1|2] 排序类型(0: 推荐排序, 1: 销量, 2: 价格)
     * param:  order - {int} = [0|1] 排序规则(0: 从高到低, 1: 从低到高)
     * param:  or - {int} = [0|1] 普通分类or品牌(0: 普通分类, 1: 品牌)
     */
    public function category_goods_list()
    {
        $validate = new \think\Validate([
            'category_id' => 'require|number',
            'by' => 'in:0,1,2',
            'order' => 'in:0,1',
            'or' => 'in:0,1'
        ]);
        $rst = $validate->check(input());
        if (!$rst) {
            $this->error($validate->getError(),'');
        }
        if (input('order')==0) {
            $order = 'DESC';
        }else{
            $order = 'ASC';
        }
        switch (input('by')){
            case 0:
                $by = 'goods_id';
                $order = 'DESC';
                break;
            case 1:
                $by = 'sell_count';
                break;
            case 2:
                $by = 'shop_price';
                break;
        }
        if (input('or') == 0) {
            $return['goods_list'] = MallGoods::where('cat_id',input('category_id'))->field('goods_id,goods_name,shop_price,goods_images,from,sell_count')->order($by,$order)->select();
        }else{
            $return['goods_list'] = MallGoods::where('brand_id',input('category_id'))->field('goods_id,goods_name,shop_price,goods_images,from,sell_count')->order($by,$order)->select();
        }

        $return['category_detail'] = Category::get(input('category_id'));

        return json($return);
    }


}