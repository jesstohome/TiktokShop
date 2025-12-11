<?php
/**
 * Created by PhpStorm.
 * User: licj
 * Date: 2021/3/19 0019 $TIME
 * Time: 10:00 下午
 */


namespace app\api\controller;

use app\admin\model\merchant\Product as MerProductModel;
use app\api\model\Area;
use app\api\model\OrderProduct;
use app\api\model\OrderRefund;
use app\api\model\OrderRefundProduct;
use app\api\model\Product;
use app\api\model\Product as ProductModel;
use app\common\controller\Api;
use think\Db;
use think\Exception;
use app\api\model\Address as AddressModel;
use app\api\model\Cart;
use think\Hook;
use think\Loader;
use think\Queue;

/**
 * 订单相关接口
 * Class Order
 * @package app\api\controller
 */
class Order extends Api
{

    /**
     * 允许频繁访问的接口
     * @var array
     */
    protected $frequently = ['getorders'];

    protected $noNeedRight = []; // 订单的所有操作 都需要登录 发布时打开
    protected $noNeedLogin = ['*'];

    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 多商户下 创建订单页面数据
     *
     * @return json
     */
    public function merCreate()
    {
        // model 实例化
        $productModel =  new Product;
        // 参数获取
        $productCode = input('product_id', 0); //商品code - 立即购买
        $cartId = input('cart_id', 0); //  购物车id - 购物车购买
        $user_id = $this->auth->id;
        $freight = 0; // 运费
        $product_data = [];

        // 逻辑实现
        try {
            if ($productCode) {
                $product_data = $productModel->getProductInfo($productCode);
            } elseif ($cartId) {
                $product_data = $productModel->getProductByCart($cartId);
            }
            if (empty($product_data)) {
                $this->error(__('请求错误，商品数据不存在'));
            }

            // 获取商户信息
            $merInfo = $this->getMerInfo($product_data);

            // 计算运费 TODO
            $freight = $this->getFreight($product_data);  //暂时默认0
            // TOdo 其他计算  如优惠券  地址 。。。。。
            $address = $this->getAddress($user_id);

            $this->success('', [
                'product' => $product_data,
                'address' => $address,
                'delivery' => $freight, // 运费
                'merchant' => $merInfo, // 商户信息
            ]);
        } catch (Exception $e) {
            $this->error($e->getMessage(), false);
        }
    }


    /**
     * 多商户下 提交订单
     *
     * @return json
     */
    public function merSubmit()
    {
        error_reporting(0);
        $cart_ids = input('cart_ids');
        if ($cart_ids) {
            // 判断是购物车提交过来 提取购物车商品信息
            $data = (new Cart())->getCartGoods($cart_ids);
            $data['address_id'] = input('address_id');
        }else{
            //直接购买
            $data = $this->request->post();
        }
        $user_id = $this->auth->id;
        //print_r($data);die;
        // 判断创建订单的条件
        if (empty(Hook::get('create_order_before'))) {
            Hook::add('create_order_before', 'app\\api\\behavior\\Order');
        }
        // 减少商品库存，增加"已下单未支付数量"
        if (empty(Hook::get('create_order_after'))) {
            Hook::add('create_order_after', 'app\\api\\behavior\\Order');
        }

        Db::startTrans();
        try {
            //预先获取订单号
            $orderModel = new \app\api\model\Order();
            $data['orderCode'] = $orderModel->getOrderTradeNo(); // 预先获取订单号
            // $result = $orderModel->createOrderNew($user_id, $data,0); // 老版本
            $result = $orderModel->submitOrder($user_id, $data);

            // 如果是购物车提交 则去除购物车数据 TODO
            // $where['id'] = array('in',$cart_id);
            // Db::name('cart')->where($where)->delete();
            if ($cart_ids) {
                (new Cart())->whereIn('cart_id', $cart_ids)->delete();
            }

            Db::commit();

        } catch (\Exception $e) {
            Db::rollback();
            $this->error($e->getMessage(), false);
        }

        $this->success(__('购买成功'), $result);
    }

    /**
     * 多商户下 支付扣款
     *
     * @return json
     */
    public function merPay()
    {
        $group_order_sn = $this->request->post('out_trade_no');  //订单组单号
        $total_price = $this->request->post('total_price');  //支付金额

        $groupModel = new \app\api\model\OrderGroup;
        $group = $groupModel->where(['group_order_sn' => $group_order_sn])->find();
        if (!$group) {
            $this->error(__('订单不存在'));
        }

        //余额是否充足
        if ($this->auth->money < $total_price) {
            $this->error(__('余额不足，请先充值'));
        }

        //扣余额
        $user_id = $this->auth->id;
        $userModel = new \app\common\model\User;
        $userModel->money($total_price,$user_id,$group_order_sn,0,'order',__('购买商品'));


        $orderModel = new \app\api\model\Order;
        $orderList = $orderModel->where(['group_order_id' => $group->group_order_id])->select();


        Db::startTrans();
        try {
            // 修改订单组状态已支付
            $group->paid = 1;
            $group->pay_time = time();
            $group->pay_type = 0;
            $group->save();

            // 子订单修改订单状态已支付
            foreach ($orderList as $order){
                $order->paid = 1;
                $order->pay_time = time();
                $order->pay_type = 0;
                $order->status = 1;
                $order->save();
            }

            Db::commit();

        } catch (\Exception $e) {
            Db::rollback();
            $this->error($e->getMessage(), false);
        }

        $this->success(__('支付成功'));
    }

    /**
     * 创建订单
     */
    public function create()
    {
        $productId = $this->request->post('id', 0);
        try {
            $user_id = $this->auth->id;
            $freight = 0; // 运费
            $send_score = 0;
            // 单个商品
            if ($productId) {
                $productId = Hashids::decodeHex($productId);
                $product = (new Product)->where(['id' => $productId, 'switch' => Product::SWITCH_ON, 'deletetime' => null])->find();
                /** 产品基础数据 **/
                $spec = $this->request->post('spec', '');
                $number = $this->request->post('number');
                file_put_contents('order_log.txt',json_encode($this->request->post()).PHP_EOL,FILE_APPEND);
                $productData[0] = $product->getDataOnCreateOrder($spec,$number);
                $weight = $product->weight * $number;
                if ($product->unit_freight) { // 单件运费
                    $freight  = round($number * $product->unit_freight,2);
                }
                file_put_contents('order_log.txt',json_encode($productData).PHP_EOL,FILE_APPEND);
                if (isset($productData[0]['score']) && $productData[0]['score'] > 0) {
                    $send_score = $productData[0]['score'] * $number;
                }else {
                    $send_score = $product->send_score * $number;
                }
            } else {
                // 多个商品
                $cart = $this->request->post('cart');
                $carts = (new \app\api\model\Cart)
                    ->whereIn('id', $cart)
                    ->with(['product'])
                    ->order(['id' => 'desc'])
                    ->select();
                $productId = [];
                // dump(collection($carts)->toArray());die;
                foreach ($carts as $k=>$cart) {
                    if ($cart->product instanceof Product) {
                        $productData[] = $cart->product->getDataOnCreateOrder($cart->spec ? $cart->spec : '', $cart->number);
                    }
                    $productId[] = $cart->product_id;
                    $productData[$k]['weight'] = $cart->product->weight * $cart->number;
                    file_put_contents('order_log.txt',json_encode($productData).PHP_EOL,FILE_APPEND);
                    if ($cart->product->unit_freight) { // 单件运费
                        $freights[]  = round($cart->number * $cart->product->unit_freight,2);
                    }

                    if (isset($productData[$k]['score']) && $productData[$k]['score'] > 0) {
                        $arr_score[] = $productData[$k]['score'] * $cart->number;
                    }else {
                        $arr_score[] = $cart->product->send_score * $cart->number;
                    }
                    file_put_contents('order_log.txt',json_encode($arr_score).PHP_EOL,FILE_APPEND);
                }
                $send_score = array_sum($arr_score);
                $weight = array_sum(array_column($productData,'weight'));
                $freight = array_sum($freights);
            }

            // $freight = $this->freight($weight);
            
            if (empty($productData) || !$productData) {
                $this->error(__('Product not exist'));
            }
            /** 默认地址 **/
            $address = (new AddressModel)->where(['user_id' => $user_id, 'is_default' => AddressModel::IS_DEFAULT_YES])->find();
            if ($address) {
                $area = (new Area)->whereIn('id', [$address->province_id, $address->city_id, $address->area_id])->column('name', 'id');
                $address = $address->toArray();
                $address['province']['name'] = $area[$address['province_id']];
                $address['city']['name'] = $area[$address['city_id']];
                $address['area']['name'] = $area[$address['area_id']];
            }

            /** 可用优惠券 **/
            // $coupon = CouponModel::all(function ($query) {
            //     $time = time();
            //     $query
            //         ->where(['switch' => CouponModel::SWITCH_ON])
            //         ->where('starttime', '<', $time)
            //         ->where('endtime', '>', $time);
            // });
            // if ($coupon) {
            //     $coupon = collection($coupon)->toArray();
            // }


            /** 运费数据 **/
            // $cityId = $address['city_id'] ? $address['city_id'] : 0;
            // $delivery = (new DeliveryRuleModel())->getDelivetyByArea($cityId);

            $order_money = 0; // 总商品价格 即为订单价格
            foreach ($productData as &$product) {
                $product['image'] = Config::getImagesFullUrl($product['image']);
                $product['sales_price'] = round($product['sales_price'], 2);
                $product['zt_price'] = round($product['sales_price'], 2);
                $product['market_price'] = round($product['sales_price'], 2);
                $order_money += $product['sales_price'];
            }
            unset($product);            
            // by zcc 2021年8月20日  获得金币数据 然后进行结算抵扣  如果金币折扣大于订单总金额 则为订单金额
            // 金币 转换 为优惠金额 转化率 TODO 待配置 先100金币为 1元
            $user = $this->auth->getUser();
            $gold = $user['gold'];
            if ($gold > $order_money * $this->gold_ratio) {
                $gold = $order_money * $this->gold_ratio;
            }
            $max_gold = 200; // 是否开启 订单最大使用金币数  如：最大使用金币200需要配置 
            // 金币 是否大于订单金币 
            if ($gold > $max_gold) {
                $gold = $max_gold;
            }

            // 获取商品所要赠送的积分和

            $this->success('', [
                'product' => $productData,
                'address' => $address,
                //'coupon' => $coupon,
                'gold' => $gold, // 抵扣的金币数
                'gold_money' => round($gold / $this->gold_ratio, 2), // 抵扣金币数转成金额
                'send_score' => $send_score,
                'delivery' => $freight // 运费
            ]);

        } catch (Exception $e) {
            $this->error($e->getMessage(), false);
        }
    }

    /**
     * 提交订单
     */
    public function submit()
    {
        $cart_id = $this->request->post('card_id');
        $data = $this->request->post();
        //dump($cart_id);die;
        // 防止 多次购物车进行提交订单 
        if ($cart_id) {
            $is = Db::name('cart')->where('id',$cart_id)->select();
            if (!$is) {
                $this->error('购物车不存在');
            }
        }
        try {
            $validate = Loader::validate('\\app\\api\\validate\\Order');
            if (!$validate->check($data, [], 'submit')) {
                throw new Exception($validate->getError());
            }
            $data['store_id'] = 27;
            $data['deposit'] = $this->auth->deposit;
            
            Db::startTrans();
            // 判断创建订单的条件
            if (empty(Hook::get('create_order_before'))) {
                Hook::add('create_order_before', 'app\\api\\behavior\\Order');
            }
            // 减少商品库存，增加"已下单未支付数量"
            if (empty(Hook::get('create_order_after'))) {
                Hook::add('create_order_after', 'app\\api\\behavior\\Order');
            }

            //预先获取订单号
            $orderModel = new \app\api\model\Order();
            $orderCode = $data['orderCode'] = $orderModel->getOrderTradeNo(); // 预先获取订单号

            // 判断是否使用金币抵扣 使用则先扣除金币  100为todo 配置
            $this->gold_ratio = Config::getByName('price_gold_ratio')['value'];
            if (isset($data['gold_money'])) {
                $gold = intval($data['gold_money'] * $this->gold_ratio) * -1;
                // \app\common\model\User::goldLog($gold,$this->auth->id,'pay','');
                $log_id = UserGoldLog::changeGold($this->auth->id,$gold,$orderCode);
                if (!$log_id) {
                    $this->error('账户金币不足以扣除');
                }
            }
            $data['gold_log_id'] = $log_id;
            $result = $orderModel->createOrderNew($this->auth->id, $data,0);

            Db::commit();
            
            //dump($cart_id);die;
            // 清除购物车
            $where['id'] = array('in',$cart_id);
            Db::name('cart')->where($where)->delete();
            
        } catch (Exception $e) {
            Db::rollback();
            $this->error($e->getMessage(), false);
        }
        
        $this->success('', $result);
    }

    /**
     * 获取运费模板
     */
    public function getDelivery()
    {
        $cityId = $this->request->get('city_id', 0);
        $delivery = (new DeliveryRuleModel())->getDelivetyByArea($cityId);
        $this->success('', $delivery['list']);
    }

    /**
     * 获取订单信息
     */
    public function getOrders()
    {
        // 0=全部,1=待付款,2=待发货,3=待收货,4=待评价,5=售后
        $type = $this->request->get('type', 0);
        $page = $this->request->get('page', 1);
        $pagesize = $this->request->get('pagesize', 10);
        try {

            $orderModel = new \app\api\model\Order();
            //$storeInfo = Store::getByuser_id($this->auth->id);
            $storeInfo = Store::where(["user_id"=>$this->auth->id,"examine_status"=>2])->find();

            if ($storeInfo && $storeInfo->id && $storeInfo->status == 'normal') {
                $result = $orderModel->getStoreOrdersByType($storeInfo->id, $type, $page, $pagesize);
            } else {
                $result = $orderModel->getOrdersByType($this->auth->id, $type, $page, $pagesize);
            }
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
        $this->success('', $result);

    }

    /**
     * 取消订单
     * 未支付的订单才叫取消，已支付的叫退货
     */
    public function cancel()
    {
        $order_id = $this->request->param('order_id', 0);
        $orderModel = new \app\api\model\Order();
        $order = $orderModel->where(['order_id' => $order_id, 'user_id' => $this->auth->id])->find();
        
        if (!$order) {
            $this->error(__('订单不存在'));
        }

        switch ($order['status']) {
            case -2:
                $this->error(__('订单已退款，无法取消'));
                break;
            case -1:
                $this->error(__('订单已取消, 无需再取消'));
                break;
        }

        if ($order['paid'] == 1) {
            $this->error(__('订单已支付，无法取消'));
        }

        if ($order['status'] == 0 && $order['paid'] == 0) {
            $order->status = -1;
            if($order->save()){
                $user_id = $order['user_id'];
                // 库存回库
                $orderModel->returnKucun($order_id);

            }
            $this->success(__('取消成功'), true);
        }
    }
    
    /**
     * 取消退货
     */
    public function cancelReturn()
    {
        $order_id = $this->request->post('order_id', 0);

        $orderModel = new \app\api\model\Order();
        $order = $orderModel->where(['id' => $order_id, 'user_id' => $this->auth->id])->find();

        if (!$order) {
            $this->error(__('Order not exist'));
        }

        if($order['status'] == -2){
            $this->error('此订单已退款，无法取消');
        }

        if ($order['status'] == \app\api\model\Order::REFUND_STATUS_APPLY) {
            $order->status = \app\api\model\Order::REFUND_STATUS_NONE;
            $order->save();
            $this->success('取消成功', true);
        }
    }
    

    /**
     * 删除订单
     * 只能删除已完成,已取消或已退款的订单
     */
    public function delete()
    {
        $order_id = $this->request->post('order_id', 0);

        $orderModel = new \app\api\model\Order();
        $order = $orderModel->where(['order_id' => $order_id, 'user_id' => $this->auth->id])->find();

        if (!$order) {
            $this->error(__('订单不存在'));
        }

        if (in_array($order['status'],[1,2,3])) {
            $this->error(__('只能删除未付款,已取消,已退款或已完成的订单'));
        }

        // if ($order['status'] == \app\api\model\Order::STATUS_NORMAL) {
        //     $this->error('只能删除已取消或已退货的订单');
        // }

        if ($order['refund_status'] == 1) {
            $this->error(__('订单申请退款中，不可删除'));
        }

        $order->delete();
        $this->success(__('删除成功'), true);
    }

    /**
     * 确认收货
     */
    public function received()
    {
        $order_id = $this->request->param('order_id', 0);
        $orderModel = new \app\api\model\Order();
        $order = $orderModel->where(['order_id' => $order_id, 'user_id' => $this->auth->id])->find();
        if (!$order) {
            $this->error(__('订单不存在'));
        }

        if ($order->status != 2) {
            $this->error(__('未发货，不能确认收货'));
        }

        $order->status = 3;
        $order->received_time = time();
        $order->verify_time = time();
        $order->save();

        //用户确认收货，商户增加余额(订单总金额)
        $merchantModel = new \app\admin\model\merchant\Merchant;
        $merchantModel->money($order->total_price, $order->mer_id, $order->order_sn, 1, 'order', __("订单完成增加余额").$order->total_price);

        $mer = $merchantModel->where(['mer_id' => $order->mer_id])->find();
        if($mer['spread_id']){
            //存在上级，上级获取佣金
            $merchantModel->money($order->extension_one, $mer['spread_id'], $order->order_sn, 0, 'order', __("下级商户订单完成增加佣金") . $order->extension_one);
        }

        //订单自动归档加入队列
        $delay = config('site.order_finish')*86400;
        Queue::later($delay, 'app\admin\job\OrderAutoFinish' , $order , 'OrderAutoFinish');

        $this->success(__('已确认收货'), true);
    }

    /**
     * 发表评论
     */
    public function comment()
    {
        $product_score = $this->request->post('product_score', 5);
        $service_score = $this->request->post('service_score', 5);
        $postage_score = $this->request->post('postage_score', 5);
        //$rate = $this->request->post('rate', 5);
        $comment = $this->request->post('comment');
        $pics = $this->request->post('pics');
        $order_id = $this->request->post('order_id', 0);
        $product_id = $this->request->post('product_id');

        $orderProductModel = new \app\api\model\OrderProduct();
        $orderProduct = $orderProductModel->where(['product_id' => $product_id, 'order_id' => $order_id, 'user_id' => $this->auth->id])->find();

        $orderModel = new \app\api\model\Order();
        $order = $orderModel->where(['order_id' => $order_id, 'user_id' => $this->auth->id])->find();

        if (!$orderProduct || !$order) {
            $this->error(__('订单不存在'));
        }
        if ($order->status != 3) {
            $this->error(__('未收货，不可评价'));
        }

        $total_score = $product_score + $service_score + $postage_score;
        $rate = bcdiv($total_score,3,1);

        $result = false;
        try {
            $reply = new \app\admin\model\product\Reply;
            $reply->user_id = $this->auth->id;
            $reply->mer_id = $order['mer_id'];
            $reply->order_id = $order_id;
            $reply->product_id = $product_id;
            $reply->product_score = $product_score;
            $reply->service_score = $service_score;
            $reply->postage_score = $postage_score;
            $reply->rate = $rate;
            $reply->comment = $comment;
            $reply->pics = $pics;
            $reply->spec = $orderProduct->spec;
            $reply->avatar = $this->auth->avatar;
            $reply->nickname = $this->auth->nickname;
            $result = $reply->save();

            if ($result) {
                $orderProduct->is_reply = 1;
                $orderProduct->save();

                // 商品全部评论完更新订单评价状态
                $unreply = $orderProductModel->where(['product_id' => $product_id, 'order_id' => $order_id, 'user_id' => $this->auth->id,'is_reply'=>0])->count();
                if($unreply == 0){
                    $order->commented_time = time();
                    $order->save();
                }
            }

        } catch (Exception $e) {
            $this->error($e->getMessage());
        }

        if ($result !== false) {
            $this->success(__('谢谢你的评价'));
        } else {
            $this->error(__('评价失败'));
        }

    }

    /**
     * 获取订单数量
     */
    public function count()
    {
        if (!$this->auth->isLogin()) {
            $this->error('');
        }
        $order = new \app\api\model\Order();

        $list = $order
            ->where([
                'user_id' => $this->auth->id,
            ])
            ->where('status', '<>', \app\api\model\Order::STATUS_CANCEL)
            ->where(function ($query) {
                $query
                    ->whereOr([
                        'have_paid' => \app\api\model\Order::PAID_NO,
                        'have_delivered' => \app\api\model\Order::DELIVERED_NO,
                        'have_received' => \app\api\model\Order::RECEIVED_NO,
                        'have_commented' => \app\api\model\Order::COMMENTED_NO
                    ])
                    ->whereOr('refund_status', '>', \app\api\model\Order::REFUND_STATUS_NONE);
            })
            ->field('have_paid,have_delivered,have_received,have_commented,refund_status,had_refund')
            ->select();

        $data = [
            'unpaid' => 0,
            'undelivered' => 0,
            'unreceived' => 0,
            'uncomment' => 0,
            'refund' => 0
        ];
        foreach ($list as $item) {
            switch (true) {
                case $item['have_paid'] > 0 && $item['have_delivered'] > 0 && $item['have_received'] > 0 && $item['have_commented'] == 0 && $item['refund_status'] == 0:
                    $data['uncomment']++;
                    break;
                case $item['have_paid'] > 0 && $item['have_delivered'] > 0 && $item['have_received'] == 0 && $item['have_commented'] == 0 && $item['refund_status'] == 0:
                    $data['unreceived']++;
                    break;
                case $item['have_paid'] > 0 && $item['have_delivered'] == 0 && $item['have_received'] == 0 && $item['have_commented'] == 0 && $item['refund_status'] == 0:
                    $data['undelivered']++;
                    break;
                case $item['have_paid'] == 0 && $item['have_delivered'] == 0 && $item['have_received'] == 0 && $item['have_commented'] == 0 && $item['refund_status'] == 0:
                    $data['unpaid']++;
                    break;
                case $item['refund_status'] > 0 && $item['had_refund'] == 0 && $item['refund_status'] != 3:
                    $data['refund']++;
                    break;

            }
        }

        $this->success('', $data);
    }

    /**
     * 订单详情
     */
    public function detail()
    {
        $order_id = $this->request->get('order_id', 0);
        try {
            $orderModel = new \app\api\model\Order();
            $order = $orderModel->where(['order_id' => $order_id, 'user_id' => $this->auth->id])->find();
            $order = $orderModel->field('order_id,order_sn,real_name,user_phone,user_address,total_num,total_price,total_cost,delivery_id,status,refund_status,createtime,pay_time,delivery_time,received_time,commented_time,finish_time,address_id,mer_id')->where(['order_id'=>$order_id])->find();
            if (!$order) {
                $this->error(__('订单不存在'));
            }
            if ($order) {
                //订单商品
                $orderProductModel = new \app\api\model\OrderProduct;
                $productModel = new \app\api\model\Product;
                $orderProduct = $orderProductModel->field('order_product_id,order_id,product_id,spec,product_num,cost,product_price,total_price,is_reply')->where(['order_id' => $order_id])->select();
                foreach ($orderProduct as &$v) {
                    $goods = $productModel->field('title,title_en,image')->where(['product_id'=>$v['product_id']])->find();
                    $v['title'] = $goods['title'];
                    $v['title_en'] = $goods['title_en'];
                    $v['image'] = $goods['image'];
                }
                $order['products'] = $orderProduct;
                //物流信息
                $orderDeliveryModel = new \app\merchant\model\order\Delivery;
                $orderDelivery = $orderDeliveryModel->where(['delivery_no'=>$order['delivery_id'],'status'=>['in',[2,3]]])->order('step DESC')->select();

                $order['delivery'] = $orderDelivery;

                //推荐商品
                $productList = MerProductModel::field('id,product_id,mer_id,sales,click')->where(['mer_id'=>$order['mer_id'],'switch'=>1])->limit(10)->select();
                foreach ($productList as $key => &$value){
                    $product = ProductModel::field('product_id,code,title,title_en,sales_price,cost_price,market_price')->where(['product_id'=>$value['product_id']])->find();
                    $value['goods'] = $product;
                }
                $order['product_list'] = $productList;

            }

        } catch (Exception $e) {
            $this->error($e->getMessage());
        }

        $this->success(__('请求成功'), $order);
    }

    /**
     * 申请售后信息
     */
    public function refundInfo()
    {
        $order_id = $this->request->post('order_id');
        $order_id = Hashids::decodeHex($order_id);

        $orderModel = new \app\api\model\Order();
        $order = $orderModel
            ->with([
                'products' => function ($query) {
                    $query->field('id,order_id,image,number,price,spec,title,product_id,(1) as choose');
                },
                'refund',
                'refundProducts'
            ])
            ->field('id,status,total_price,delivery_price,have_commented,have_delivered,have_paid,have_received,refund_status')
            ->where(['id' => $order_id, 'user_id' => $this->auth->id])->find();

        if (!$order) {
            $this->error(__('Order not exist'));
        }

        $order = $order->append(['refund_status_text'])->toArray();

        foreach ($order['products'] as &$product) {
            $product['image'] = Config::getImagesFullUrl($product['image']);
            $product['choose'] = 0;

            // 如果是已提交退货的全选
            if ($order['status'] == \app\api\model\Order::STATUS_REFUND) {
                foreach ($order['refund_products'] as $refundProduct) {
                    if ($product['order_product_id'] == $refundProduct['order_product_id']) {
                        $product['choose'] = 1;
                    }
                }
            }
        }

        unset($order['refund_products']);

        $this->success('', $order);
    }


    /**
     * 申请售后
     *
     */
    public function refund()
    {
        $order_id = $this->request->post('order_id');
        $user_id = $this->auth->id;
        $orderModel = new \app\api\model\Order();
        //dump($id);die;
        $order = $orderModel->where(['order_id' => $order_id, 'user_id' => $user_id])->find();
        if (!$order) {
            $this->error(__('订单不存在'));
        }
        if ($order['status'] == 0) {
            $this->error(__('订单未支付，可直接取消，无需申请售后'));
        }
        if ($order['status'] == -2) {
            $this->error(__('订单已退款'));
        }
        if ($order['refund_status'] > 0) {
            $this->error(__('订单已申请售后，无法重复申请'));
        }

        $amount = $this->request->post('amount', 0);
        $serviceType = $this->request->post('service_type');
        $receivingStatus = $this->request->post('receiving_status');
        $reasonType = $this->request->post('reason_type');
        $refundExplain = $this->request->post('refund_explain');
        $images = $this->request->post('images');
        $orderProductId = $this->request->post('order_product_id');

        // if (!$orderProductId) {
        //     $this->error(__('Please select goods'));
        // }
        if (!in_array($receivingStatus, [OrderRefund::UNRECEIVED, OrderRefund::RECEIVED])) {
            $this->error(__('请选择货物接收状态'));
        }
        if (!in_array($serviceType, [OrderRefund::TYPE_REFUND_NORETURN, OrderRefund::TYPE_REFUND_RETURN, OrderRefund::TYPE_EXCHANGE])) {
            $this->error(__('请选择售后服务类型'));
        }

        Db::startTrans();
        try {
            $orderRefund = new OrderRefund();
            $orderRefund->user_id = $user_id;
            $orderRefund->order_id = $order_id;
            $orderRefund->refund_sn = $order->order_sn;
            $orderRefund->mer_id = $order->mer_id;
            $orderRefund->receiving_status = $receivingStatus;
            $orderRefund->service_type = $serviceType;
            $orderRefund->reason_type = $reasonType;
            $orderRefund->amount = $amount;
            $orderRefund->refund_explain = $refundExplain;
            $orderRefund->images = $images;
            $orderRefund->save();

            $productIdArr = explode(',', $orderProductId);
            $refundProduct = [];
            foreach ($productIdArr as $orderProductId) {
                $tmp['order_product_id'] = $orderProductId;
                $tmp['order_id'] = $order_id;
                $tmp['user_id'] = $user_id;
                $tmp['refund_id'] = $orderRefund['refund_id'];
                $tmp['createtime'] = time();
                $refundProduct[] = $tmp;
            }
            (new OrderRefundProduct)->insertAll($refundProduct);

            // $order->status = -2;
            $order->refund_status = 1;
            $order->save();

            (new OrderProduct)->where(['order_product_id'=>['in',$orderProductId]])->update(['is_refund'=>1]); //订单商品状态 改为申请中

            Db::commit();
        } catch (Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        $this->success('退款申请成功，请等待审核');
    }

    /**
     * 售后发货
     */
    public function refundDelivery()
    {
        $orderId = $this->request->post('order_id');
        $expressNumber = $this->request->post('express_number');

        if (!$expressNumber) {
            $this->error(__('Please fill in the express number'));
        }

        $orderId = Hashids::decodeHex($orderId);
        $orderModel = new \app\api\model\Order();
        $order = $orderModel
            ->where(['id' => $orderId, 'user_id' => $this->auth->id])
            ->with(['refund'])->find();

        if (!$order || !$order->refund) {
            $this->error(__('Order not exist'));
        }
        try {
            Db::startTrans();

            $order->refund->express_number = $expressNumber;

            $order->refund_status = \app\api\model\Order::REFUND_STATUS_APPLY;

            if ($order->refund->save() && $order->save()) {
                Db::commit();
            } else {
                throw new Exception(__('Operation failed'));
            }

        } catch (Exception $e) {
            Db::rollback();
            $this->success($e->getMessage());
        }
        $this->success('', 1);
    }

    /**
     * Notes:测试用生成字符串
     * Author: licj
     * DateTime: 2021/3/19 0019 16:44
     * function testOrderSn
     * @package app\api\controller
     */
    public function testOrderSn(){
        //测试百度api解析地理编码 begin
        $apiUrl=Config::getByName('bdapi_ak')['value'];
        $address='北京市海淀区上地十街10号';
        $result=$apiUrl.$address;
        $bb=\fast\Http::post($result);
        $aa=json_decode($bb,true);
        //测试百度api解析地理编码 end
        $one=uniqid();
        $two=substr($one, 7, 4);
        $three=str_split($two, 1);
        $four = array_map('ord', $three);
        $five=implode(NULL,$four );
        $result =  substr($three, 0, 4);
        return $result;
    }
    
    /**
     * 获得各状态下的订单数量
     *
     * @return void
     */
    public function getOrdersNums()
    {
        $orderModel = new \app\api\model\Order();
        $user_id = $this->auth->id;
        // 全部
        $condition['user_id'] = ['=', $user_id];
        $condition['status'] = ['=', $orderModel::STATUS_NORMAL];
        $data['all'] = $orderModel::where($condition)->count();

        // 未付款
        $condition1['user_id'] = ['=', $user_id];
        $condition1['have_paid'] = ['=', $orderModel::PAID_NO];
        $condition1['status'] = ['=', $orderModel::STATUS_NORMAL];
        $data['nopay'] = $orderModel::where($condition1)->count();
        // 代发货
        $condition2['user_id'] = ['=', $user_id];
        $condition2['have_paid'] = ['>', $orderModel::PAID_NO];
        $condition2['have_delivered'] = ['=', $orderModel::DELIVERED_NO];
        $condition2['status'] = ['=', $orderModel::STATUS_NORMAL];
        $data['nofahuo'] = $orderModel::where($condition2)->count();
        // 待收货
        $condition3['user_id'] = ['=', $user_id];
        $condition3['have_paid'] = ['>', $orderModel::PAID_NO];
        $condition3['have_delivered'] = ['>', $orderModel::DELIVERED_NO];
        $condition3['have_received'] = ['=', $orderModel::RECEIVED_NO];
        $condition3['status'] = ['=', $orderModel::STATUS_NORMAL];
        $data['shouhuo'] = $orderModel::where($condition3)->count();
        // 已收货
        $condition4['user_id'] = ['=', $user_id];
        $condition4['have_paid'] = ['>', $orderModel::PAID_NO];
        $condition4['have_delivered'] = ['>', $orderModel::DELIVERED_NO];
        $condition4['have_received'] = ['>', $orderModel::RECEIVED_NO];
        $condition4['have_commented'] = ['=', $orderModel::COMMENTED_NO];
        $condition4['status'] = ['=', $orderModel::STATUS_NORMAL];
        $data['wancheng'] = $orderModel::where($condition4)->count();

        $this->success('',$data);
    }

    /**
     * 获取商品对应的运费
     *
     * @param [] $data
     * @return integer
     */
    protected function getFreight($data)
    {
        $value = 0;
        // code todo
        return $value;
    }

    /**
     * 获取商品对应的附赠项
     *
     * @param [] $data
     * @return integer
     */
    protected function getSendScore($data)
    {
        $value = 0;
        // code todo
        return $value;
    }

    /**
     * 获取用户的默认地址
     *
     * @param integer $user_id
     * @return array
     */
    protected function getAddress(int $user_id)
    {
        // 封到model层 实现通用 最好 TODO
        $address = (new AddressModel)
            ->where(['user_id' => $user_id, 'is_default' => AddressModel::IS_DEFAULT_YES])
            ->find();

        if ($address) {
            // $area = (new Area)->whereIn('id', [$address->province_id, $address->city_id, $address->area_id])->column('name', 'id');
            // $address = $address->toArray();
            // $address['province'] = $area[$address['province_id']];
            // $address['city'] = $area[$address['city_id']];
            // $address['area'] = $area[$address['area_id']];
        }

        return $address;
    }

    /**
     * 获取商品的商户信息
     *
     * @param [type] $data
     * @return array
     */
    public function getMerInfo($data)
    {
        $mer = [];
        // dump($data);die;
        foreach ($data as $key => $va) {
            $mer[] = [
                'mer_id' => $va['mer_id'],
                'product_id' => $va['product_id']
            ];
        }
        return $mer;
    }
}
