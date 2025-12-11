<?php

namespace app\merchant\controller;

use app\common\controller\Mer;
use app\merchant\model\order\Order as OrderModel;
use app\merchant\model\order\Product as OrderProductModel;
use app\merchant\model\order\Delivery as OrderDeliveryModel;
use app\admin\model\product\Product as ProductModel;

/**
 * 订单接口
 */
class Order extends Mer
{
    protected $noNeedLogin = [];
    protected $noNeedRight = ['*'];

    /**
     * 订单列表
     *
     * @ApiMethod (GET)
     * @param string $search 搜索
     * @param int $status 状态
     * @param int $page 页码
     * @param int $limit 每页数量
     */
    public function list()
    {
        $search = $this->request->get('search', '');
        $status = $this->request->get('status', 'all');
        $page = $this->request->get('page', 1);
        $limit = $this->request->get('limit', 10);

        $orderModel = new OrderModel;

        $where = ['o.mer_id' => $this->auth->mer_id];
        if ($status != 'all') {
            if($status == 5){
                $where['o.status'] = 1;
                $where['o.is_pick'] = 0;
            }else{
                $where['o.status'] = $status;
            }
        }

        if($search){
            // $where['p.title'] = ['like', '%' . $search . '%'];
            $where['o.order_sn'] = $search;
        }

        $count = $orderModel->alias('o')
            // ->join('order_product op', 'o.order_id = op.order_id', 'LEFT')
            // ->join('product p', 'op.product_id = p.product_id', 'LEFT')
            ->where($where)->count();
        $list = $orderModel->alias('o')
            // ->join('order_product op', 'o.order_id = op.order_id', 'LEFT')
            // ->join('product p', 'op.product_id = p.product_id', 'LEFT')
            ->field('o.order_id,o.order_sn,o.total_num,o.total_price,o.total_cost,o.total_profit,o.status,o.paid,o.delivery_status,o.is_pick,o.is_virtual,o.createtime')
            ->where($where)
            ->page($page, $limit)
            ->order('o.order_id DESC')
            ->select();
        foreach ($list as $row) {
            $orderProduct = OrderProductModel::where('order_id', $row['order_id'])->field('product_id,product_num,cost,product_price,profit,total_price')->select();
            foreach ($orderProduct as $p){
                $p['goods'] = ProductModel::field('product_id,title,title_en,image')->where(['product_id'=>$p['product_id']])->find();
            }
            $row['orderProduct'] = $orderProduct;
        }
        $total_price = 0;
        $total_profit = 0;

        $total_price = OrderModel::where(['mer_id' => $this->auth->mer_id,'status'=>1,'is_pick'=>1])->sum('total_price');
        // $total_cost = OrderModel::where(['mer_id' => $this->auth->mer_id,'status'=>1,'is_pick'=>1])->sum('total_cost');
        $total_profit = OrderModel::where(['mer_id' => $this->auth->mer_id,'status'=>1,'is_pick'=>1])->sum('total_profit');


        $total_price = bcadd($total_price, 0,2);
        $total_profit = bcadd($total_profit, 0,2);
        // $total_profit = bcsub($total_price, $total_cost,2);
        // $commission_ratio = config('site.commission_ratio');
        // $total_profit = bcmul($total_price, $commission_ratio,2);

        $data = [
            'total_price' => $total_price,
            'total_profit' => $total_profit,
            'total' => $count,
            'list' => $list
        ];
        $this->success(__('请求成功'),  $data);
    }

    /**
     * 订单详情
     *
     * @ApiMethod (GET)
     * @param int $id 订单ID
     */
    public function detail()
    {
        $order_id = $this->request->get('id', 0);
        if (!$order_id) {
            $this->error(__('参数错误'));
        }

        $order = OrderModel::field('order_id,order_sn,real_name,user_phone,user_address,total_num,total_price,total_cost,total_profit,delivery_id,createtime')->where(['order_id'=>$order_id])->find();
        if (!$order) {
            $this->error(__('订单不存在'));
        }
        $order['user_phone'] = mb_substr($order['user_phone'],0,3).'****'.mb_substr($order['user_phone'],strlen($order['user_phone'])-3,3);
        //订单商品
        $orderProduct = OrderProductModel::field('order_product_id,order_id,product_id,product_num,cost,product_price,profit,total_price,createtime')->where(['order_id'=>$order_id])->select();
        foreach ($orderProduct as $row)
        {
            $goods = ProductModel::field('title,title_en,image')->where(['product_id'=>$row['product_id']])->find();
            $row['title'] = $goods['title'];
            $row['title_en'] = $goods['title_en'];
            $row['image'] = $goods['image'];
        }
        $order['product'] = $orderProduct;

        //物流信息
        $orderDelivery = OrderDeliveryModel::where(['delivery_no'=>$order['delivery_id'],'status'=>['in',[2,3]]])->order('step DESC')->select();
        $order['delivery'] = $orderDelivery;

        $this->success(__('请求成功'), $order);
    }

    /**
     * 商家提货
     *
     * @ApiMethod (POST)
     * @param int $order_id 订单ID
     */
    public function pick(){
        $order_id = $this->request->post('order_id', 0);
        if (!$order_id) {
            $this->error(__('参数错误'));
        }

        $order = OrderModel::where(['order_id'=>$order_id])->find();
        if (!$order) {
            $this->error(__('订单不存在'));
        }

        if($order['status'] == 0 || $order['paid'] == 0){
            $this->error(__('未支付订单不可提货'));
        }

        if($order['is_pick'] == 1 ){
            $this->error(__('订单已提货'));
        }

        //余额是否足够提货
        if($order->total_cost > $this->auth->mer_money){
            $this->error(__('余额不足，请先充值'));
        }

        if($order['status'] == 1 && $order['paid'] == 1 && $order['is_pick'] == 0){
            $order->is_pick = 1;
            $order->pick_time = time();
            $order->save();

            // if($order->is_virtual == 0) { //虚拟订单不扣除成本
                //扣除余额(订单成本金额)
                $merchantModel = new \app\merchant\model\merchant\Merchant;
                $merchantModel->money($order->total_cost, $order->mer_id, $order->order_sn, 0, 'order', __("订单提货支付 ") . $order->total_cost);
            // }
        }

        $this->success(__('提货成功'));
    }

    /**
     * 商家提货
     *
     * @ApiMethod (POST)
     */
    public function pick_batch(){
        $ids = file_get_contents('php://input');
        $ids_arr = json_decode($ids,true);
        $order_ids = $ids_arr['ids'];
        if(!$order_ids){
            $this->error(__('未选中订单'));
        }

        $where = [
            'order_id'=>['in', $order_ids]
        ];

        $orderList = OrderModel::where($where)->select();
        foreach ($orderList as $row) {
            if (!$row) {
                $this->error(__('订单不存在'));
            }

            if ($row['status'] == 0 || $row['paid'] == 0) {
                $this->error(__('未支付订单不可提货'));
            }

            if ($row['is_pick'] == 1) {
                $this->error(__('订单已提货'));
            }

            //余额是否足够提货
            if ($row->total_cost > $this->auth->mer_money) {
                $this->error(__('余额不足，请先充值'));
            }

            if ($row['status'] == 1 && $row['paid'] == 1 && $row['is_pick'] == 0) {
                $row->is_pick = 1;
                $row->pick_time = time();
                $row->save();

                // if($order->is_virtual == 0) { //虚拟订单不扣除成本
                //扣除余额(订单成本金额)
                $merchantModel = new \app\merchant\model\merchant\Merchant;
                $merchantModel->money($row->total_cost, $row->mer_id, $row->order_sn, 0, 'order', __("订单提货支付") . $row->total_cost);
                // }
            }
        }
        $this->success(__('提货成功'));
    }
}
