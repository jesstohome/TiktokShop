<?php

namespace app\admin\model\product;

use think\Model;


class Reply extends Model
{
    // 表名
    protected $name = 'product_reply';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'merchant_reply_time_text'
    ];
    

    protected static function init()
    {
        self::afterInsert(function ($row) {
            $pk = $row->getPk();
            $row->getQuery()->where($pk, $row[$pk])->update(['weigh' => $row[$pk]]);
        });
    }


    public function getMerchantReplyTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['merchant_reply_time']) ? $data['merchant_reply_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    protected function setMerchantReplyTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }


    public function user()
    {
        return $this->belongsTo('app\admin\model\User', 'user_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function merchant()
    {
        return $this->belongsTo('app\admin\model\merchant\Merchant', 'mer_id', 'mer_id', [], 'LEFT')->setEagerlyType(0);
    }


    public function order()
    {
        return $this->belongsTo('app\admin\model\order\OrderProduct', 'order_product_id', 'order_product_id', [], 'LEFT')->setEagerlyType(0);
    }


    public function product()
    {
        return $this->belongsTo('app\admin\model\product\Product', 'product_id', 'product_id', [], 'LEFT')->setEagerlyType(0);
    }
}
