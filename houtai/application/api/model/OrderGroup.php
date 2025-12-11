<?php

namespace app\api\model;

use think\composer\ThinkFramework;
use think\Model;

class OrderGroup extends Model
{
    // 表名
    protected $name = 'group_order';

    // // 开启自动写入时间戳字段
    // protected $autoWriteTimestamp = 'int';

    // // 定义时间戳字段名
    // protected $createTime = 'createtime';
    // protected $updateTime = 'null';


    public function getOrderSn()
    {
        return 'D'.date('YmdHis').time();
    }

    public function createGroupOrder($data,$userId)
    {
        $order_group = [
            'group_order_sn' => $data['group_order_sn']??$this->getOrderSn(),
            'user_id' => $userId,
            'total_postage' => $data['total_postage'], // 邮费
            'total_price' => $data['total_price'], // 订单总额
            'total_num' => $data['total_num']??0, //  商品数
//            'integral' => $data['score_price'], // 使用积分数
//            'integral_price' => $data['gold_price'], // 积分抵扣金额
//            'give_integral' =>$data['send_score'], // 赠送积分
//            'coupon_price' => $data['coupon_price'], // 优惠金额
//            'real_name' => Address::getValue($data['address_id'],'name'), // 联系人
//            'user_phone' => Address::getValue($data['address_id'],'mobile'), // 联系电话
//            'user_address' => (new Address)->getAddresText($data['address_id']), // 收货地址
            'pay_price' => $data['pay_price'], // 支付金额
            'pay_postage' => $data['pay_postage'], // 支付邮费
            'total_cost' => $data['total_cost'], // 成本价
            'createtime' => time(),
            // 'give_coupon_ids' => '', // 赠送优惠券
            // 'paid' => 0, // 是否支付
            'pay_time' => 0, // 支付时间
            'pay_type' => 0, // 支付方式 0=余额 1=微信 2=小程序 3=h5
            'address_id' => $data['address_id'], // 收货地址
        ];
        //print_r($order_group);die;
        $res = $this->save($order_group);
        if ($res) {
            return $this->group_order_id;
        }
    }
}
