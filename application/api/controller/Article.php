<?php

namespace app\api\controller;


use app\api\model\MallArticle;
use app\api\model\MallGoods;
use app\common\controller\Api;
use app\lib\exception\MissException;
use app\lib\exception\ParamsException;

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
        $list = MallArticle::order('article_id','DESC')->field('article_id,cover_image,title,intro,from2')->select();
        return json($list);
    }

    /**
     * get: 获取文章详情
     * path: detail
     * param: article_id - {int} 文章ID
     */
    public function detail($article_id)
    {
        (new \app\api\validate\Article())->goCheck();
        $detail = MallArticle::field('goods_id,top_image,title,content,from')->find($article_id);
        if (!$detail) {
            throw new MissException();
        }
        $detail->active = MallGoods::active($detail->goods_id);
        $detail->goods = MallGoods::with('mallattrs')->field('goods_id,goods_name,goods_images,from')->find($detail->goods_id)->toArray();

        return json($detail);
    }
}