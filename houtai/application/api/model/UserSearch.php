<?php

namespace app\api\model;


use think\Model;

class UserSearch extends Model
{
    // 表名
    protected $name = 'user_search';

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;

}
