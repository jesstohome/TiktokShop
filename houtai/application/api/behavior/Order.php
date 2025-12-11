<?php

namespace app\api\behavior;

//use app\api\extend\Ali;
use app\admin\model\User;
use app\api\model\Address;
use app\api\model\Config;
use app\api\model\OrderProduct;
use app\api\model\Product;
use app\common\model\User as CommonUser;
//use app\admin\model\Coupon;
use think\Db;
use think\Exception;
use think\Model;
use think\Queue;

/**
 * 订单相关行为
 * Class Order
 * @package app\api\behavior
 */
class Order
{
    /**
     * 创建订单之后
     * 行为一：根据订单减少商品库存 增加"已下单未支付数量"
     * 行为二：如果选了购物车的就删除购物车的信息
     * @param array $params 商品属性
     * @param array $extra [specNumber] => ['spec1' => 'number1','spec2' => 'number2']
     */
    public function createOrderAfter(&$params, $extra)
    {
        // 行为一
        $key = 0;
        $productModel = new \app\api\model\Product;
        $prefix = \think\Config::get('database.prefix');
       // print_r($params);
        foreach ($params as $k => $value) {
            //print_r($params);
            $result = 0;
            $spec = $value['spec'];
            $number = $value['spec_buy_num'];
            if ($value['use_spec'] == 0) {
                $productInfo = $productModel->where(['product_id' => $params[$key]['product_id']])->find();
                //增销量，减库存
                $real_sales = $productInfo['real_sales'] + $number;
                $stock = $productInfo['stock'] - $number;
                $result = $productModel->where(['product_id' => $params[$key]['product_id']])->update(['real_sales'=>$real_sales,'stock'=>$stock]);
                //$result = Db::execute('UPDATE ' . $prefix . "product SET real_sales = real_sales+{$number}, stock = stock-{$number} WHERE product_id = {$params[$key]['product_id']}");

            } else if ($value['use_spec'] == 1) {
                $info = $productModel->getBaseData($params[$key], $spec);
                //print_r($info);
                // mysql<5.7.13时用
                // if (mysql < 5.7.13) {
                //    $spec = str_replace(',', '","', $spec);
                //    $search = '"stock":"' . $info['stock'] . '","value":["' . $spec . '"]';
                //    $stock = $info['stock'] - $number;
                //    $replace = '"stock":\"' . $stock . '\","value":["' . $spec . '"]';
                //    $sql = 'UPDATE ' . $prefix . "product SET no_buy_yet = no_buy_yet+{$number}, stock = stock-{$number}, real_sales = real_sales+{$number} ,`specTableList` = REPLACE(specTableList,'$search','$replace') WHERE id = {$params[$key]['id']}";
                //    $result = Db::execute($sql);
                // }
                //下面语句直接操作JSON
                //if (mysql >= 5.7.13) {
                $info['stock'] -= $number;
                $info['sales'] += $number;
                $productInfo = $productModel->where(['product_id' => $params[$key]['product_id']])->find();
                //增销量，减库存
                $real_sales = $productInfo['real_sales'] + $number;
                $stock = $productInfo['stock'] - $number;

//                $result = $productModel->where(['product_id' => $params[$key]['product_id']])->update(['real_sales'=>$real_sales,'stock'=>$stock]);
                $sql = 'UPDATE ' . $prefix . "product SET real_sales = {$real_sales}, stock = {$stock},specTableList = JSON_REPLACE(specTableList, '$[{$info['key']}].stock', {$info['stock']}),specTableList = JSON_REPLACE(specTableList, '$[{$info['key']}].sales', {$info['sales']}) WHERE product_id = {$params[$key]['product_id']}";
                //echo $sql;die;

                $result = Db::execute($sql);
                //}
            }
            if ($result == 0) { // 锁生效
                throw new Exception(__('下单失败,请重试'));
            }
            $key++;
        }
    }


    /**
     * 检查是否符合创建订单的条件
     * 条件1：商品是否存在
     * 条件2：商品库存情况
     * 条件3：收货地址是否在范围内
     * 条件4：是否使用优惠券，优惠券能否可用
     * @param array $params
     * @param array $extra
     * @throws Exception
     * @throws \think\exception\DbException
     */
    public function createOrderBefore(&$params, $extra)
    {
        $merchant = []; // 商户数据信息
        //print_r($extra);
        $specs = explode(',', $extra['spec']);
        foreach ($specs as &$spec) {
            if($spec){
                $spec = str_replace('|', ',', $spec);
            }
        }
        //print_r($specs);die;
        $numbers = explode(',', $extra['number']);
        $productIds = explode(',', $extra['product_id']);
        $merIds = explode(',', $extra['mer_id']);
        if (count($specs) !== count($numbers) || count($specs) !== count($productIds) || count($productIds) !== count($merIds)) {
            throw new Exception(__('参数错误'));
        }

        // 条件一
        $products = [];
        foreach ($productIds as $key => &$productId) {
            $products[$key] = Db::name('product')
                ->where(['product_id' => $productId, 'switch' => 1])
                ->find();
            if (!$products[$key]) {
                throw new Exception(__('商品不存在或已下架'));
            }
        }
        // dump($products);die;
        if (count($products) == 0 || count($productIds) != count($products)) {
            throw new Exception(__('存在下架商品'));
        }
        // 从购物车下单多个商品时，有同一个商品的不同规格导致减库存问题
        if (count($productIds) > 0) {
            $reduceStock = [];
            foreach ($products as $key => $value) {
                if (!isset($reduceStock[$value['product_id']])) {
                    $reduceStock[$value['product_id']] = $numbers[$key];
                } else {
                    $products[$key]['stock'] -= $reduceStock[$value['product_id']];
                    $reduceStock[$value['product_id']] += $numbers[$key];
                }
            }
        }

        // 订单价格
        $orderPrice = 0;
        //成本价
        $costPrice = 0;
        // 条件二
        foreach ($products as $key => $product) {
            $productInfo = (new \app\api\model\Product())->getBaseData($product, $specs[$key] ? $specs[$key] : '');
            if ($productInfo['stock'] < $numbers[$key]) {
                throw new Exception(__('库存不足，剩余%s件', $productInfo['stock']));
            }
            # 自提订单选择自提价
            // 订单价格
            $order =  bcmul($productInfo['sales_price'], $numbers[$key], 2);
            //利润
            $commission_ratio = config('site.commission_ratio');
            $single_profit = number_format($productInfo['sales_price'] * $commission_ratio, 2);
            $profit = bcmul($single_profit, $numbers[$key], 2);
            //成本价
            $cost = bcmul($productInfo['cost_price'], $numbers[$key], 2);
            $orderPrice = bcadd($orderPrice, $order, 2);
            $costPrice = bcadd($costPrice, $cost, 2);
            //$productInfo['store_id'] = $product['store_id'];

            $baseProductInfo[] = $productInfo;
            //$products[$key]['weight'] = $product['weight'] * $numbers[$key];

            // 添加每单位运费机制  统计运费
//            if(isset($product['unit_freight']) && $product['unit_freight'] > 0){
//                $unit_freight[] = [
//                    'freight' => $product['unit_freight'] * $numbers[$key],
//                ];
//            }
            $product['spec'] = $specs[$key] ? $specs[$key] : '';
            $product['spec_price'] = $productInfo['sales_price'];
            $product['spec_buy_num'] = $numbers[$key];
            $product['spec_profit'] = $single_profit;

            $merchant[$merIds[$key]]['mer_id'] = $merIds[$key];
            $merchant[$merIds[$key]]['goods'][] =  $product;
            $merchant[$merIds[$key]]['orderPrice'][] = $order;
            $merchant[$merIds[$key]]['costPrice'][] = $cost;
            $merchant[$merIds[$key]]['profitPrice'][] = $profit;
            $merchant[$merIds[$key]]['goodsnum'][] = $numbers[$key];
            //$store[$product['mer_id']]['weight'][] = $product['weight'] * $numbers[$key];
        }
        // dump($baseProductInfo);die;
        // 条件三  (暂时不考虑)
        $delivery = [];
        // $delivery = (new DeliveryRuleModel())->cityInScopeOfDelivery($extra['city_id'], $extra['delivery_id']);
        // if (!$delivery) {
        //     throw new Exception(__('Your receiving address is not within the scope of delivery'));
        // } else {
        //     if ($delivery['min'] > array_sum($numbers)) {
        //         throw new Exception(__('You must purchase at least %s item to use this shipping method', $delivery['min']));
        //     }
        // }

        $address = (new Address)->where(['address_id' => $extra['address_id'], 'user_id' => $extra['userId']])->find();
        if (!$address) {
            throw new Exception(__('地址不存在'));
        }
        $address['detail'] = $address['country'].$address['detail'];
        // 条件四 （暂时不考虑）
        if (isset($extra['coupon_id']) && $extra['coupon_id']) {
            $coupon = Coupon::get($extra['coupon_id']);
            if ($coupon['switch'] == Coupon::SWITCH_OFF || $coupon['deletetime'] || $coupon['starttime'] > time() || $coupon['endtime'] < time()) {
                throw new Exception('此优惠券不可用');
            }
            // 至少消费多少钱
            if ($coupon['least'] > $orderPrice) {
                throw new Exception('选中的优惠券不满足使用条件');
            }
        } else {
            $coupon = [];
        }
        $freight = !empty($unit_freight) ? array_sum(array_column($unit_freight, 'freight')) : 0;
        // $weight = array_sum(array_column($products,'weight'));
        // $freight = $this->freight($weight);

        $params = [$products, $delivery, $coupon, $baseProductInfo, $address, $orderPrice, $costPrice, $specs, $numbers,$freight,$merchant];
    }

    /**
     * 运费计算
     * @param $weight
     */
    public function freight($weight)
    {
        $plusFreight = \app\api\model\Config::where('name','plus_freight')->value('value');
        if($weight <= 1){
            $freight = \app\api\model\Config::where('name','freight')->value('value');
        }else{
            $weightInt = intval($weight);
            if($weightInt < $weight){
                $weightInt++;
            }
            $freight = \app\api\model\Config::where('name','freight')->value('value');
            $freight = $freight + ($weightInt-1)*$plusFreight;
        }
        return $freight;
    }

    /**
     * 支付成功
     * 行为一：更改订单的支付状态，更新支付时间。
     * 行为二：减少商品的已下单但未支付的数量
     * 行为三：订单自动收货,自动归档加入队列
     * 行为四：添加业绩数据
     * @param $params
     * @param $extra
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function paidSuccess(&$params, $extra)
    {
        $orderGroup = &$params; // 此处的params为orderGroup的数据

        $orderGroup->pay_time = time();
        $orderGroup->pay_type = $extra['pay_type']; // 0=余额 1=微信 2=小程序 3=h5
        $orderGroup->paid = 1; // 0=未支付 1=已支付

        //$orderGroup->have_paid = time();// 更新支付时间为当前时间
        //$orderGroup->pay_type = $extra['pay_type'];

        //$orderGroup->have_delivered = time();// 更新订单为已发货

        $orderGroup->save();

        // 然后获取 order表中的这个订单组下的所有订单  然后再进行遍历处理
        $orders = \app\api\model\Order::where('group_order_id',$orderGroup['group_order_id'])->select();

        //确认分销人员和分销金额
        $userModel = new \app\api\model\User();
        $userInfo = $userModel->where(['id'=>$orderGroup->user_id])->find();
        $brokerage_func_status = config('site.brokerage_func_status');
        $store_brokerage_ratio = config('site.store_brokerage_ratio') * 0.01;

        foreach ($orders as $order) {
            // 进行处理每个商户订单的情况 TODO
            $order->have_paid = 1;
            $order->pay_type = $extra['pay_type'];
            //确认分销人员和分销金额
            if(!empty($userInfo['r_user_id']) && $brokerage_func_status == 1 && !empty($store_brokerage_ratio)){
                $spread_uid = $userInfo['r_user_id'];
                $one_brokerage = bcmul($store_brokerage_ratio,$order->total_price,2);
                $order->spread_uid = $spread_uid;
                $order->one_brokerage = $one_brokerage;
                //更新分销记录
                $userMoneyLogModel = new UserMoneyLog();
                $userMoneyLogModel->changeMoney($spread_uid,$one_brokerage,'brokerage',$order->id);
            }
            $order->save();

            //订单商品更新 删除购物车
            $orderProductModel = new OrderProduct();
            $orderProducts = $orderProductModel
                ->with('product')
                ->where(['order_id' => $order->id])
                ->select();

            foreach ($orderProducts as $product) {
                if (isset($product->product)) {
                    $product->product->no_buy_yet -= $product->number;
                    $product->product->save();
                }
                //清空购物车
                $cart = \app\api\model\Cart::get(['product_id'=>$product->product_id,'user_id'=>$product->user_id]);
                if($cart){
                    $cart->delete();
                }
            }

            //若是拼团更新拼团状态
            if(!empty($order->combination_id) && !empty($order->pink_id)){
                $pinkModel = new Pink();
                $pinkInfo = $pinkModel->where(['id'=>$order->pink_id])->find();
                if($pinkInfo['k_id'] > 0){
                    $k_id = $pinkInfo['k_id'];
                }else{
                    $k_id = $pinkInfo['id'];
                }
                $pinkT = $pinkModel->where(['id'=>$k_id,'is_refund'=>0])->find(); //团长拼团信息
                $count = $pinkModel->where(['k_id'=>$k_id,'is_refund'=>0])->count();

                //剩余参团人数
                $count2 = $count + 1;
                $overplus = $pinkT['people'] - $count2;
                if($overplus < 1){
                    $pinkModel->where(['id'=>$k_id,'is_refund'=>0])->update(['status'=>2]);//团长拼团完成
                    $pinkModel->where(['k_id'=>$k_id,'is_refund'=>0])->update(['status'=>2]);//团员拼团完成

                    //拼团完成随机选取
                    $orderNoArr = [$pinkT['order_id'],$pinkInfo['order_id']];
                    Queue::later(1, 'app\admin\job\PinkAutoFinish' , $orderNoArr , 'PinkAutoFinish' );
                }
                $pinkModel->where(['id'=>$order->pink_id])->update(['have_paid'=>1]);
            }
        }

        // More ...

        //行为三
       // $rdelay=Config::getByName('ord_received.value');
        Queue::later(1, 'app\admin\job\OrderAutoReceived' , $order , 'OrderAutoReceived' );

        //$fdelay=Config::getByName('ord_finish')*86400;
        Queue::later(1, 'app\admin\job\OrderAutoFinish' , $order , 'OrderAutoFinish' );


        // 成功提交下单返积分 （按要求添加 不考虑特殊情况 如：支付成功->获得积分->使用积分->退单 情况）
        // $score_rates = Config::getByName('score_return_rates');
        // if ($score_rates && $score_rates > 0) {
        //     // 获得订单的金额
        //     $order_price = $order['order_price'];
        //     $score = ceil($order['order_price'] * ($score_rates / 100)); // 进一取整 最少1积分
        //     // 处理积分
        //     $memo = '成交订单' . $order['order_price'];
        //     CommonUser::scoreLog($score, $order['user_id'], 'order', $memo);
        // }

        //$performanceModel = new Performance
//        if (Config::getByName('sto_switch')){
//            $rebate=Config::getByName('sto_rebate');
//            db('user')->where(['id'=>$order['store_id']])->setInc('money',$order['money']*$rebate);
//            $moneylog = array(
//                'user_id' => $order['store_id'],
//                'type'=>'order',
//                'order_id'=>$order['out_trade_no'],
//                'money' => '+'.$order['money']*$rebate,
//                'before' => $order['money'],
//                'after' => $order['money']+$order['money']*$rebate,
//                'memo' => '订单分润',
//                'createtime' => time()
//            );
//            db('user_money_log')->insertGetId($moneylog);
//        }
    }

    public function paidSuccess2(&$params, $extra)
    {
        $order = &$params;
        $order->pay_type = $extra['pay_type'];

        //$order->have_delivered = time();// 更新订单为已发货
        $order->save();

        $orderProductModel = new OrderProduct();
        $orderProducts = $orderProductModel
            ->with('product')
            ->where(['order_id' => $order->id])
            ->select();

        foreach ($orderProducts as $product) {
            if (isset($product->product)) {
                $product->product->no_buy_yet -= $product->number;
                $product->product->save();
            }
            //清空购物车
            $cart = \app\api\model\Cart::get(['product_id'=>$product->product_id,'user_id'=>$product->user_id]);
            if($cart){
                $cart->delete();
            }

        }

    }

    /**
     * 支付失败
     * @param $params
     */
    public function paidFail(&$params)
    {
        $order = &$params;
        $order->have_paid = \app\api\model\Order::PAID_NO;
        $order->save();

        // More ...
    }

    /**
     * 订单退款
     * 行为一：退款
     * @param array $params 订单数据
     */
    public function orderRefund(&$params)
    {
        $order = &$params;
        // 行为一
        switch ($order['pay_type']) {
            case \app\api\model\Order::PAY_WXPAY:
                if($params['have_delivered'] == 0){
                    $amount = $params['total_price'];
                }else{
                    $amount = bcsub($params['total_price'],$params['delivery_price'],2);
                }
                $app = Wechat::initEasyWechat('payment');
                $result = $app->refund->byOutTradeNumber($params['out_trade_no'], $params['out_trade_no'], bcmul($params['total_price'], 100), bcmul($params['refund']['amount'], 100), [
                    // 可在此处传入其他参数，详细参数见微信支付文档
                    'refund_desc' => $params['refund']['reason_type'],
                ]);
                break;
            case \app\api\model\Order::PAY_ALIPAY:
                $alipay = Ali::initAliPay();
                $order = [
                    'out_trade_no' => $params['out_trade_no'],
                    'refund_amount' => $params['total_price'],
                ];
                $result = $alipay->refund($order);
                break;
            case \app\api\model\Order::PAY_DEPOSIT:
                $user = \app\common\model\User::get($order->user_id);
                $user->deposit += $order->deposit;
                $result = $user->save();
                break;
        }

        // More ...
    }
}
