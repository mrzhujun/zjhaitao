<?php

namespace app\api\controller;

use app\api\service\UserToken;
use app\api\validate\TokenGet;
use app\common\controller\Api;

/**
 * swagger: Token接口
 */
class Token extends Api
{

    protected $noNeedLogin = ['*'];
    protected $noNeedRight = '*';

    /**
     * post: 获取用户open_id
     * path: getToken
     * param: code - {string} 微信登陆获取到的code
     */
    public function getToken($code)
    {
        (new TokenGet())->goCheck();
        $ut = new UserToken($code);
        $token = $ut->get($code);
        $return['token'] = $token;
        $this->success('',$return);
    }

}
