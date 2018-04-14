<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/8
 * Time: 11:45
 */

namespace app\api\model;


use think\Exception;
use think\Model;

class MallUser extends BaseModel
{
    protected $autoWriteTimestamp = true;
    protected $hidden = ['create_time','update_time','from','user_id'];

    public function getWxHeadimageAttr($value,$data)
    {
        return self::returnImageAttr($value,$data['from']);
    }
    /**
     * 签到增加用户积分
     */
    public function jifen_add($user_id)
    {
        $modelQiandao = new MallQiandao();
        $month = date('Ym');
        $monthQiandao = $modelQiandao->where("user_id='{$user_id}' and date like '{$month}%'")->field('date')->order('id DESC')->limit(1)->select();
        $userObj = $this->where("user_id={$user_id}")->field('jifen,jifen_total,jifen_month')->find();

        //计算昨天的日期
        $yestoday = date('Ymd',strtotime('-1 day'));
        $dayQiandao = $modelQiandao->where("user_id='{$user_id}' and date='{$yestoday}'")->find();

        //昨日未签到则累计签到天数归1
        if (!$dayQiandao) {
            $keepDate = 1;
        }else{ //昨日已签到则累计天数增加
            $keepDate = $dayQiandao->keep_date+1<=10?$dayQiandao->keep_date+1:10;
        }

        //本月签到则月积分累加
        if ($monthQiandao) {
            $jifenMonth = $userObj['jifen_month'] + $keepDate * 10;
        }else{//本月未签到月积分清零
            $jifenMonth = $keepDate * 10;
        }

        try {
            $this::where(['user_id' => $user_id])->setInc('jifen', $keepDate * 10, 2);
            $this::where(['user_id' => $user_id])->setInc('jifen_total', $keepDate * 10, 2);
            $this::where(['user_id' => $user_id])->update(['jifen_month' => $jifenMonth]);
        }catch (Exception $e)
        {
            $return['status'] = false;
            $return['msg'] = $e->getMessage();
            return $return;
        }

            $return['status'] = true;
            $return['msg'] = '签到成功';
            $return['keepdate'] = $keepDate;
            return $return;

    }

    public function malladdresss()
    {
        return $this->hasMany('MallAddress','user_id')->field('address_id,name,phone,address,address_detail,is_default');
    }
}