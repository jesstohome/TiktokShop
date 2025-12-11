<?php

namespace app\admin\controller\merchant;

use app\common\controller\Backend;

/**
 * 商户充值管理
 *
 * @icon fa fa-circle-o
 */
class Recharge extends Backend
{
    protected $noNeedRight = ['verify'];
    /**
     * Recharge模型对象
     * @var \app\admin\model\merchant\Recharge
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\merchant\Recharge;
        $this->merchantModel = new \app\admin\model\merchant\Merchant;
        $this->noticeModel = new \app\admin\model\merchant\Notice;
        $this->view->assign("rechargeTypeList", $this->model->getRechargeTypeList());
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
                    }])
                    ->where($where)
                    ->order($sort, $order)
                    ->paginate($limit);
            }else{
                $list = $this->model
                    ->with(['merchant'])
                    ->where($where)
                    ->order($sort, $order)
                    ->paginate($limit); 
            }
            
           

            foreach ($list as $row) {
                
                $row->getRelation('merchant')->visible(['mer_id','mer_name']);
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
            $row = $this->model->get(['recharge_id' => $ids]);
            $row->status = $params['status'];
            $row->admin_msg = $params['admin_msg'];
            $row->admin_id = $this->auth->id;
            $row->save();

            //通过 增加余额
            if ($params['status'] == 1) {
                $this->merchantModel->money($row['price'], $row['mer_id'], $row['order_id'],1,'recharge',"Approved,merchant increase the balance ".$row['price']);
            }

            //给商户发通知
            $notice = [
                'mer_id' => $row['mer_id'],
                'type' => 1,
                'user' => '系统',
                'title' => '充值审核通知',
                'content' => '您的充值已审核，请注意查看',
            ];
            $this->noticeModel->create($notice);

            $this->success("审核成功");
        }
        return $this->view->fetch();
    }
}
