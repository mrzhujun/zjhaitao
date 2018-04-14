<?php

namespace app\api\controller;


use app\api\model\MallGoods;
use app\common\controller\Api;
use \app\api\model\Category;

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
        $list = Category::where("type='category' and pid='0'")->field('id,name,image,image_from')->select();
        foreach ($list as $k => $v){
            $list[$k]->cate_child = Category::where('pid',$v['id'])->select();
        }
        $this->success('获取成功',$list,200);
    }

    /**
     * get: 获取分类商品列表
     * path: category_goods_list
     * param:  category_id - {int} 分类ID
     * param:  by - {int} = [0|1|2] 排序类型(0: 推荐排序, 1: 销量, 2: 价格)
     * param:  order - {int} = [0|1] 排序规则(0: 从高到低, 1: 从低到高)
     */
    public function category_goods_list()
    {
        $validate = new \think\Validate([
            'category_id' => 'require|number',
            'by' => 'in:0,1,2',
            'order' => 'in:0,1'
        ]);
        $rst = $validate->check(input());
        if (!$rst) {
            $this->error($validate->getError(),'',400);
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
        $return['goods_list'] = MallGoods::where('cat_id',input('category_id'))->order($by,$order)->select();
        $return['category_detail'] = Category::get(input('category'));

        $this->success('获取成功',$return,200);
    }


}