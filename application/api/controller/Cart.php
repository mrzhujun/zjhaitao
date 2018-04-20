<?php

namespace app\api\controller;


use app\api\model\MallCart;
use app\api\model\MallGoods;
use app\api\model\MallUser;
use app\api\validate\CartDelete;
use app\common\controller\Api;
use app\api\validate\CartAdd as ValidateCartAdd;
use app\api\validate\Cart as ValidateCart;
use app\lib\exception\CartException;
use app\lib\exception\ForbiddenException;
use app\lib\exception\GoodsException;
use app\lib\exception\MissException;
use app\lib\exception\SuccessMessage;
use app\lib\exception\UserException;
use think\Exception;

/**
 * swagger: 购物车
 */
class Cart extends Common
{
    // 无需验证登录的方法
    protected $noNeedLogin = ['*'];
    // 无需要判断权限规则的方法
    protected $noNeedRight = ['*'];

    /**
     * get: 购物车列表
     * path: cart_list
     * param: token - {string} token方法获取
     */
    public function cart_list()
    {
        $userObj = $this->check_user();
        //显示购物车数据
        $list = MallCart::cart_list($userObj->user_id);
        return json($list);
    }


    /**
     * post: 新增购物车
     * path: add
     * param: token - {string} token方法获取
     * param: goods_id - {int} 商品id
     * param: spec_id - {int} 规格id
     * param: num - {int} 数量
     */
    public function add()
    {
        (new ValidateCartAdd())->goCheck();
        $userObj = $this->check_user();
        $user_id  = $userObj->user_id;
        //判断购物车商品是否足够
        //添加购物车
        $goodsObj = MallGoods::where('goods_id','=',input('goods_id'))->field('goods_count')->find();
        if (!$goodsObj) {
            throw new GoodsException();
        }

        $params = $this->request->param();
        $params['user_id'] = $user_id;
        $cartModel = new MallCart($params);
        $result = $cartModel->allowField(true)->save();

        //显示购物车数据
        $list = $cartModel::cart_list($user_id);
        throw new SuccessMessage();
    }

    /**
     * delete: 删除购物车
     * path: delete
     * param: token - {string} token方法获取
     * param: cart_id - {int} 购物车id
     */
    public function delete()
    {
        (new CartDelete())->goCheck();
        $userObj = $this->check_user();
        $cartObj = MallCart::where('cart_id','=',input('cart_id'))->where('user_id','=',$userObj->user_id)->find();
        if (!$cartObj) {
            throw new MissException();
        }
        $cartObj->delete();
        throw new SuccessMessage();
    }

    /**
     * put: 修改购物车
     * path: edit
     * param: token - {string} token方法获取
     * param: cart_id - {int} 购物车id
     * param: num - {int} 商品数量
     * param: spec_id - {int} 商品规格id
     */
    public function edit()
    {
        (new ValidateCart())->goCheck();
        $userObj = $this->check_user();
        $cartObj = MallCart::where('cart_id','=',input('cart_id'))->find();
        if (!$cartObj) {
            throw new MissException([
                'code' => 404,
                'msg' => '所请求的购物车不存在',
                'errorCode' => 70000
            ]);
        }

        if ($cartObj->user_id != $userObj->user_id) {
            throw new ForbiddenException();
        }
        $cartObj->num = input('num');
        $cartObj->spec_id = input('spec_id');
        $cartObj->save();
        throw new SuccessMessage();
    }
}