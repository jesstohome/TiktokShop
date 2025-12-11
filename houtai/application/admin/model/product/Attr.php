<?php

namespace app\admin\model\product;

use think\Model;


class Attr extends Model
{
    // 表名
    protected $name = 'product_attr';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];

    public function product()
    {
        return $this->belongsTo('app\admin\model\product\Product', 'product_id', 'product_id', [], 'LEFT')->setEagerlyType(0);
    }
}
