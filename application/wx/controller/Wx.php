<?php
/**
 * Author: zhujun
 * DateTime: 2018/4/11 17:05
 */
namespace app\wx\controller;

use think\Controller;
use think\Exception;

class Wx extends Controller
{
    public function getopenid($code) {
        $adminObj = db('admin')->field('appid,appsecret')->find();
        if (!$adminObj) {
            throw new Exception('服务器获取信息失败');
        }
        $appid = $adminObj['appid'];
        $AppSecret = $adminObj['appsecret'];
        $getses = $this -> getSession($appid, $AppSecret, $code);
        $openid = $getses -> openid;
        return $openid;
    }

    public function getSession($appid, $AppSecret, $code) {
        $url = 'https://api.weixin.qq.com/sns/jscode2session?appid=' . $appid . '&secret=' . $AppSecret . '&js_code=' . $code . '&grant_type=authorization_code';
        $response = json_decode(file_get_contents($url));
        return $response;
    }
}