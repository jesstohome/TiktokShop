<?php

namespace app\admin\model\merchant;

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
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'type_text',
        'is_see_text',
        'see_time_text'
    ];
    

    
    public function getTypeList()
    {
        return ['1' => __('Type 1'), '2' => __('Type 2')];
    }

    public function getIsSeeList()
    {
        return ['0' => __('Is_see 0'), '1' => __('Is_see 1')];
    }


    public function getTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['type']) ? $data['type'] : '');
        $list = $this->getTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsSeeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_see']) ? $data['is_see'] : '');
        $list = $this->getIsSeeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getSeeTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['see_time']) ? $data['see_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    protected function setSeeTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }


    public function merchant()
    {
        return $this->belongsTo('app\admin\model\Merchant', 'mer_id', 'mer_id', [], 'LEFT')->setEagerlyType(0);
    }
}
