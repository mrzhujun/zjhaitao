<?php
/**
 * Author: zhujun
 * DateTime: 2018/4/11 9:27
 */

namespace app\api\model;


use app\lib\exception\MissException;
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
                MallAddress::where("user_id={$address->user_id}")->update(['is_default' => 0]);
            }
        });

        MallAddress::event('after_update',function ($address){
            if ($address->is_default == 1) {
                MallAddress::where("user_id={$address->user_id} and address_id!={$address->address_id}")->update(['is_default' => 0]);
            }
        });


    }

    public static function getDefaultAddress($user_id)
    {
        $addressObj = self::where('user_id','=',$user_id)
            ->where('is_default','=',1)
            ->field('address_id,name,phone,address,address_detail')
            ->find();
        if (!$addressObj) {
            throw new MissException([
                'msg' => '该用户没有默认地址'
            ]);
        }
        return $addressObj;
    }
}