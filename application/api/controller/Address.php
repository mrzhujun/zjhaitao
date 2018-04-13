<?php

namespace app\api\controller;


use app\api\model\MallAddress;
use app\common\controller\Api;

/**
 * swagger: 用户地址
 */
class Address extends Api
{
    // 无需验证登录的方法
    protected $noNeedLogin = ['*'];
    // 无需要判断权限规则的方法
    protected $noNeedRight = ['*'];

    /**
     * post: 新增地址
     * path: address_add
     * param: user_id - {int} 用户id
     * param: name - {string} 收货人
     * param: phone - {int} 手机号
     * param: p - {string} 省
     * param: c - {string} 市
     * param: t - {string} 区
     * param: address_detail - {string} 具体地址
     * param: is_default - {int}  = [0|1|2|3|4] 是否设为默认(0: 否, 1: 是)
     */
    public function address_add()
    {
        $validate = new \think\Validate([
            'user_id' => 'require|number',
            'phone' => 'require|length:11',
            'p' => 'require',
            'c' => 'require',
            't' => 'require',
            'address_detail' => 'require',
            'is_default' => 'in:0,1'
        ]);
        if (!$validate->check($_POST)) {
            $this->error($validate->getError(),'',400);
        }

        $_POST['address'] = $_POST['p'].','.$_POST['c'].','.$_POST['t'];
        $model = new MallAddress($_POST);
        if (!$model->allowField(true)->save()) {
            $this->error('数据保存出错','','500');
        }
        $this->success('创建成功',$model,'201');
    }


    /**
     * put: 修改地址
     * path: address_edit
     * param: address_id - {int} 地址id
     * param: name - {string} 收货人
     * param: phone - {int} 手机号
     * param: p - {string} 省
     * param: c - {string} 市
     * param: t - {string} 区
     * param: address_detail - {string} 具体地址
     * param: is_default - {int}  = [0|1|2|3|4] 是否设为默认(0: 否, 1: 是)
     */
    public function address_edit()
    {
        $params = $this->request->param();
        $validate = new \think\Validate([
            'address_id' => 'require|number',
            'phone' => 'require|length:11',
            'p' => 'require',
            'c' => 'require',
            't' => 'require',
            'address_detail' => 'require',
            'is_default' => 'in:0,1'
        ]);
        if (!$validate->check($params)) {
            $this->error($validate->getError(),'',400);
        }

        $params['address'] = $params['p'].','.$params['c'].','.$params['t'];
        $addressObj = MallAddress::get($params['address_id']);
        if ($addressObj->name == $params['name'] && $addressObj->phone == $params['phone'] && $addressObj->address_detail == $params['address_detail'] && $addressObj->address = $params['p'].','.$params['c'].','.$params['t'] && $addressObj->is_default == $params['is_default']) {
            $this->error('数据未改变',$addressObj,'400');
        }
        $rst = $addressObj->allowField('name,phone,address,address_detail,is_default')->save($params);
        if (!$rst) {
            $this->error('数据保存出错','','500');
        }
        $this->success('更改成功',$addressObj,'201');
    }



}