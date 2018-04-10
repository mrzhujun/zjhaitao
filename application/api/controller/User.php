<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/8
 * Time: 11:51
 */

namespace app\api\controller;


use app\api\model\MallQiandao;
use app\api\model\MallUser;
use app\api\controller\Common;
use think\Exception;

/**
 * 用户
 */
class User extends Common
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
        $userObj = MallUser::where("open_id='{$open_id}'")->field('user_id,open_id,wx_name,wx_headimage,update_time')->find();
        if (!$userObj) {
            $return['status'] = 0;
            $return['msg'] = '该用户还未注册';
            $return['data'] = [];
            return $this->success('返回成功',$return);
        }
        //头像昵称每个月更新一次
        if ($userObj && (!$userObj->wx_name ||!$userObj->wx_headimage||$userObj->update_time+30*24*3600)<time()) {
            $return['status'] = 2;
            $return['msg'] = '需要继续上传昵称头像';
            $return['data'] = $userObj->toArray();
            return $this->success('返回成功',$return);
        }
        $return['status'] = 1;
        $return['mssage'] = '该用户已存在且不用上传头像昵称';
        $return['data'] = $userObj->toArray();
        return $this->success('返回成功',$return);
    }

    /**
     * 用户注册及更新头像昵称
     *
     * @ApiMethod (POST)
     * @ApiParams (name="open_id", type="string", required=true, description="open_id")
     * @ApiParams (name="wx_name", type="string", required=true, description="微信昵称")
     * @ApiParams (name="wx_headimage", type="file", required=true, description="微信头像")
     * @ApiReturn   (data="{
     *     'code':'1/0',
     *     'msg':'更新成功/更新用户信息失败',
     *     'time':'1523173262',
     *     'data':{
     *              'user_id': 30,
     *              'wx_name': '123',
     *              'wx_headimage': '/assets/wx_headimage/00e3a19b3ce8a84dcaa16146233e9e9a.jpg',
     *              'jifen': 150,
     *              'jifen_total': 150,
     *              'update_time': 1523355625
     *     }
     *     }")
     */
    public function register($open_id,$wx_name)
    {
        if (!$open_id || !$wx_name) {
            $this->error('信息填写不完整');
        }
        $model = new MallUser();
        $userObj = $model::where(['open_id'=>$open_id])->field('user_id,wx_name,wx_headimage,jifen,jifen_total,update_time')->find();
        if ($userObj && $userObj->wx_name && $userObj->wx_headimage && $userObj->update_time+30*24*3600>time()) {
            $this->error('该用户暂不需要修改状态',$userObj);
        }
        $filePath = $this->upload('wx_headimage',1);
        if ($filePath['code'] != 1) {
            $this->error('上传头像失败');
        }
        //存入数据库
        $_POST['wx_headimage'] = $filePath['data']['url'];
        if (!$userObj) {
            $rst = $model->allowField(true)->save($_POST);
        }else{
            $rst = $model->allowField(true)->save($_POST,['open_id'=>$open_id]);
        }
        if (!$rst) {
            $this->error('更新用户信息失败',$userObj);
        }else{
            $this->success('成功',$userObj);
        }
    }

    /**
     * 获取用户信息
     *
     * @ApiParams (name="user_id", type="int", required=true, description="用户user_id")
     * @ApiReturn   (data="{
     *     'code':'1/0',
     *     'msg':'获取成功/失败',
     *     'time':'1523173262',
     *     'data':{
     *              'data':{'user_id':1,'open_id':'open_id1','wx_name':'name1','wx_headimage':'image1'}
     *     }
     *     }")
     */
    public function user_info($user_id)
    {
        $userObj = MallUser::get($user_id);
        if (!$userObj) {
            $this->error('该用户不存在');
        }
        switch ($userObj->jifen_total){
            case $userObj->jifen_total<50000:
                $userObj->vip = '普通会员';
                break;
            case $userObj->jifen_total>=50000 && $userObj->jifen_total<100000:
                $userObj->vip = '黄金会员';
                break;
            case $userObj->jifen_total>=100000 && $userObj->jifen_total<200000:
                $userObj->vip = '铂金会员';
                break;
            case $userObj->jifen_total>=200000 && $userObj->jifen_total<500000:
                $userObj->vip = '砖石会员';
                break;
            case $userObj->jifen_total>=500000:
                $userObj->vip = '翡翠会员';
                break;
        }
        $this->success('获取成功',$userObj);
    }



}