<?php
/**
 * Author: zhujun
 * DateTime: 2018/4/20 14:20
 */

namespace app\api\service;


use app\lib\exception\ForbiddenException;

class UserAuth
{
    public function checkUserAuth($id1,$id2)
    {
        if ($id1 != $id2) {
            throw new ForbiddenException([
                'msg' => '不能操作他人的数据'
            ]);
        }
    }
}