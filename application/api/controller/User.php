<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/8
 * Time: 11:51
 */

namespace app\api\controller;


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
            $info['status'] = 0;
            $info['msg'] = '该用户还未注册';
            $info['data'] = [];
            return $this->success('返回成功',$info);
        }
        //头像昵称每个月更新一次
        if ($userObj && (!$userObj->wx_name ||!$userObj->wx_headimage||$userObj->update_time+30*24*3600)<time()) {
            $info['status'] = 2;
            $info['msg'] = '需要继续上传昵称头像';
            $info['data'] = $userObj->toArray();
            return $this->success('返回成功',$info);
        }
        $info['status'] = 1;
        $info['mssage'] = '该用户已存在且不用上传头像昵称';
        $info['data'] = $userObj->toArray();
        return $this->success('返回成功',$info);
    }

    /**
     * 用户注册及更新头像昵称方法
     *
     * @ApiMethod (POST)
     * @ApiParams (name="open_id", type="string", required=true, description="open_id")
     * @ApiParams (name="wx_name", type="string", required=true, description="微信昵称")
     * @ApiParams (name="wx_headimage", type="file", required=true, description="微信头像")
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
    public function register($open_id,$wx_name)
    {
        if (!$open_id || !$wx_name) {
            throw new Exception('信息填写不完整');
        }
        $model = new MallUser();
        $userObj = $model::get(['open_id'=>$open_id]);
        if ($userObj && $userObj->wx_name && $userObj->wx_headimage && $userObj->update_time+30*24*3600>time()) {
            throw new Exception('该用户暂不需要修改状态');
        }
        $filePath = $this->upload('wx_headimage');
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
            $this->error('更新用户信息失败');
        }else{
            $this->success('成功');
        }
    }


}