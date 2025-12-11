<?php

namespace app\admin\controller\order;

use app\common\controller\Backend;
use think\Db;
use think\exception\DbException;
use think\exception\PDOException;
use think\exception\ValidateException;
use think\Queue;

/**
 * 订单管理
 *
 * @icon fa fa-circle-o
 */
class Order extends Backend
{
    protected $noNeedRight = ['product','status','delivery','change_status','delivery_batch','received'];
    /**
     * Order模型对象
     * @var \app\admin\model\order\Order
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\order\Order;
        $this->productModel = new \app\admin\model\order\OrderProduct;
        $this->deliveryModel = new \app\admin\model\order\Delivery;
        $this->customModel = new \app\admin\model\logistics\Custom;
        $this->logisticsModel = new \app\admin\model\logistics\Logistics;
        $this->merchantModel = new \app\admin\model\merchant\Merchant;

        $this->view->assign("statusList", $this->model->getStatusList());
        $this->view->assign("deliveryStatusList", $this->model->getDeliveryStatusList());

        $logisticsStatus = [ '0' => '已暂停', '1' => '正常','2' => '已执行', '3' => '已完成'];
        $this->view->assign("logisticsStatus", $logisticsStatus);

        //$logistics = $this->logisticsModel->select();
        //$this->view->assign("logistics", $logistics);
        $this->assignconfig('auth_edit', $this->auth->check('order/order/edit'));
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

            $isSuperAdmin = $this->auth->isSuperAdmin();
            $whereAgent = [];
            if(!$isSuperAdmin){
                $admin_id = $this->auth->id;
                $whereAgent['merchant.agent_id'] = $admin_id;
            }

            $list = $this->model
                    ->with(['group','user','merchant'])
                    ->where($where)
                    ->where($whereAgent)
                    ->order($sort, $order)
                    ->paginate($limit);

            foreach ($list as $row) {
                
                $row->getRelation('group')->visible(['group_order_id','group_order_sn']);
				$row->getRelation('user')->visible(['id','username','nickname']);
				$row->getRelation('merchant')->visible(['mer_id','mer_name']);
            }

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }


    public function product($ids = null){
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

            $order_id = $this->request->get('order_id');
            $where2 = [];
            if($order_id){
                $where2['order_product.order_id'] = $order_id;
            }

            $list = $this->productModel
                ->with(['order', 'product'])
                ->field('product_num,order_product.cost,product_price,(product_num*order_product.cost) AS total_cost,order_product.total_price, profit,(profit*product_num) AS total_profit')
                ->where($where2)
                ->where($where)
                ->order($sort, $order)
                ->paginate($limit);

            foreach ($list as $row) {
                $row->getRelation('order')->visible(['order_sn']);
                $row->getRelation('product')->visible(['title','image']);
            }

            //$this->view->assign('productList', $list->items());
            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        $this->assignconfig('order_id', $ids);
        return $this->view->fetch();
    }

    /**
     * 更改状态
     */
    public function status($ids)
    {
        $status = $this->request->get('status');
        $row = $this->model->get(['order_id' => $ids]);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        if ($this->request->isAjax()) {
            $row->status = $status;
            $row->save();
            $this->success("状态修改成功");
        }

        $this->view->assign("row", $row->toArray());
        return $this->view->fetch();
    }

    /**
     * 更改状态
     */
    public function change_status($ids)
    {
        $row = $this->model->get(['order_id' => $ids]);
        if (!$row) {
            $this->error(__('No Results were found'));
        }

        if ($this->request->isPost()) {
            $params = $this->request->post('row/a');
            $row->delivery_status = $params['delivery_status'];
            $row->save();

            if($params['delivery_status'] == 4){ //物流状态更新
                $this->deliveryModel->where(['delivery_no' => $row->delivery_id])->update(['status' => 2]); //全部已执行
            }

            $this->success("状态修改成功");
        }

        $this->view->assign("row", $row->toArray());
        return $this->view->fetch();
    }

    /**
     * 发货
     *
     * @param $ids
     * @return string
     * @throws DbException
     * @throws \think\Exception
     */
    public function delivery($ids = null){
        $type = $this->request->get('type');
        $row = $this->model->field('order_id,order_sn,delivery_id')->where(['order_id' => $ids])->find();
        if (!$row) {
            $this->error(__('No Results were found'));
        }

        if (false === $this->request->isPost()) {
            $deliveryList = $this->deliveryModel->where(['delivery_no' => $row->delivery_id])->select();
            $row['deliveryList'] = $deliveryList;

            //总次数
            $total = $this->deliveryModel->where(['delivery_no' => $row->delivery_id])->count();
            //执行次数
            $yes = $this->deliveryModel->where(['delivery_no' => $row->delivery_id, 'status' => 3])->count();
            if($yes == $total){
                $row['delivery_status'] = '已完成';
            }else{
                $row['delivery_status'] = '未完成';
            }
            //最近触发时间
            $last_time = $this->deliveryModel->where(['delivery_no' => $row->delivery_id, 'status' => ['in',[2,3]]])->order('step DESC')->value('updatetime');
            $row['last_time'] = $last_time ? date('Y-m-d H:i:s',$last_time) : '';

            $this->view->assign('row', $row);
            $this->view->assign('type', $type);
            return $this->view->fetch();
        }
        $params = $this->request->post('row/a');
        $result = false;
        Db::startTrans();
        try {
            $row->delivery_id = $row->order_sn;
            $row->delivery_status = 1;
            $row->delivery_time = time();
            $row->status = 2;
            $row->save();

            //发货后自动进入等待自动收货队列
            $later = config('site.order_received')*86400;
            Queue::later($later, 'app\admin\job\OrderAutoReceived' , $row , 'OrderAutoReceived');

            $deliveryList = [];
            if($params['use'] == 1){//使用系统模板
                $deliveryList = $this->logisticsModel->where(['status' => 1])->select();
            }else {
                $delivery = $params['delivery'];
                $deliveryList = json_decode($delivery, true);
            }
            $arr = [];
            foreach ($deliveryList as $v){
                $arr[] = [
                    'delivery_no' => $row->delivery_id,
                    'step' => $v['step'],
                    'status' => $v['status'],
                    'interval' => $v['interval'],
                    'mark' => $v['mark'],
                ];
            }

            $result = $this->deliveryModel->saveAll($arr);

            //进入队列等待执行
            $list = $this->deliveryModel->where(['delivery_no' => $row->delivery_id])->select();
            $delay = 0;
            foreach ($list as $vv){
                $delay += $vv['interval'];
                $vv['order_id'] = $row['order_id'];
                Queue::later($delay, 'app\admin\job\DeliveryAutoFinish' , $vv , 'DeliveryAutoFinish');
            }

            //$result = $row->allowField(true)->save($params);
            Db::commit();
        } catch (ValidateException|PDOException|Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        if (false === $result) {
            $this->error(__('No rows were updated'));
        }
        $this->success();
    }

    /**
     * 确认收货
     */
    public function received($ids = null)
    {
        $row = $this->model->where(['order_id' => $ids])->find();
        if (!$row) {
            $this->error(__('No Results were found'));
        }

        if ($row->status != 2) {
            $this->error('The order was not shipped');
        }

        $row->status = 3;
        $row->received_time = time();
        $row->verify_time = time();
        $row->save();

        //用户确认收货，商户增加余额(订单总金额)
        $this->merchantModel->money($row->total_price, $row->mer_id, $row->order_sn, 1, 'order', "订单完成增加余额".$row->total_price);

        $mer = $this->merchantModel->where(['mer_id' => $row->mer_id])->find();
        if($mer['spread_id']){
            //存在上级，上级获取佣金
            $this->merchantModel->money($row->extension_one, $mer['spread_id'], $row->order_sn, 0, 'order', "下级商户订单完成增加佣金" . $row->extension_one);
        }

        //订单自动归档加入队列
        $delay = config('site.order_finish')*86400;
        Queue::later($delay, 'app\admin\job\OrderAutoFinish' , $row , 'OrderAutoFinish');

        $this->success('已确认收货');
    }

    /**
     * 批量发货
     *
     * @return string
     * @throws DbException
     * @throws \think\Exception
     */
    public function delivery_batch(){
        if($this->request->isAjax()){
            $ids = $this->request->post('ids/a');

            $result = false;
            Db::startTrans();
            try {
                foreach ($ids as $id) {
                    $row = $this->model->field('order_id,order_sn,delivery_id,status,is_pick')->where(['order_id' => $id])->find();
                    if($row->status != 1 || $row->is_pick != 1){
                        $this->error('订单'.$row->order_sn.'不可发货');
                    }
                    $row->delivery_id = $row->order_sn;
                    $row->delivery_status = 1;
                    $row->delivery_time = time();
                    $row->status = 2;
                    $row->save();

                    //发货后自动进入等待自动收货队列
                    $later = config('site.order_received') * 86400;
                    Queue::later($later, 'app\admin\job\OrderAutoReceived', $row, 'OrderAutoReceived');

                    $deliveryList = $this->logisticsModel->where(['status' => 1])->select();
                    $arr = [];
                    foreach ($deliveryList as $v) {
                        $arr[] = [
                            'delivery_no' => $row->delivery_id,
                            'step' => $v['step'],
                            'status' => $v['status'],
                            'interval' => $v['interval'],
                            'mark' => $v['mark'],
                        ];
                    }

                    $result = $this->deliveryModel->saveAll($arr);

                    //进入队列等待执行
                    $list = $this->deliveryModel->where(['delivery_no' => $row->delivery_id])->select();
                    $delay = 0;
                    foreach ($list as $vv) {
                        $delay += $vv['interval'];
                        $vv['order_id'] = $row['order_id'];
                        Queue::later($delay, 'app\admin\job\DeliveryAutoFinish', $vv, 'DeliveryAutoFinish');
                    }
                }

                Db::commit();
            } catch (ValidateException|PDOException|Exception $e) {
                Db::rollback();
                $this->error($e->getMessage());
            }
            if (false === $result) {
                $this->error(__('批量发货失败'));
            }
            $this->success('批量发货成功');
        }
    }
}
