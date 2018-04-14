<?php


namespace app\api\controller;


use app\api\model\MallUser;
use app\wx\controller\Wx;
use think\Exception;

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
     * get: 获取用户open_id
     * path: getopenid
     * param: code - {string} 微信登陆获取到的code
     */
    public function getopenid($code) {
        $wx = new Wx();
        try {
            $open_id = $wx->getopenid($code);
        }catch (Exception $e){
            $this->error($e->getMessage(),'',500);
        }
        $this->success('获取成功',$open_id,201);
    }


    /**
     * get: 返回用户注册状态
     * path: user_status
     * param: open_id - {string} open_id
     */
    public function user_status()
    {
        $open_id = $this->request->param()['open_id'];

        $userObj = MallUser::where("open_id='{$open_id}'")->field('user_id,open_id,wx_name,wx_headimage,update_time')->find();
        if (!$userObj) {
            $return['status'] = 0;
            $return['msg'] = '该用户还未注册';
            $return['data'] = [];
            return $this->success('返回成功',$return,200);
        }
        //头像昵称每个月更新一次
        if ($userObj && (!$userObj->wx_name ||!$userObj->wx_headimage||$userObj->update_time+30*24*3600)<time()) {
            $return['status'] = 2;
            $return['msg'] = '需要继续上传昵称头像';
            $return['data'] = $userObj->toArray();
            return $this->success('返回成功',$return,200);
        }
        $return['status'] = 1;
        $return['mssage'] = '该用户已存在且不用上传头像昵称';
        $return['data'] = $userObj->toArray();
        return $this->success('返回成功',$return,200);
    }


    /**
     * post: 用户注册及更新头像昵称
     * path: register
     * param: open_id - {string} open_id
     * param: wx_name - {string} 微信昵称
     * param: wx_headimage - {file} 微信头像
     */
    public function register($open_id,$wx_name)
    {
        if (!$open_id || !$wx_name) {
            $this->error('信息填写不完整',400);
        }
        $model = new MallUser();
        $userObj = $model::where(['open_id'=>$open_id])->field('user_id,wx_name,wx_headimage,jifen,jifen_total,update_time')->find();
        if ($userObj && $userObj->wx_name && $userObj->wx_headimage && $userObj->update_time+30*24*3600>time()) {
            $this->error('该用户暂不需要修改状态',$userObj,400);
        }
        $filePath = $this->upload('wx_headimage',1);
        if ($filePath['code'] != 1) {
            $this->error('上传头像失败',500);
        }
        //存入数据库
        $_POST['wx_headimage'] = $filePath['data']['url'];
        if (!$userObj) {
            $rst = $model->allowField(true)->save($_POST);
        }else{
            $rst = $model->allowField(true)->save($_POST,['open_id'=>$open_id]);
        }
        if (!$rst) {
            $this->error('更新用户信息失败',$userObj,500);
        }else{
            $this->success('成功',$userObj,201);
        }
    }


    /**
     * get: 获取用户信息
     * path: user_info
     * param: user_id - {int} 用户user_id
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
        $this->success('获取成功',$userObj,200);
    }



}