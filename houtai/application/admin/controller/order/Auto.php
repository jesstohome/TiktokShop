<?php

namespace app\admin\controller\order;

use app\common\controller\Backend;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;
use think\Queue;

/**
 * 自动下单管理
 *
 * @icon fa fa-circle-o
 */
class Auto extends Backend
{

    /**
     * Auto模型对象
     * @var \app\admin\model\order\Auto
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\order\Auto;
        $this->productModel = new \app\admin\model\product\Product;

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
                
                // $whereAgent['agent_id'] = $admin_id;
             
              $list = $this->model
                ->with(['merchant' => function($query){
                        $admin_id = $this->auth->id;
                        $query->where('agent_id', '=', $admin_id);
                    },'user'])
                ->where($where)
                ->order($sort, $order)
                ->paginate($limit);
            }else{
                $list = $this->model
                ->with(['merchant','user'])
                ->where($where)
                ->order($sort, $order)
                ->paginate($limit);
            }
          

            foreach ($list as $row) {

                $row->getRelation('merchant')->visible(['mer_id','mer_name']);
                $row->getRelation('user')->visible(['id','username','nickname']);
            }

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 添加
     *
     * @return string
     * @throws \think\Exception
     */
    public function add()
    {
        if (false === $this->request->isPost()) {
            return $this->view->fetch();
        }
        $params = $this->request->post('row/a');
        if (empty($params)) {
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $params = $this->preExcludeFields($params);

        if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
            $params[$this->dataLimitField] = $this->auth->id;
        }
        $result = false;
        Db::startTrans();
        try {
            //是否采用模型验证
            $result = $this->model->allowField(true)->save($params);

            //添加队列
            if($result) {
                $delay = strtotime($params['start_time']) - time();
                $delay = $delay >= 0 ? $delay : 0;
                $data = [
                    'id' => $this->model->id,
                    'mer_id' => $params['mer_id'],
                    'user_id' => $params['user_id'],
                    'product_id' => $params['product_id'],
                    'num' => $params['num'],
                ];
                Queue::later($delay, 'app\admin\job\OrderAutoCreate', $data, 'OrderAutoCreate');
            }

            Db::commit();
        } catch (ValidateException|PDOException|Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        if ($result === false) {
            $this->error(__('No rows were inserted'));
        }
        $this->success();
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

            $product_id = $this->request->get('product_id');
            $num = $this->request->get('num');
            $where2 = [];
            if($product_id){
                $where2['product_id'] = ['in',$product_id];
            }

            $list = $this->productModel
                ->field('product_id,title,image,cost_price,sales_price,(sales_price - cost_price) AS profit,createtime')
                ->where($where2)
                ->where($where)
                ->order($sort, $order)
                ->paginate($limit);
            foreach ($list as &$row){
                $row['num'] = $num;
            }
            //$this->view->assign('productList', $list->items());
            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        $product_id = $this->request->get('product_id');
        $num = $this->request->get('num');
        $this->assignconfig('product_id', $product_id);
        $this->assignconfig('num', $num);
        return $this->view->fetch();
    }
}
