<?php

namespace app\merchant\model\merchant;

use think\Model;


class Extract extends Model
{

    // 表名
    protected $name = 'merchant_extract';
    
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
    public static function getOrderNo($prefix='tx'){
        $prefix = $prefix ? $prefix : '';
        $order_id_main = date('YmdHis') . rand(1000,9999);
        return $prefix . $order_id_main;
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
}
