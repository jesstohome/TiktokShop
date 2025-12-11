<?php

namespace app\admin\controller\order;

use app\common\controller\Backend;

/**
 * 退货信息
 *
 * @icon fa fa-circle-o
 */
class Refund extends Backend
{

    /**
     * Refund模型对象
     * @var \app\admin\model\order\Refund
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\order\Refund;
        $this->orderProductModel = new \app\admin\model\order\OrderProduct;
        $this->productModel = new \app\admin\model\product\Product;

        $this->view->assign("receivingStatusList", $this->model->getReceivingStatusList());
        $this->view->assign("serviceTypeList", $this->model->getServiceTypeList());
        $this->view->assign("statusList", $this->model->getStatusList());
    }



    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */


    /**
     * 查看
     */
    public function index()
    {
        //当前是否为关联查询
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $list = $this->model
                    ->with(['user','order'])
                    ->where($where)
                    ->order($sort, $order)
                    ->paginate($limit);

            foreach ($list as $row) {
                
                $row->getRelation('user')->visible(['id','username','nickname']);
				$row->getRelation('order')->visible(['order_id','order_sn']);
            }

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }

    public function verify($ids)
    {
        if ($this->request->isPost()) {
            $params = $this->request->post('row/a');
            $row = $this->model->get(['refund_id' => $ids]);
            $row->status = $params['status'];
            $row->admin_msg = $params['admin_msg'];
            $row->admin_id = $this->auth->id;
            $row->refunded_time = time();
            $row->save();


            $orderModel = new \app\admin\model\order\Order;
            $order = $orderModel->where(['order_id' => $row['order_id']])->find();

            $refundProductModel = new \app\admin\model\order\RefundProduct;
            $orderProductModel = new \app\admin\model\order\OrderProduct;
            $refundProductList = $refundProductModel->where(['refund_id' => $row->refund_id])->select();

            $total_profit = 0;  //总利润
            $total_cost_all = 0; //总成本价
            $total_extension_one = 0; //总佣金
            $order_product_ids = [];
            foreach ($refundProductList as $key => $value) {
                $orderProduct = $orderProductModel->where(['order_product_id' => $value['order_product_id']])->find();
                $order_product_ids[] = $value['order_product_id'];

                $total_cost = bcmul($orderProduct['cost'], $orderProduct['product_num'], 2);
                $total_cost_all = bcadd($total_cost_all, $total_cost, 2);
                //总利润
                $profit = bcmul($orderProduct['profit'], $orderProduct['product_num'], 2);
                $total_profit = bcadd($total_profit, $profit, 2);

                $extension_one = bcmul($orderProduct['extension_one'], $orderProduct['product_num'], 2);
                $total_extension_one = bcadd($total_extension_one, $extension_one, 2);
            }

            if ($params['status'] == 1) {
                //通过 用户返回余额
                $userModel = new \app\common\model\User;
                $userModel->money($row['amount'], $row['user_id'], $row['refund_sn'], 1, 'order', "User refund application is approved,return balance " . $row['amount']);

                //根据订单状态判断回款/扣款方式
                $merchantModel = new \app\admin\model\merchant\Merchant;
                if ($order['status'] == 3) {
                    //用户已收货，扣除商家收取的利润
                    $merchantModel->money($total_profit, $row->mer_id, $row->refund_sn, 0, 'order', "用户退款扣除利润" . $total_profit);

                    $mer = $merchantModel->where(['mer_id' => $row->mer_id])->find();
                    if($mer['spread_id']){
                        //存在上级，扣除上级佣金
                        $merchantModel->money($total_extension_one, $mer['spread_id'], $row->refund_sn, 0, 'order', "用户退款扣除佣金" . $total_extension_one);
                    }

                } elseif ($order['status'] < 3 && $order['is_pick'] == 1) {
                    //商户已提货未收款，返还成本价
                    $merchantModel->money($total_cost_all, $row->mer_id, $row->refund_sn, 0, 'order', "商户退款返还提货定金" . $total_cost_all);
                }

                //同意订单状态
                $orderProductModel->where(['order_product_id'=>['in',$order_product_ids]])->update(['is_refund'=>2]); //订单商品状态 改为已退款

                // if($orderProductModel->where(['order_product_id'=>['in',$order_product_ids],'is_refund'=>0])->count() == 0){
                //     $order->status = -2;  //全部退款
                // }else{
                //     $order->status = -2; //部分退款
                // }
                $order->status = -2;
                $order->refund_status = 2;
                $order->save();
            }else{
                //拒绝后恢复订单状态
                $orderProductModel->where(['order_product_id'=>['in',$order_product_ids]])->update(['is_refund'=>0]); //订单商品状态 改为已退款

                $order->refund_status = 0;
                $order->save();
            }

            $this->success("审核成功");
        }
        return $this->view->fetch();
    }

    public function product($ids = null){
        //当前是否为关联查询
        $this->relationSearch = false;
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            // $product_ids = $this->request->get('product_ids');
            $order_product_ids = $this->request->get('order_product_ids');
            $where2 = [];
            // if($product_ids){
            //     $where2['product_id'] = ['in', $product_ids];
            // }

            if($order_product_ids){
                $where2['order_product_id'] = ['in', $order_product_ids];
            }

            $list = $this->orderProductModel
                ->with(['product'])
                ->field('order_product_id,product_num,cost,product_price,profit,total_price')
                ->where($where2)
                ->where($where)
                ->order($sort, $order)
                ->paginate($limit);

            foreach ($list as $row) {
                $row->getRelation('product')->visible(['title','image']);
            }

            //$this->view->assign('productList', $list->items());
            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        //获取商品id
        $refund_id = $ids;
        $refundProductModel = new \app\admin\model\order\RefundProduct;
        // $orderProductModel = new \app\admin\model\order\OrderProduct;
        $refundProductList = $refundProductModel->where(['refund_id' => $refund_id])->select();
        // $product_id = [];
        $order_product_id = [];
        foreach ($refundProductList as $key => $value) {
            $order_product_id[] = $value['order_product_id'];
            // $orderProduct = $orderProductModel->where(['order_product_id' => $value['order_product_id']])->find();
            // $product_id[] = $orderProduct['product_id'];
        }
        // $product_ids = implode(',', $product_id);
        $order_product_ids = implode(',', $order_product_id);

        // $this->assignconfig('product_ids', $product_ids);
        $this->assignconfig('order_product_ids', $order_product_ids);
        return $this->view->fetch();
    }
}
