<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/18
 * Time: 11:49
 */
namespace app\admin\controller\order;

use app\common\controller\Backend;

class Simple extends  Backend
{
    public function index()
    {
        return $this->fetch('index');
    }
}