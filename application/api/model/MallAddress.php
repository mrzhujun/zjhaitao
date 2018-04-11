<?php
/**
 * Author: zhujun
 * DateTime: 2018/4/11 9:27
 */

namespace app\api\model;


use think\Exception;
use think\Model;

class MallAddress extends Model
{
    protected static function init()
    {
        parent::init();
        MallAddress::event('after_insert',function ($address){
            if (!$address->user_id) {
                throw new Exception('请传入user_id');
            }
            if ($address->is_default == 1) {
                try {
                    MallAddress::where("user_id={$address->user_id}")->update(['is_default' => 0]);
                }catch (Exception $e){
                    throw new Exception($e->getMessage());
                }
            }
        });

        MallAddress::event('after_update',function ($address){
            if ($address->is_default == 1) {
                try {
                    MallAddress::where("user_id={$address->user_id} and address_id!={$address->address_id}")->update(['is_default' => 0]);
                }catch (Exception $e){
                    throw new Exception($e->getMessage());
                }
            }
        });


    }
}