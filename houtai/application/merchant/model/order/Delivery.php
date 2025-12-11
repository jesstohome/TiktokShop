<?php

namespace app\merchant\model\order;

use think\Model;


class Delivery extends Model
{

    // 表名
    protected $name = 'order_delivery';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];

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
    public function getUpdatetimeAttr($value)
    {
        return date('Y-m-d H:i:s', $value);
    }
}
