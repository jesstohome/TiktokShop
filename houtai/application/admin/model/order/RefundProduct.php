<?php

namespace app\admin\model\order;

use think\Model;


class RefundProduct extends Model
{
    // 表名
    protected $name = 'order_refund_product';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];
}
