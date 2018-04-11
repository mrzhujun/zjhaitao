<?php
/**
 * Author: zhujun
 * DateTime: 2018/4/11 18:02
 */

namespace app\api\controller;


use app\api\model\MallArticle;
use app\api\model\MallGoods;
use app\common\controller\Api;

class Article extends Api
{
    // 无需验证登录的方法
    protected $noNeedLogin = ['*'];
    // 无需要判断权限规则的方法
    protected $noNeedRight = ['*'];

    /**
     * 获取文章列表
     * @ApiReturn   (data="{
     *     'code':'1/0',
     *     'msg':'返回成功/失败',
     *     'time':'1523173262',
     *     'data':'文章列表'
     *     }")
     */
    public function all()
    {
        $list = MallArticle::all(function ($query){
            $query->order('article_id','DESC');
        });
        foreach ($list as $k => $v){
            $v->goods;
        }
        $this->success('获取成功',$list,'200');
    }
}