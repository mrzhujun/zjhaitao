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
        $list = MallArticle::with('goodss')->order('article_id','DESC')->select();

        $this->success('获取成功',$list,'200');
    }
}