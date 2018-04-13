<?php
/**
 * Author: zhujun
 * DateTime: 2018/4/11 18:02
 */

namespace app\api\controller;


use app\api\model\MallArticle;
use app\api\model\MallGoods;
use app\common\controller\Api;

/**
 * 文章
 */
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
        $list = MallArticle::with('goodss')->order('article_id','DESC')->select();
        foreach ($list as $k => $v){
            $list[$k]['goodss']['goods_images'] = config('setting.img_prefix').$v['goodss']['goods_images'];
        }
        $this->success('获取成功',$list,'200');
    }
}