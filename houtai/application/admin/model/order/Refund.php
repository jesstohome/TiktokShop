<?php

namespace app\admin\model\order;

use think\Model;


class Refund extends Model
{

    

    

    // 表名
    protected $name = 'order_refund';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'receiving_status_text',
        'service_type_text',
        'status_text',
        'refunded_time_text'
    ];
    

    
    public function getReceivingStatusList()
    {
        return ['0' => __('Receiving_status 0'), '1' => __('Receiving_status 1')];
    }

    public function getServiceTypeList()
    {
        return ['0' => __('Service_type 0'), '1' => __('Service_type 1'), '2' => __('Service_type 2')];
    }

    public function getStatusList()
    {
        return ['-1' => __('Status -1'), '0' => __('Status 0'), '1' => __('Status 1')];
    }


    public function getReceivingStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['receiving_status']) ? $data['receiving_status'] : '');
        $list = $this->getReceivingStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getServiceTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['service_type']) ? $data['service_type'] : '');
        $list = $this->getServiceTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getRefundedTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['refunded_time']) ? $data['refunded_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    protected function setRefundedTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }


    public function user()
    {
        return $this->belongsTo('app\admin\model\User', 'user_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function order()
    {
        return $this->belongsTo('app\admin\model\Order', 'order_id', 'order_id', [], 'LEFT')->setEagerlyType(0);
    }
}
