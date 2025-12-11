<?php

namespace app\admin\model\merchant;

use think\Model;


class Admin extends Model
{

    // 表名
    protected $name = 'merchant_admin';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'status_text'
    ];



    public function getStatusList()
    {
        return ['1' => __('Status 1'), '0' => __('Status 0')];
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }




    public function merchant()
    {
        return $this->belongsTo('app\admin\model\merchant\Merchant', 'mer_id', 'mer_id', [], 'LEFT')->setEagerlyType(0);
    }

    //审核通过生成商户后台账号
    public function addAccount($data)
    {
        $insert = [
            'mer_id' => $data['mer_id'],
            'mer_name' => $data['mer_name'],
            'real_name' => $data['name'],
            'phone' => $data['phone'],
            'account' => $data['phone'],
            'pwd' => md5(123456),
            'status' => 1,
        ];
        $admin = self::where(['account'=>$data['phone']])->find();
        if($admin){
            return self::where(['account'=>$data['phone']])->update($insert);
        }else{
            return self::create($insert);
        }
    }
}
