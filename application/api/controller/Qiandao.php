<?php

namespace app\api\controller;
use app\api\model\MallQiandao;
use app\api\model\MallUser;
use app\common\controller\Api;


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
     * get: 今日签到
     * path: add
     * param: user_id - {int} user_id
     */
    public function add($user_id)
    {
        $modelQiandao = new MallQiandao();
        $isQiandao = $modelQiandao->where("user_id",$user_id)->where('date',date('Ymd'))->count();
        $thisMonth = date('Ym');
        $list = $modelQiandao->where("user_id={$user_id} and date like '{$thisMonth}%'")->field('date')->select();
        if ($isQiandao != 0) {
            $list = $this->array2array($list,'date');
            $this->error('已经签到',$list,403);
        }
        $userModel = new MallUser();
        $rst = $userModel->jifen_add($user_id);
        if (!$rst) {
            $list = $this->array2array($list,'date');
            $this->error('积分增加失败',$list,500);
        }
        $qiandao = MallQiandao::create([
            'user_id' => $user_id,
            'date' => date('Ymd'),
            'keep_date'=>$rst
        ]);
        $list = $modelQiandao->where("user_id={$user_id} and date like '{$thisMonth}%'")->field('date')->select();
        if ($qiandao) {
            $list = $this->array2array($list,'date');
            $this->success('签到成功',$list,201);
        }else{
            $this->error('签到失败');
        }
    }


    /**
     * get: 获取用户签到总览
     * path: index
     * param: user_id - {int} user_id
     */
    public function index($user_id)
    {
        $qiandaoModel = new MallQiandao();
        $list = $qiandaoModel->where("user_id={$user_id}")->field('date')->select();
        if (!$list) {
            $this->error('获取失败');
        }

        $this->success('获取成功',$list);
    }



}