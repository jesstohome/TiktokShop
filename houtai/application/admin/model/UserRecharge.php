<?php

namespace app\admin\model;

use think\Model;


class UserRecharge extends Model
{
    // 表名
    protected $name = 'user_recharge';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'pay_time_text'
    ];

    public function getTypeList()
    {
        return ['0' => __('Recharge_type 0'), '1' => __('Recharge_type 1')];
    }

    public function getPayTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['pay_time']) ? $data['pay_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    protected function setPayTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }


    public function user()
    {
        return $this->belongsTo('User', 'user_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    /**
     * Notes: 生成订单编号
     * Author: licj
     * DateTime: 2021/3/19 0019 16:18
     * function getOrderTradeNo
     * @package app\api\model
     */
    public static function getOrderNo($prefix='ur'){
        $prefix = $prefix ? $prefix : '';
        $order_id_main = date('YmdHis') . rand(1000,9999);
        return $prefix . $order_id_main;
    }
}
