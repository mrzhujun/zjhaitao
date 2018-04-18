<?php


namespace app\api\controller;


use app\api\model\MallUser;
use app\api\service\Token as ServiceToken;
use app\lib\exception\UserException;

/**
 * swagger: 用户
 */
class User extends Common
{
    // 无需验证登录的方法
    protected $noNeedLogin = ['*'];
    // 无需要判断权限规则的方法
    protected $noNeedRight = ['*'];



    /**
     * get: 返回用户注册状态(status判断是否需要上传头像昵称)
     * path: user_status
     * param: token - {string} token方法获取
     */
    public function user_status()
    {
        $user_id = ServiceToken::getCurrentUserId();
        $userObj = MallUser::get($user_id);
        if (!$userObj) {
            throw new UserException();
        }

        $userObj = MallUser::get($user_id);


        //头像昵称每个月更新一次
        if (!$userObj->wx_name ||$userObj->wx_headimage=='19t582g994.iask.in'||$userObj->update_time+30*24*3600<time()) {
            $return['status'] = 0;
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
     * post: 用户更新头像昵称
     * path: update
     * param: token - {string} token方法获取
     * param: wx_name - {string} 微信昵称
     * param: wx_headimage - {file} 微信头像
     */
    public function update($token,$wx_name)
    {
        $user_id = ServiceToken::getCurrentUserId();
        $userObj = MallUser::get($user_id);
        if (!$userObj) {
            throw new UserException();
        }
        if (!$token || !$wx_name) {
            $this->error('信息填写不完整');
        }

        if ($userObj->wx_name && $userObj->wx_headimage!='19t582g994.iask.in' && $userObj->update_time+30*24*3600>time()) {
            $this->error('该用户暂不需要修改状态',$userObj);
        }
        $filePath = $this->upload('wx_headimage',1);
        if ($filePath['code'] != 1) {
            $this->error('上传头像失败');
        }
        //存入数据库
        $_POST['wx_headimage'] = $filePath['data']['url'];
        $rst = $userObj->allowField(true)->save($_POST);
        if (!$rst) {
            $this->error('更新用户信息失败',$userObj);
        }else{
            $this->success('成功',$userObj);
        }
    }


    /**
     * get: 获取用户信息
     * path: user_info
     * param: token - {string} token方法获取
     */
    public function user_info()
    {
        $userObj = $this->check_user();
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