<?php

namespace app\admin\model\merchant;

use think\Model;


class Recharge extends Model
{

    

    

    // 表名
    protected $name = 'merchant_recharge';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'recharge_type_text',
        'pay_time_text'
    ];
    

    
    public function getRechargeTypeList()
    {
        return ['0' => __('Recharge_type 0'), '1' => __('Recharge_type 1')];
    }


    public function getRechargeTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['recharge_type']) ? $data['recharge_type'] : '');
        $list = $this->getRechargeTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getPayTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['pay_time']) ? $data['pay_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    protected function setPayTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }


    public function merchant()
    {
        return $this->belongsTo('app\admin\model\merchant\Merchant', 'mer_id', 'mer_id', [], 'LEFT')->setEagerlyType(0);
    }
}
