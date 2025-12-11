<?php

namespace app\admin\model;

use think\Model;


class UserAddress extends Model
{

    

    

    // 表名
    protected $name = 'user_address';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];


    public function province()
    {
        return $this->belongsTo('area', 'province_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    public function city()
    {
        return $this->belongsTo('area', 'city_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    public function area()
    {
        return $this->belongsTo('area', 'area_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

}
