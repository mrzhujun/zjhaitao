<?php

namespace app\api\controller;

use app\api\model\MallBanner;
use app\common\controller\Api;
use app\api\validate\Banner as ValidateBanner;

/**
 * swagger: banner
 */
class Banner extends Api
{
    // 无需验证登录的方法
    protected $noNeedLogin = ['*'];
    // 无需要判断权限规则的方法
    protected $noNeedRight = ['*'];
    /**
     * get: 获取banner列表
     * path: banner_list
     * param: in - {int} = [0|1] 哪里的banner 0=首页
     */
    public function banner_list($in)
    {
        (new ValidateBanner())->goCheck();
        $list = MallBanner::where("banner_in='{$in}'")->select();
        return json($list);

    }
}
