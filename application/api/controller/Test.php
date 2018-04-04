<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/3
 * Time: 18:37
 */

namespace app\api\controller;


use app\common\controller\Api;
use think\Exception;

/**
 * 朱俊接口
 */
class Test extends Api
{
    // 无需验证登录的方法
    protected $noNeedLogin = ['*'];
    // 无需要判断权限规则的方法
    protected $noNeedRight = ['*'];

    /**
     * 朱俊的测试方法
     *
     * @ApiTitle （名称）
     * @ApiParams (name="id", type="integer", required=true, description="会员ID")
     * @ApiHeaders (name=token, type=string, required=true, description="请求的Token")
     */
    public function zhujun()
    {
        try {
            return taewrt;
        }catch (Exception $e){
            return json($e->getMessage());
        }
    }

    /**
     * 首页
     *
     * 可以通过@ApiInternal忽略请求的方法
     * @ApiInternal
     */
    public function index()
    {
        return 'index';
    }


    /**
     * 测试方法
     *
     * @ApiTitle    (测试名称)
     * @ApiSummary  (测试描述信息)
     * @ApiSector   (测试分组)
     * @ApiMethod   (POST)
     * @ApiHeaders  (name=token, type=string, required=true, description="请求的Token")
     * @ApiParams   (name="id", type="integer", required=true, description="会员ID")
     * @ApiParams   (name="name", type="string", required=true, description="用户名")
     * @ApiParams   (name="data", type="object", sample="{'user_id':'int','user_name':'string','profile':{'email':'string','age':'integer'}}", description="扩展数据")
     * @ApiReturnParams   (name="code", type="integer", required=true, sample="0")
     * @ApiReturnParams   (name="msg", type="string", required=true, sample="返回成功")
     * @ApiReturnParams   (name="data", type="object", sample="{'user_id':'int','user_name':'string','profile':{'email':'string','age':'integer'}}", description="扩展数据返回")
     * @ApiReturn   (data="{
     *  'code':'0',
     *  'mesg':'返回成功'
     * }")
     */
    public function test($id = '', $name = '')
    {
        echo "id={$id}\n";;
        echo "name={$name}\n";
        $this->success("返回成功", $this->request->request());
    }
}