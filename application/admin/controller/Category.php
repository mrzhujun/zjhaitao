<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use app\common\model\Category as CategoryModel;
use fast\Tree;

/**
 * 分类管理
 *
 * @icon fa fa-list
 * @remark 用于统一管理网站的所有分类,分类可进行无限级分类
 */
class Category extends Backend
{

    protected $model = null;
    protected $categorylist = [];
    protected $noNeedRight = ['selectpage'];

    public function _initialize()
    {
        parent::_initialize();
        $this->request->filter(['strip_tags']);
        $this->model = model('app\common\model\Category');

        $tree = Tree::instance();
        $tree->init(collection($this->model->order('weigh desc,id desc')->select())->toArray(), 'pid');
        $this->categorylist = $tree->getTreeList($tree->getTreeArray(0), 'name');
        $categorydata = [0 => ['type' => 'all', 'name' => __('None')]];
        foreach ($this->categorylist as $k => $v)
        {
            $categorydata[$v['id']] = $v;
        }
        $categorydata[0]['pid'] = 0;


        $this->view->assign("flagList", $this->model->getFlagList());
        $this->view->assign("typeList", CategoryModel::getTypeList());
        $this->view->assign("parentList", $categorydata);
    }

    /**
     * 查看
     */
    public function index()
    {
        if ($this->request->isAjax())
        {
            $search = $this->request->request("search");
            $type = $this->request->request("type");

            //构造父类select列表选项数据
            $list = [];

                foreach ($this->categorylist as $k => $v)
                {
                    if ($search) {
                        if ($v['type'] == $type && stripos($v['name'], $search) !== false || stripos($v['nickname'], $search) !== false)
                        {
                            if($type == "all" || $type == null) {
                                $list = $this->categorylist;
                            } else {
                                $list[] = $v;
                            }
                        }
                    } else {
                        if($type == "all" || $type == null) {
                            $list = $this->categorylist;
                        } else if ($v['type'] == $type){
                            $list[] = $v;
                        }

                    }

                }

            $total = count($list);

            foreach ($list as $k => $v){
                if ($v['type'] == 'category') {
                    $list[$k]['type'] = '分类';
                }
                if ($v['type'] == 'brand') {
                    $list[$k]['type'] = '品牌';
                }
            }
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * Selectpage搜索
     * 
     * @internal
     */
    public function selectpage()
    {
        return parent::selectpage();
    }

    /**
     * 查看品牌
     */
    public function index_brand()
    {
        if ($this->request->isAjax())
        {
            $search = $this->request->request("search");
            $type = $this->request->request("type");

            //构造父类select列表选项数据
            $list = [];

            foreach ($this->categorylist as $k => $v)
            {
                    if ($search) {
                        if ($v['type'] == $type && stripos($v['name'], $search) !== false || stripos($v['nickname'], $search) !== false)
                        {
                            if($type == "all" || $type == null) {
                                $list = $this->categorylist;
                            } else {
                                $list[] = $v;
                            }
                        }
                    } else {
                        if($type == "all" || $type == null) {
                            $list = $this->categorylist;
                        } else if ($v['type'] == $type){
                            $list[] = $v;
                        }

                    }
            }
            $list2 = [];
            foreach ($list as $k => $v){
                if ($v['type'] == 'brand') {
                    $list2[] = $v;
                }
            }
            $total = count($list2);
            $result = array("total" => $total, "rows" => $list2);

            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 查看品牌
     */
    public function index_cate()
    {
        if ($this->request->isAjax())
        {
            $search = $this->request->request("search");
            $type = $this->request->request("type");

            //构造父类select列表选项数据
            $list = [];

            foreach ($this->categorylist as $k => $v)
            {
                if ($search) {
                    if ($v['type'] == $type && stripos($v['name'], $search) !== false || stripos($v['nickname'], $search) !== false)
                    {
                        if($type == "all" || $type == null) {
                            $list = $this->categorylist;
                        } else {
                            $list[] = $v;
                        }
                    }
                } else {
                    if($type == "all" || $type == null) {
                        $list = $this->categorylist;
                    } else if ($v['type'] == $type){
                        $list[] = $v;
                    }

                }
            }
            $list2 = [];
            foreach ($list as $k => $v){
                if ($v['type'] == 'category') {
                    $list2[] = $v;
                }
            }
            $total = count($list2);
            $result = array("total" => $total, "rows" => $list2);

            return json($result);
        }
        return $this->view->fetch();
    }

}
