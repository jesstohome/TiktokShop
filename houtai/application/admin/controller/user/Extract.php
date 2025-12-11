<?php

namespace app\admin\controller\user;

use app\common\controller\Backend;

/**
 * 用户提现管理
 *
 * @icon fa fa-circle-o
 */
class Extract extends Backend
{
    protected $noNeedRight = ['verify'];
    /**
     * UserExtract模型对象
     * @var \app\admin\model\UserExtract
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\UserExtract;
        $this->view->assign("extractTypeList", $this->model->getExtractTypeList());
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
                    ->with(['user','admin'])
                    ->where($where)
                    ->order($sort, $order)
                    ->paginate($limit);

            foreach ($list as $row) {
                
                $row->getRelation('user')->visible(['id','username','nickname']);
				$row->getRelation('admin')->visible(['id','username','nickname']);
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
            $row = $this->model->get(['extract_id' => $ids]);
            $row->status = $params['status'];
            $row->admin_msg = $params['admin_msg'];
            $row->admin_id = $this->auth->id;
            $row->save();

            //未通过 返回余额
            if ($params['status'] == -1) {
                $userModel = new \app\common\model\User;
                $userModel->money($row['extract_price'], $row['user_id'], $row['extract_sn'],1,'extract',"The user failed to withdraw the return balance $".$row['extract_price']);
            }

            $this->success("审核成功");
        }
        return $this->view->fetch();
    }
}
