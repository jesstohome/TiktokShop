<?php

namespace app\admin\model\merchant;

use think\Model;


class Train extends Model
{

    

    

    // 表名
    protected $name = 'through_train';
    
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
