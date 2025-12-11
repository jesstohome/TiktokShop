<?php

namespace app\admin\model;

use think\Model;


class UserExtract extends Model
{

    

    

    // 表名
    protected $name = 'user_extract';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'extract_type_text',
        'status_text'
    ];
    

    
    public function getExtractTypeList()
    {
        return ['0' => __('Extract_type 0'), '1' => __('Extract_type 1')];
    }

    public function getStatusList()
    {
        return ['-1' => __('Status -1'), '0' => __('Status 0'), '1' => __('Status 1')];
    }


    public function getExtractTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['extract_type']) ? $data['extract_type'] : '');
        $list = $this->getExtractTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }




    public function user()
    {
        return $this->belongsTo('User', 'user_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function admin()
    {
        return $this->belongsTo('Admin', 'admin_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    /**
     * Notes: 生成订单编号
     * Author: licj
     * DateTime: 2021/3/19 0019 16:18
     * function getOrderTradeNo
     * @package app\api\model
     */
    public static function getOrderNo($prefix='ue'){
        $prefix = $prefix ? $prefix : '';
        $order_id_main = date('YmdHis') . rand(1000,9999);
        return $prefix . $order_id_main;
    }


}
