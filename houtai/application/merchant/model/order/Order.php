<?php

namespace app\merchant\model\order;

use think\Model;


class Order extends Model
{

    // 表名
    protected $name = 'order';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];

    /**
     * Notes: 生成订单编号
     * Author: licj
     * DateTime: 2021/3/19 0019 16:18
     * function getOrderTradeNo
     * @package app\api\model
     */
    public function getOrderNo($prefix=''){
        $prefix = $prefix ? $prefix : '';
        $order_id_main = date('Ymd') . rand(10000000,99999999);
        $order_id_len = strlen($order_id_main);
        $order_id_sum = 0;
        for($i=0; $i<$order_id_len; $i++){

            $order_id_sum += (int)(substr($order_id_main,$i,1));
        }
        return $order_id_main . str_pad((100 - $order_id_sum % 100) % 100,2,'0',STR_PAD_LEFT).substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 4);
    }

    /**
     * 格式化时间
     * @param $value
     * @return false|string
     */
    public function getCreatetimeAttr($value)
    {
        return date('Y-m-d H:i:s', $value);
    }

    /**
     * 格式化时间
     * @param $value
     * @return false|string
     */
    public function getPayTimeAttr($value)
    {
        return $value ? date('Y-m-d H:i:s', $value) : $value;
    }

    public function getPickTimeAttr($value)
    {
        return $value ? date('Y-m-d H:i:s', $value) : $value;
    }
    public function getVerifyTimeAttr($value)
    {
        return $value ? date('Y-m-d H:i:s', $value) : $value;
    }

    public function getDeliveryTimeAttr($value)
    {
        return $value ? date('Y-m-d H:i:s', $value) : $value;
    }

    public function getReceivedTimeAttr($value)
    {
        return $value ? date('Y-m-d H:i:s', $value) : $value;
    }

    public function getCommentedTimeAttr($value)
    {
        return $value ? date('Y-m-d H:i:s', $value) : $value;
    }

    public function getFinishTimeAttr($value)
    {
        return $value ? date('Y-m-d H:i:s', $value) : $value;
    }
}
