<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/7
 * Time: 10:13
 */

namespace app\api\controller;


use app\common\controller\Api;
use think\controller\Rest;
use \app\api\model\News as N;

/**
 * 新闻测试接口
 */
class News extends Api
{
    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['*'];

    /**
     * 测试方法
     *
     * @ApiTitle （名称）
     * @ApiParams (name="id", type="integer", required=true, description="会员ID")
     * @ApiReturn   (data="{
     *  'code':'0',
     *  'mesg':'返回成功'
     *  'data':'data'
     * }")
     */
    public function read($id)
    {
        $model = new N();
        $data = $model->where('id',$id)->find();
        return json($data);
    }

}