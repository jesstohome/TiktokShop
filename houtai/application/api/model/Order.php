<?php

namespace app\api\model;

use think\Hook;
use think\Model;

/**
 * Class Order 订单
 * @package app\api\model
 */
class Order extends Model
{
    // 表名
    protected $name = 'order';

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;


    /**
     * Notes: 生成订单编号
     * Author: licj
     * DateTime: 2021/3/19 0019 16:18
     * function getOrderTradeNo
     * @package app\api\model
     */
    public function getOrderTradeNo(){
        $order_id_main = date('Ymd') . rand(10000000,99999999);
        $order_id_len = strlen($order_id_main);
        $order_id_sum = 0;
        for($i=0; $i<$order_id_len; $i++){

            $order_id_sum += (int)(substr($order_id_main,$i,1));
        }
        return $order_id_main . str_pad((100 - $order_id_sum % 100) % 100,2,'0',STR_PAD_LEFT).substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 4);
    }

    /**
     * 提交订单数据
     *
     * @param int $userId
     * @param array $data
     * @return array
     */
    public function submitOrder($userId, $data)
    {
        $totalPrice = 0; // 实付金额
        $data['userId'] = $userId;
        Hook::listen('create_order_before', $params, $data);
        list($products, $delivery, $coupon, $baseProductInfos, $address, $orderPrice, $costPrice, $specs, $numbers,$freight,$merchant) = $params;
        // 拿到上面钩子返回参数（人家写的 未动） 进行数据处理 组成自己想要的数据结构数据 进行传参执行

        // 订单费用
        //$orderPrice;

        // 运费
        $deliveryPrice = $freight ?? 0;
        // 客户需求： 对运费进行处理  自提不需要运费

        // 实际支付 = 订单金额 + 运费;
        $totalPrice = bcadd($orderPrice ,$deliveryPrice,2);

        // 创建一个主订单
        $orderGroupModel = new OrderGroup;
        $group_order_sn = $orderGroupModel->getOrderSn();
        $group_order = [
            'group_order_sn' => $group_order_sn,
            'total_num' => array_sum($numbers),
            'total_cost' => $costPrice,
            'total_price' => $orderPrice,
            'total_postage' => $deliveryPrice, // 运费
            'pay_price' => $totalPrice, // 需支付金额
            'pay_postage' => $deliveryPrice, // 需支付金额
            'address_id' => $data['address_id']
        ];
        $orderGroupId =  $orderGroupModel->createGroupOrder($group_order,$userId);

        // 创建商户订单
        foreach ($merchant as  $v) {
            $this->createMerOrder($v['mer_id'],$userId,$orderGroupId,$address,$v);
        }

        return [
            'order_id' => $orderGroupId,
            'out_trade_no' => $group_order_sn,
            'total_price'  =>  $totalPrice,
        ];
    }

    /**
     * 生成商户订单
     *
     * @param int $mer_id
     * @param [] $data
     * @return void
     */
    public function createMerOrder($mer_id, $userId, $groupOrderId,$address,$data)
    {
        //$orderPrice = array_sum($data['orderPrice']);
        $totalPrice = array_sum($data['orderPrice']);
        $costPrice = array_sum($data['costPrice']);
        $profitPrice = array_sum($data['profitPrice']);
        $goodsnum = array_sum($data['goodsnum']);

        $commission_ratio = config('site.commission_ratio');
        $brokerage = config('site.brokerage');
        //商户利润
        // $total_profit = bcsub($totalPrice, $costPrice, 2);
        // $total_profit = bcmul($totalPrice,$commission_ratio,2);
        $total_cost = bcsub($totalPrice,$profitPrice,2);
        // 一级佣金
        $extension_one_all = bcmul($profitPrice, $brokerage, 2);

        $out_trade_no = $this->getOrderTradeNo();
        $mer_order = [
            'user_id' => $userId, // 用户id
            'mer_id' => $mer_id, // 商户id
            'group_order_id' => $groupOrderId, // 总订单id
            'order_sn' => $out_trade_no, // 商户订单号
            'total_postage' => 0.00, // 运费
            'total_num' => $goodsnum, // 商品总数
            'total_price' => $totalPrice, //  订单总金额
            'total_cost' => $total_cost, //  订单总成本(原字段$costPrice)
            'total_profit' => $profitPrice, //  订单总利润
            'pay_price' => $totalPrice, //  实际支付金额
            'extension_one' => $extension_one_all, //  一级佣金
            'real_name' => $address['name'], // 收货人
            'user_phone' => $address['mobile'], // 收货人电话
            'user_address' => $address['detail'], // 详细地址
            'address_id' => $address['address_id'], // 地址id
            'remark' => $data['remark'] ?? '',
            'status' => 0,
            'createtime' => time(),
        ];
        //print_r($mer_order);die;
        // 创建订单数据表
        $model = new self();
        $res = $model->save($mer_order);
        $orderProduct = $specNumber = [];
        $products = $data['goods'] ?? [];
        foreach ($products as $key => $product) {
            //商户利润
            //$profit = number_format($product['spec_price'] * $commission_ratio, 2);
            $cost = bcsub($product['spec_price'] ,$product['spec_profit'],2);
            // 一级佣金
            $extension_one = bcmul($product['spec_profit'], $brokerage, 2);
            $orderProduct[] = [
                'user_id' => $userId,
                'order_id' => $model->order_id,
                'product_id' => $product['product_id'],
                'extension_one' => $extension_one, //单商品
                'product_num' => $product['spec_buy_num'],
                'spec' => $product['spec'] ?? '',
                'cost' => $cost, //原字段$product['cost_price']
                'product_price' => $product['spec_price'],
                'profit' => $product['spec_profit'],
                'total_price' => $product['spec_price'] * $product['spec_buy_num'],
                'createtime' => time(),
            ];
        }
        // 创建 订单产品信息表
        (new OrderProduct)->insertAll($orderProduct);

        Hook::listen('create_order_after', $products, $data); //处理库存
    }

    /**
     * 订单商品的库存 回库
     *
     * @param int $order_id
     * @param bool $is_return 是否退款  false  true
     * @return void
     */
    public function returnKucun($order_id,$is_return = false)
    {
        // 先获取订单中所有的商品信息 订单商品数量
        $order = self::get($order_id);
        if (!$order) {
            return false;
        }
        // 订单产品信息表
        $products = OrderProduct::where(['order_id' => $order_id])->select();

        foreach ($products as $key => $v) {
            $pro = Product::find($v['product_id']);
            if (!$pro) {
                // TODO 不存在数据 则记录数据 后台处理
                continue;
            }
            if ($v['spec'] && $pro['use_spec'] == 1 && !empty($pro['specTableList'])) {
                $specTableList = json_decode($pro['specTableList'], true);
                foreach ($specTableList as $k => &$specItem) {
                    // 组成字符串 看是否和 订单产品规格一致
                    if (implode(',', $specItem['value']) == $v['spec']) {
                        // 增加规格库存值
                        $specItem['stock'] = strval($specItem['stock'] + $v['product_num']); //  转为 字符串 方便json全部由引号控制
                        $specItem['sales'] = strval($specItem['sales'] - $v['product_num']); //  转为 字符串 方便json全部由引号控制
                        if ($is_return) {
                            // $specItem['sales'] =  $specItem['sales'] > 0 ? $specItem['sales'] - $v['number'] : 0;// 虚拟销售量 不减少
                        }
                    }
                }
                // 转成json 再存入数据中
                $pro['specTableList'] = json_encode($specTableList,JSON_UNESCAPED_UNICODE);
            }
            // 增加总库存值  如果没有使用规格也直接增加总库存值
            $pro['stock'] += $v['product_num'];
            $pro['real_sales'] = $pro['real_sales'] > $v['product_num'] ? $pro['real_sales'] - $v['product_num'] : 0;
            if ($is_return) {
                // $pro['sales'] =  $pro['sales'] > 0 ? $pro['sales'] - $v['number'] : 0;// 虚拟销售量 不减少
                // $pro['real_sales'] = $pro['real_sales'] > 0 ? $pro['real_sales'] - $v['number'] : 0;
            }
            $pro->save();
        }
        return true;
    }

    /**
     * 关联订单的商品
     */
    public function products()
    {
        return $this->hasMany('orderProduct', 'order_id', 'id');
    }

    /**
     * 格式化时间
     * @param $value
     * @return false|string
     */
    public function getCreatetimeAttr($value)
    {
        return date('Y-m-d H:i:s', $value);
    }

    /**
     * 格式化时间
     * @param $value
     * @return false|string
     */
    public function getPayTimeAttr($value)
    {
        return $value ? date('Y-m-d H:i:s', $value) : $value;
    }

    /**
     * 格式化时间
     * @param $value
     * @return false|string
     */
    public function getVerifyTimeAttr($value)
    {
        return $value ? date('Y-m-d H:i:s', $value) : $value;
    }

    /**
     * 格式化时间
     * @param $value
     * @return false|string
     */
    public function getDeliveryTimeAttr($value)
    {
        return $value ? date('Y-m-d H:i:s', $value) : $value;
    }

    public function getReceivedTimeAttr($value)
    {
        return $value ? date('Y-m-d H:i:s', $value) : $value;
    }

    public function getCommentedTimeAttr($value)
    {
        return $value ? date('Y-m-d H:i:s', $value) : $value;
    }

    public function getFinishTimeAttr($value)
    {
        return $value ? date('Y-m-d H:i:s', $value) : $value;
    }
}
