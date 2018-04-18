<?php

namespace app\api\controller;

use app\api\model\MallBanner;
use app\common\controller\Api;

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
     * param: in - {int} = [0] 哪里的banner 0=首页
     */
    public function banner_list($in)
    {
        $validate = new \think\Validate([
            'in' => 'require|number'
        ]);
        if (!$validate->check(input())) {
            $this->error($validate->getError(),'');
        }
        $list = MallBanner::where("banner_in='{$in}'")->select();
        $this->success('返回成功',$list);

    }
}
