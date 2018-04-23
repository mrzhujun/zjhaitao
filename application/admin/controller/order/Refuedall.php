<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/18
 * Time: 12:01
 */

namespace app\admin\controller\order;


use app\common\controller\Backend;

class Refuedall extends Backend
{

    public function index()
    {
        return $this->fetch('index');
    }
}