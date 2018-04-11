<?php
/**
 * Author: zhujun
 * DateTime: 2018/4/11 9:51
 */

namespace app\api\controller;


use app\api\model\MallAddress;
use app\common\controller\Api;

/**
 * 用户地址
 */
class Address extends Api
{
    // 无需验证登录的方法
    protected $noNeedLogin = ['*'];
    // 无需要判断权限规则的方法
    protected $noNeedRight = ['*'];

    /**
     * 更新地址
     * @ApiMethod   (PUT)
     * @ApiParams   (name="address_id", type="integer", required=true, description="地址id")
     * @ApiParams   (name="name", type="string", required=true, description="收货人")
     * @ApiParams   (name="phone", type="integer", required=true, description="手机号")
     * @ApiParams   (name="p", type="string", required=true, description="省")
     * @ApiParams   (name="c", type="string", required=true, description="市")
     * @ApiParams   (name="t", type="string", required=true, description="区")
     * @ApiParams   (name="address_detail", type="string", required=true, description="具体地址")
     * @ApiParams   (name="is_default", type="int", required=true, description="[0/1]是否设为默认")
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


    /**
     * 新增地址
     * @ApiMethod   (POST)
     * @ApiParams   (name="user_id", type="int", required=true, description="用户id")
     * @ApiParams   (name="name", type="string", required=true, description="收货人")
     * @ApiParams   (name="phone", type="int", required=true, description="手机号")
     * @ApiParams   (name="p", type="string", required=true, description="省")
     * @ApiParams   (name="c", type="string", required=true, description="市")
     * @ApiParams   (name="t", type="string", required=true, description="区")
     * @ApiParams   (name="address_detail", type="string", required=true, description="具体地址")
     * @ApiParams   (name="is_default", type="int", required=true, description="[0/1]是否设为默认")
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

}