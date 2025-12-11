<?php

namespace app\admin\model\merchant;

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
        'is_ad_text'
    ];
    

    protected static function init()
    {
        self::afterInsert(function ($row) {
            $pk = $row->getPk();
            $row->getQuery()->where($pk, $row[$pk])->update(['weigh' => $row[$pk]]);
        });
    }

    
    public function getIsAdList()
    {
        return ['0' => __('Is_ad 0'), '1' => __('Is_ad 1')];
    }


    public function getIsAdTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_ad']) ? $data['is_ad'] : '');
        $list = $this->getIsAdList();
        return isset($list[$value]) ? $list[$value] : '';
    }




    public function merchant()
    {
        return $this->belongsTo('app\admin\model\merchant\Merchant', 'mer_id', 'mer_id', [], 'LEFT')->setEagerlyType(0);
    }


    public function goods()
    {
        return $this->belongsTo('app\admin\model\product\Product', 'product_id', 'product_id', [], 'LEFT')->setEagerlyType(0);
    }
}
