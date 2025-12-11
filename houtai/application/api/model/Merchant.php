<?php

namespace app\api\model;

use think\Model;

/**
 * Class Merchant 商户
 * @package app\api\model
 */
class Merchant extends Model
{
    // 表名
    protected $name = 'merchant';

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    /**
     * 处理图片
     * @param $value
     * @return string
     */
    public function getMerAvatarAttr($value) {
        return Config::getImagesFullUrl($value);
    }
}
