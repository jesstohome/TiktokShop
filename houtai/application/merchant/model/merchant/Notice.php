<?php

namespace app\merchant\model\merchant;

use think\Model;


class Notice extends Model
{

    // 表名
    protected $name = 'merchant_notice';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;

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
    public function getSeeTimeAttr($value)
    {
        return $value ? date('Y-m-d H:i:s', $value) : $value;
    }
}
