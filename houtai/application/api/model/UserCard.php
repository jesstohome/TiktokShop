<?php

namespace app\api\model;

use think\Model;


class UserCard extends Model
{

    // 表名
    protected $name = 'user_card';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;

    // 追加属性
    protected $append = [

    ];



}
