<?php

namespace app\api\controller;
use app\api\model\MallQiandao;
use app\api\model\MallUser;
use app\api\service\Token as ServiceToken;
use app\lib\exception\UserException;


/**
 * swagger: 签到
 */
class Qiandao extends Common
{
    // 无需验证登录的方法
    protected $noNeedLogin = ['*'];
    // 无需要判断权限规则的方法
    protected $noNeedRight = ['*'];


    /**
     * post: 今日签到
     * path: add
     * param: token - {string} token方法获取
     */
    public function add()
    {
        $user_id = ServiceToken::getCurrentUserId();
        $userObj = MallUser::get($user_id);
        if (!$userObj) {
            throw new UserException();
        }
        $modelQiandao = new MallQiandao();
        $isQiandao = $modelQiandao->where("user_id",$user_id)->where('date',date('Ymd'))->count();
        $thisMonth = date('Ym');
        $list = $modelQiandao->where("user_id={$user_id} and date like '{$thisMonth}%'")->field('date')->select();
        if ($isQiandao != 0) {
            $list = $this->array2array($list,'date');
            $this->error('已经签到',$list);
        }
        $userModel = new MallUser();
        $rst = $userModel->jifen_add($user_id);

        if (!$rst['status']) {
            $list = $this->array2array($list,'date');
            $this->error('积分增加失败'.$list['msg'],$list);
        }
        $qiandao = MallQiandao::create([
            'user_id' => $user_id,
            'date' => date('Ymd'),
            'keep_date'=>$rst['keepdate']
        ]);
        $list = $modelQiandao->where("user_id={$user_id} and date like '{$thisMonth}%'")->field('date')->select();
        $return['keepdate'] = $rst['keepdate'];
        $return['getjifen'] = $rst['getjifen'];
        $list = $this->array2array($list,'date');
        $return['datelist'] = $list;
        if ($qiandao) {
            $this->success('签到成功',$return);
        }else{
            $this->error('签到失败',$return);
        }
    }


    /**
     * get: 获取用户签到总览
     * path: index
     * param: token - {string} token方法获取
     */
    public function index()
    {
        $user_id = ServiceToken::getCurrentUserId();
        $userObj = MallUser::get($user_id);
        if (!$userObj) {
            throw new UserException();
        }
        $qiandaoModel = new MallQiandao();
        $list = $qiandaoModel->where("user_id={$user_id}")->field('date')->select();
        if (!$list) {
            $this->error('暂无数据',$list);
        }

        $list = $this->array2array($list,'date');
        $this->success('获取成功',$list);
    }



}