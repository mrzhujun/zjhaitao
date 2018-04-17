<?php

namespace app\api\controller;


use app\api\model\MallCart;
use app\api\model\MallGoods;
use app\api\model\MallUser;
use app\common\controller\Api;
use app\api\validate\Cart as ValidateCart;
use app\api\service\Token as ServiceToken;
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

        $user_id = ServiceToken::getCurrentUserId();
        $userObj = MallUser::get($user_id);
        if (!$userObj) {
            throw new UserException();
        }
        //显示购物车数据
        $list = MallCart::where('user_id','=',$user_id)->select();
        foreach ($list as $k => $v){
            $list[$k]['goods_detail'] = MallCart::oneDetail($v->goods_id,$v->spec_id);
        }
        $this->success('获取成功',$list,200);
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
        $userObj = $this->check_user();
        $user_id  = $userObj->user_id;
        //判断购物车商品是否足够
        //添加购物车
        $goodsObj = MallGoods::where('goods_id','=',input('goods_id'))->field('goods_count')->find();
        if (!$goodsObj->checkGoodsStock(input('goods_id'),input('num'))) {
            $this->error('商品库存不足','',404);
        }
        $params = $this->request->param();
        $params['user_id'] = $user_id;
        $cartModel = new MallCart($params);
        $result = $cartModel->allowField(true)->save();

        //显示购物车数据
        $list = MallCart::all('user_id','=',$user_id)->select();
        foreach ($list as $k => $v){
            $list[$k]['goods_detail'] = MallCart::oneDetail($v->goods_id,$v->spec_id);
        }
        if (!$result) {
            $this->error('添加失败',$list,500);
        }
        $this->success('添加成功',$list,201);
    }
}