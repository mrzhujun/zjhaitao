<?php
/**
 * Author: zhujun
 * DateTime: 2018/4/16 14:16
 */
namespace app\api\validate;

use think\Exception;
use think\Request;
use think\Validate;

class BaseValidate extends Validate
{
    public function goCheck()
    {
        //获取http传入的参数对这些参数进行检验
        $request = Request::instance();
        $params = $request->param();

        $result = $this->batch()->check($params);
        if (!$result) {
            // $this->error有一个问题，并不是一定返回数组，需要判断
            $msg = is_array($this->error) ? implode(';', $this->error) : $this->error;
            $return['msg'] = $msg;
            $return['status'] = false;
        }else{
            $return['status'] = true;
        }
        return $return;
    }

    protected function isPositiveInreger($value,$rule = '',$data = '',$filed = '')
    {
        if (is_numeric($value) && is_int($value+0) && ($value + 0) > 0) {
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