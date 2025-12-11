<?php

namespace app\admin\model\order;

use think\Model;


class Order extends Model
{

    

    

    // 表名
    protected $name = 'order';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'status_text',
        'delivery_status_text',
        'pay_time_text',
        'verify_time_text'
    ];

    public function getStatusList()
    {
        return ['-2' => __('Status -2'), '-1' => __('Status -1'), '0' => __('Status 0'), '1' => __('Status 1'),'2' => __('Status 2'), '3' => __('Status 3'), '4' => __('Status 4')];
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function getDeliveryStatusList()
    {
        return ['1' => __('Delivery_status 1'),'2' => __('Delivery_status 2'), '3' => __('Delivery_status 3'), '4' => __('Delivery_status 4')];
    }


    public function getDeliveryStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['delivery_status']) ? $data['delivery_status'] : '');
        $list = $this->getDeliveryStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getPayTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['pay_time']) ? $data['pay_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getVerifyTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['verify_time']) ? $data['verify_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    protected function setPayTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    protected function setVerifyTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }


    public function group()
    {
        return $this->belongsTo('app\admin\model\order\GroupOrder', 'group_order_id', 'group_order_id', [], 'LEFT')->setEagerlyType(0);
    }


    public function user()
    {
        return $this->belongsTo('app\admin\model\User', 'user_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function merchant()
    {
        return $this->belongsTo('app\admin\model\merchant\Merchant', 'mer_id', 'mer_id', [], 'LEFT')->setEagerlyType(0);
    }
}
