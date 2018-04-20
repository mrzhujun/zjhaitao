<?php

namespace app\api\controller;


use app\api\model\MallAddress;
use app\api\model\MallUser;
use app\api\service\Token as ServiceToken;
use app\api\validate\Address as ValidateAddress;
use app\lib\exception\DSuccessMessage;
use app\lib\exception\NoChangeException;
use app\lib\exception\ParamsException;
use app\lib\exception\SuccessMessage;
use app\lib\exception\UserException;

/**
 * swagger: 用户地址
 */
class Address extends Common
{
    // 无需验证登录的方法
    protected $noNeedLogin = ['*'];
    // 无需要判断权限规则的方法
    protected $noNeedRight = ['*'];

    /**
     * post: 新增地址
     * path: add
     * param: token - {string} token方法获取
     * param: name - {string} 收货人
     * param: phone - {int} 手机号
     * param: p - {string} 省
     * param: c - {string} 市
     * param: t - {string} 区
     * param: address_detail - {string} 具体地址
     * param: is_default - {int}  = [0|1|2|3|4] 是否设为默认(0: 否, 1: 是)
     */
    public function add()
    {
        (new ValidateAddress())->goCheck();
        $userObj = $this->check_user();
        $user_id  = $userObj->user_id;
        $_POST['address'] = $_POST['p'].','.$_POST['c'].','.$_POST['t'];
        $_POST['user_id'] = $user_id;
        $model = new MallAddress($_POST);
        $model->allowField(true)->save();
        throw new SuccessMessage();
    }


    /**
     * put: 修改地址
     * path: edit
     * param: token - {string} token方法获取
     * param: address_id - {int} 地址id
     * param: name - {string} 收货人
     * param: phone - {int} 手机号
     * param: p - {string} 省
     * param: c - {string} 市
     * param: t - {string} 区
     * param: address_detail - {string} 具体地址
     * param: is_default - {int}  = [0|1] 是否设为默认(0: 否, 1: 是)
     */
    public function edit()
    {
        $this->check_user();
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
            throw new ParamsException([
                'msg' => $validate->getError()
            ]);
        }

        $params['address'] = $params['p'].','.$params['c'].','.$params['t'];
        $addressObj = MallAddress::get($params['address_id']);
        if ($addressObj->name == $params['name'] && $addressObj->phone == $params['phone'] && $addressObj->address_detail == $params['address_detail'] && $addressObj->address = $params['p'].','.$params['c'].','.$params['t'] && $addressObj->is_default == $params['is_default']) {
            throw new NoChangeException();
        }
        $addressObj->allowField('name,phone,address,address_detail,is_default')->save($params);
        throw new SuccessMessage();
    }

    /**
     * delete: 删除地址
     * path: delete
     * param: token - {string} token方法获取
     * param: address_id - {int} 地址id
     */
    public function delete()
    {
        $user_id = ServiceToken::getCurrentUserId();
        $userObj = MallUser::get($user_id);
        if (!$userObj) {
            throw new UserException();
        }
        $params = $this->request->param();

        $validate = new \think\Validate([
            'address_id' => 'require|number',
        ]);
        if (!$validate->check($params)) {
            $this->error($validate->getError(),'');
        }
        MallAddress::destroy($params['address_id']);
        throw new SuccessMessage();
    }

    /**
     * get: 获取用户所有地址列表
     * path: address_list
     * param: token - {string} token方法获取
     */
    public function address_list()
    {
        $user_id = ServiceToken::getCurrentUserId();
        $userObj = MallUser::get($user_id);
        if (!$userObj) {
            throw new UserException();
        }
        $list = $userObj->malladdresss()->select();
        return json($list);
    }

}