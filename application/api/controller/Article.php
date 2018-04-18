<?php

namespace app\api\controller;


use app\api\model\MallArticle;
use app\api\model\MallGoods;
use app\common\controller\Api;

/**
 * swagger: 文章
 */
class Article extends Api
{
    // 无需验证登录的方法
    protected $noNeedLogin = ['*'];
    // 无需要判断权限规则的方法
    protected $noNeedRight = ['*'];

    /**
     * get: 获取文章列表
     * path: all
     */
    public function all()
    {
        $list = MallArticle::order('article_id','DESC')->field('article_id,cover_image,title,intro')->select();
        $this->success('获取成功',$list);
    }

    /**
     * get: 获取文章详情
     * path: detail
     * param: article_id - {int} 文章ID
     */
    public function detail($article_id)
    {
        if (!$article_id ||!is_numeric($article_id)) {
            $this->error('参数错误','');
        }
        $detail = MallArticle::field('goods_id,top_image,title,content')->find($article_id);
        if (!$detail) {
            $this->error('文章不存在','');
        }
        $detail->active = MallGoods::active($detail->goods_id);
        $detail->goods = MallGoods::with('mallattrs')->field('goods_id,goods_name,goods_images,from')->find($detail->goods_id)->toArray();

        $this->success('获取成功',$detail);
    }
}