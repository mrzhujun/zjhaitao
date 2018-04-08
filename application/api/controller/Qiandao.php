<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/8
 * Time: 11:51
 */

namespace app\api\controller;
use app\common\controller\Api;


/**
 * 签到
 */
class Qiandao extends Api
{
    // 无需验证登录的方法
    protected $noNeedLogin = ['*'];
    // 无需要判断权限规则的方法
    protected $noNeedRight = ['*'];

    /**
     * 返回用户注册状态
     * @ApiMethod   (POST)
     * @ApiParams   (name="open_id", type="string", required=true, description="open_id")
     * @ApiReturn   (data="{
     *     'code':'1/0',
     *     'msg':'返回成功/失败',
     *     'time':'1523173262',
     *     'data':{
     *             'status':'0/1/2',
     *              'msg':'该用户还未注册/该用户已存在且不用上传头像昵称/需要继续上传昵称头像',
     *              'data':{'user_id':1,'open_id':'open_id1','wx_name':'name1','wx_headimage':'image1'}
     *     }
     *     }")
     */
    public function user_status($open_id)
    {
        
    }



}