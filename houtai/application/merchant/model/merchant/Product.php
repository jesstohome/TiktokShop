<?php

namespace app\merchant\model\merchant;

use think\Model;


class Product extends Model
{
    // 表名
    protected $name = 'merchant_product';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];

    protected static function init()
    {
        self::afterInsert(function ($row) {
            $pk = $row->getPk();
            $row->getQuery()->where($pk, $row[$pk])->update(['weigh' => $row[$pk]]);
        });
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
    public function getUpdatetimeAttr($value)
    {
        return date('Y-m-d H:i:s', $value);
    }
}
