<?php
/**
 * Author: zhujun
 * DateTime: 2018/4/16 14:32
 */
namespace app\api\service;

use app\api\model\MallUser;
use app\lib\exception\TokenException;
use app\lib\exception\WxException;
use think\Exception;

class UserToken extends Token
{
    protected $code;
    protected $wxAppId;
    protected $wxAppSecret;
    protected $wxLoginUrl;

    function __construct($code)
    {
        $this->code = $code;
        $this->wxAppId = config('wx.app_id');
        $this->wxAppSecret = config('wx.app_secret');
        $this->wxLoginUrl = sprintf(config('wx.login_url'),
            $this->wxAppId,$this->wxAppSecret,$this->code);
    }

    public function get()
    {
        $result = curl_get($this->wxLoginUrl);
        $wxResult = json_decode($result,true);
        if (empty($wxResult)) {
            throw new WxException([
                'msg' => '获取session_key及open_id时异常，微信内部错误'
            ]);
        }else{
            $loginFail = array_key_exists('errcode',$wxResult);
            if ($loginFail) {
                throw new WxException([
                    'msg' => '微信服务器获取出错，错误原因:'.$wxResult['errmsg']
                ]);
            }else{
                return $this->grantToken($wxResult);
            }
        }

    }

    //拿到open_id对比数据库，生成令牌写入缓存，返回令牌
    private function grantToken($wxResult)
    {
        $open_id = $wxResult['openid'];

        $userObj = MallUser::getByOpenId($open_id);
        if ($userObj) {
            $user_id = $userObj->user_id;
        }else{
            $user_id = $this->newUser($open_id);
        }

        //记录登陆日志
        (new UserLog())->record($user_id);

        $cachedValue = $this->prepareCachedValue($wxResult,$user_id);
        $token = $this->saveToCache($cachedValue);
        return $token;
    }

    //写入缓存
    private function saveToCache($cachedValue)
    {
        $key = self::generateToken();
        $value = json_encode($cachedValue);
        $expire_in = config('setting.token_expire_in');

        $result = cache($key,$value,$expire_in);
        if (!$result) {
            throw new TokenException([
                'msg' => '服务器缓存异常',
                'errorCode' => 10005
            ]);
        }
        return $key;
    }

    private function prepareCachedValue($wxResult,$user_id)
    {
        $cachedValue = $wxResult;
        $cachedValue['user_id'] = $user_id;
        $cachedValue['scope'] = 16;
        return $cachedValue;
    }

    //写入一条user
    private function newUser($open_id)
    {
        $user = MallUser::create([
            'open_id' => $open_id
        ]);
        return $user->user_id;
    }

}