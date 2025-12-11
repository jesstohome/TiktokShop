<?php

namespace app\admin\controller\order;

use app\common\controller\Backend;

/**
 * 订单购物详情管理
 *
 * @icon fa fa-circle-o
 */
class OrderProduct extends Backend
{

    /**
     * OrderProduct模型对象
     * @var \app\admin\model\order\OrderProduct
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\order\OrderProduct;

    }



    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */


    /**
     * 查看
     */
    public function index($ids = null)
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

            $order_id = $this->request->get('order_id');
            $where2 = [];
            if($order_id){
                $where2['order_id'] = $order_id;
            }

            $list = $this->model
                    ->with(['order','user','product'])
                    ->field('product_num,cost,product_price,(product_num*cost) AS total_cost,total_price,(product_price-cost) AS profit,(total_price-total_cost) AS total_profit')
                    ->where($where2)
                    ->where($where)
                    ->order($sort, $order)
                    ->paginate($limit);

            foreach ($list as $row) {
                
                $row->getRelation('order')->visible(['order_id','order_sn']);
				$row->getRelation('user')->visible(['id','username']);
				$row->getRelation('product')->visible(['product_id','title']);
            }

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        $this->assignconfig('order_id', $ids);
        return $this->view->fetch();
    }

}
