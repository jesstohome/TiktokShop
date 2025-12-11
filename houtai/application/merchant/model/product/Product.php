<?php

namespace app\merchant\model\product;

use app\api\model\Config;
use think\Model;
use traits\model\SoftDelete;

class Product extends Model
{

    use SoftDelete;

    

    // 表名
    protected $name = 'product';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

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
     * 处理图片
     * @param $value
     * @return string
     */
    public function getImageAttr($value) {
        return Config::getImagesFullUrl($value);
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
