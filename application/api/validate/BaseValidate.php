<?php
/**
 * Author: zhujun
 * DateTime: 2018/4/16 14:16
 */
namespace app\api\validate;

use app\lib\exception\ParamsException;
use think\Request;
use think\Validate;

class BaseValidate extends Validate
{
    protected $rule = [];
    public function goCheck()
    {
        $request = Request::instance();
        $params = $request->param();
        if (!$this->check($params)) {
            $exception = new ParamsException(
                [
                    // $this->error有一个问题，并不是一定返回数组，需要判断
                    'msg' => is_array($this->error) ? implode(
                        ';', $this->error) : $this->error,
                ]);
            throw $exception;
        }else{
            return true;
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

    public function getDataByRule($arrays)
    {
        if (array_key_exists('user_id', $arrays) | array_key_exists('uid', $arrays)) {
            // 不允许包含user_id或者uid，防止恶意覆盖user_id外键
            throw new ParamsException([
                'msg' => '参数中包含有非法的参数名user_id或者uid'
            ]);
        }
        $newArray = [];
        foreach ($this->rule as $key => $value) {
            $newArray[$key] = $arrays[$key];
        }
        return $newArray;
    }

    //没有使用TP的正则验证，集中在一处方便以后修改
    //不推荐使用正则，因为复用性太差
    //手机号的验证规则
    protected function isMobile($value)
    {
        $rule = '^1(3|4|5|7|8)[0-9]\d{8}$^';
        $result = preg_match($rule, $value);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }



}