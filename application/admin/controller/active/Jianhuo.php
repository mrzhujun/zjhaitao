<?php

namespace app\admin\controller\active;

use app\common\controller\Backend;

/**
 * 活动管理
 *
 * @icon fa fa-circle-o
 */
class Jianhuo extends Backend
{
    
    /**
     * MallActive模型对象
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('MallActive');
        $this->view->assign("activeTypeList", $this->model->getActiveTypeList());
    }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */

    /**
     * 添加
     */
    public function add()
    {
        if ($this->request->isPost())
        {
            $params = $this->request->post("row/a");
            if ($params)
            {
                if ($this->dataLimit && $this->dataLimitFieldAutoFill)
                {
                    $params[$this->dataLimitField] = $this->auth->id;
                }
                try
                {
                    $goodsidArr = explode(',',$params['goods_id']);
                    $param['active_type'] = $params['active_type'];
                    foreach ($goodsidArr as $k => $v){
                        $param['goods_id'] = $v;
                        $count = db('mall_active')->where($param)->count();
                        if ($count == 0) {
                            $result = db('mall_active')->insert($param);
                            if (!$result)
                            {
                                $this->error();
                            }
                        }


                    }
                    $this->success();
                }
                catch (\think\exception\PDOException $e)
                {
                    $this->error($e->getMessage());
                }

            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        return $this->view->fetch();
    }

    /**
     * 查看
     */
    public function index()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax())
        {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('pkey_name'))
            {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                ->where($where)
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            $list = collection($list)->toArray();
            $n = 0;
            foreach ($list as $k => $v){
                if ($v['active_type'] != 2) {
                    unset($list[$k]);
                    $n++;
                }else{
                    $goodsObj = db('mall_goods')->where("goods_id={$v['goods_id']}")->field('goods_images,promote_price,shop_price,goods_name,promote_end_time')->find();
                    $list[$k]['goods_image'] = explode(',',$goodsObj['goods_images'])[0];
                    $list[$k]['shop_price'] = $goodsObj['shop_price'];
                    $list[$k]['goods_name'] = $goodsObj['goods_name'];
                }
            }
            $result = array("total" => $total-$n, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }
}
