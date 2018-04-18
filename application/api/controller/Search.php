<?php

namespace app\api\controller;
use app\api\model\BaseModel;
use app\api\model\MallArticle;
use app\api\model\MallGoods;
use app\common\controller\Api;

/**
 * swagger: 搜索
 */
class Search extends Api
{
    // 无需验证登录的方法
    protected $noNeedLogin = ['*'];
    // 无需要判断权限规则的方法
    protected $noNeedRight = ['*'];
    /**
     * get: 商品搜索列表
     * path: search_goods
     * param:  keywords - {string} 关键字
     * param:  by - {int} = [0|1|2] 排序类型(0: 推荐排序, 1: 销量, 2: 价格)
     * param:  order - {int} = [0|1] 排序规则(0: 从高到低, 1: 从低到高)
     */
    public function search_goods()
    {
        $validate = new \think\Validate([
            'keywords' => 'require|max:30',
            'by' => 'in:0,1,2',
            'order' => 'in:0,1',
        ]);
        $rst = $validate->check(input());
        if (!$rst) {
            $this->error($validate->getError(),'');
        }

        $keywords = input('keywords');
        if (input('by') == 0) {
            $list = MallGoods::where('goods_name','like',"%{$keywords}%")->select();
        }else{
            switch (input('order')){
                case 0:
                    $order = 'DESC';
                    break;
                case 1:
                    $order = 'ASC';
                    break;
            }

            if (input('by') == 0) {
                $by = 'sell_count';
            }else{
                $by = 'shop_price';
            }

            $list = MallGoods::where('goods_name','like',"%{$keywords}%")->order($by,$order)->select();
        }

        if (!$list) {
            $this->error('没有搜索到结果','');
        }

        $this->success('获取成功',$list);

    }

    /**
     * get: 商品搜索列表
     * path: search_article
     * param:  keywords - {string} 关键字
     */
    public function search_article()
    {
        $keywords = input('keywords');
        $list = MallArticle::where('title','like',"%{$keywords}%")->select();
        if (!$list) {
            $this->error('没有搜索到结果','');
        }
        $this->success('获取成功',$list);
    }

}