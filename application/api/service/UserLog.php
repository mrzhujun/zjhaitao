<?php
/**
 * Author: zhujun
 * DateTime: 2018/4/23 10:34
 */

namespace app\api\service;


class UserLog
{
    /**
     * 记录登陆日志
     * @param $ussr_id
     */
    public function record($user_id)
    {
        $today = date('Ymd');
        $is_log = db('mall_loginlog')->where("user_id={$user_id} and login_time='{$today}'")->count();
        if ($is_log) {
            db('mall_loginlog')->where("user_id={$user_id} and login_time='{$today}'")->setInc('tips');
        }else{
            db('mall_loginlog')->insert(['user_id'=>$user_id,'login_time'=>$today,'login_time_int'=>time()]);
        }
    }
}