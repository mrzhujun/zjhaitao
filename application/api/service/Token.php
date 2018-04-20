<?php
/**
 * Author: zhujun
 * DateTime: 2018/4/16 15:58
 */

namespace app\api\service;


use app\lib\exception\MissException;
use app\lib\exception\TokenException;
use think\Cache;
use think\Exception;
use think\Request;

class Token
{
    public static function generateToken()
    {
        $randsChars = getRandChars();
        //用三组字符串进行md5加密返回
        $timestmap = $_SERVER['REQUEST_TIME_FLOAT'];
        $salt = config('secure.token_salt');

        return md5($randsChars.$timestmap.$salt);
    }

    /**
     * 通过token获取缓存中的用户信息
     * @param $key
     * @return mixed
     * @throws Exception
     * @throws TokenException
     */
    public static function getCurrentTokenVal($key)
    {
        $token = Request::instance()
            ->param('token');
        $vars = Cache::get($token);
        if (!$vars) {
            throw new TokenException();
        }else{
            if (!is_array($vars)) {
                $vars = json_decode($vars,true);
            }

            if (array_key_exists($key,$vars)) {
                return $vars[$key];
            }else{
                throw new MissException([
                    'msg' => '尝试获取的Token变量不存在',
                    'errorCode' => 10005
                ]);
            }

        }
    }

    /**
     * 获取用户id
     * @return mixed
     * @throws Exception
     * @throws TokenException
     */
    public static function getCurrentUserId()
    {
        $user_id = self::getCurrentTokenVal('user_id');
        return $user_id;

    }


}