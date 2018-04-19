<?php
/**
 * Author: zhujun
 * DateTime: 2018/4/16 14:16
 */
namespace app\api\validate;

use app\lib\exception\ParameterException;
use think\Exception;
use think\Request;
use think\Validate;

class BaseValidate extends Validate
{
    protected $rule = [];
    public function goCheck()
    {
        $request = Request::instance();
        $params = $request->param();

        $validate = new Validate($this->rule);
        if (!$validate->check($params)) {
            throw new Exception($validate->getError());
        }
    }

    protected function isPositiveInreger($value,$rule = '',$data = '',$filed = '')
    {
        if (is_numeric($value) && ($value + 0) > 0) {
            return true;
        }else{
            return false;
        }
    }
    protected function isNotEmpty($value,$rule = '',$data = '',$filed = '')
    {
        if (empty($value)) {
            return false;
        }else{
            return true;
        }
    }

}