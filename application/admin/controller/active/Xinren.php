<?php

namespace app\admin\controller\active;

use app\common\controller\Backend;

/**
 * 活动管理
 *
 * @icon fa fa-circle-o
 */
class Xinren extends Backend
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
            $arr = [];
            foreach ($list as $k => $v){
                if ($v['active_type'] != 1) {
                    unset($list[$k]);
                    $n++;
                }else{
                    $goodsObj = db('mall_goods')->where("goods_id={$v['goods_id']}")->field('goods_images,promote_price,shop_price,goods_name,promote_end_time')->find();
                    $list[$k]['goods_image'] = explode(',',$goodsObj['goods_images'])[0];
                    if ($goodsObj['promote_end_time']>time()) {
                        $list[$k]['active_price'] = $goodsObj['promote_price'];
                    }else{
                        $list[$k]['active_price'] = '-';
                    }

                    $list[$k]['shop_price'] = $goodsObj['shop_price'];
                    $list[$k]['goods_name'] = $goodsObj['goods_name'];
                    $arr[] = $list[$k];
                }
            }
            $result = array("total" => $total-$n, "rows" => $arr);

            return json($result);
        }
        return $this->view->fetch();
    }
    /**
     * 添加
     */
    public function add()
    {
        if ($this->request->isPost())
        {
            $params = $this->request->post("row/a");
            if ($params)
            $goodsObj = db('mall_goods')->where("goods_id={$params['goods_id']}")->field('is_promote,promote_end_time')->find();
                if($goodsObj['promote_end_time'] > time()){
                    $this->error('该商品正在活动中');
                }

            {
                if ($this->dataLimit && $this->dataLimitFieldAutoFill)
                {
                    $params[$this->dataLimitField] = $this->auth->id;
                }
                try
                {
                    //是否采用模型验证
                    if ($this->modelValidate)
                    {
                        $name = basename(str_replace('\\', '/', get_class($this->model)));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : true) : $this->modelValidate;
                        $this->model->validate($validate);
                    }
                    $result = $this->model->allowField(true)->save($params);
                    db('mall_goods')->where("goods_id={$params['goods_id']}")->update(['active_id'=>$this->model->active_id,'promote_price'=>$params['active_price'],'promote_start_time'=>strtotime($params['start_time']),'promote_end_time'=>strtotime($params['end_time'])]);
                    if ($result !== false)
                    {
                        $this->success();
                    }
                    else
                    {
                        $this->error($this->model->getError());
                    }
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
     * 编辑
     */
    public function edit($ids = NULL)
    {
        $row = $this->model->get($ids);
        if (!$row)
            $this->error(__('No Results were found'));
        $goodsObj = db('mall_goods')->where("goods_id={$row->goods_id}")->field('promote_price,goods_name')->find();
        $row->active_price = $goodsObj['promote_price'];
        $row->goods_name = $goodsObj['goods_name'];

        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds))
        {
            if (!in_array($row[$this->dataLimitField], $adminIds))
            {
                $this->error(__('You have no permission'));
            }
        }
        if ($this->request->isPost())
        {
            $params = $this->request->post("row/a");
            if ($params)
            {
                try
                {
                    //是否采用模型验证
                    if ($this->modelValidate)
                    {
                        $name = basename(str_replace('\\', '/', get_class($this->model)));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : true) : $this->modelValidate;
                        $row->validate($validate);
                    }
                    $result = $row->allowField(true)->save($params);
                    if ($result !== false)
                    {
                        db('mall_goods')->where("goods_id={$params['goods_id']}")->update(['promote_price'=>$params['active_price'],'promote_end_time'=>strtotime($params['end_time'])]);
                        $this->success();
                    }
                    else
                    {
                        $this->error($row->getError());
                    }
                }
                catch (\think\exception\PDOException $e)
                {
                    $this->error($e->getMessage());
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }


    /**
     * 删除 同步商品伙同
     */
    public function del($ids = "")
    {
        if ($ids)
        {
            $pk = $this->model->getPk();
            $adminIds = $this->getDataLimitAdminIds();
            if (is_array($adminIds))
            {
                $count = $this->model->where($this->dataLimitField, 'in', $adminIds);
            }
            $list = $this->model->where($pk, 'in', $ids)->select();
            $count = 0;
            foreach ($list as $k => $v)
            {
                //同步该商品活动
                db('mall_goods')->where("goods_id={$v->goods_id}")->update(['promote_end_time'=>0]);
                $count += $v->delete();
            }
            if ($count)
            {
                $this->success();
            }
            else
            {
                $this->error(__('No rows were deleted'));
            }
        }
        $this->error(__('Parameter %s can not be empty', 'ids'));
    }
}
