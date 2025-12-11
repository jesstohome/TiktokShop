<?php

namespace app\admin\controller\merchant;

use app\common\controller\Backend;
use think\Db;
use think\exception\DbException;
use think\exception\PDOException;
use think\exception\ValidateException;

/**
 * 商户提现管理
 *
 * @icon fa fa-circle-o
 */
class Extract extends Backend
{
    protected $noNeedRight = ['verify'];
    /**
     * Extract模型对象
     * @var \app\admin\model\merchant\Extract
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\merchant\Extract;
        $this->merchantModel = new \app\admin\model\merchant\Merchant;
        $this->noticeModel = new \app\admin\model\merchant\Notice;
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
            
             $isSuperAdmin = $this->auth->isSuperAdmin();
            $whereAgent = [];
            if(!$isSuperAdmin){
                
                // $whereAgent['agent_id'] = $admin_id;
             
                 $list = $this->model
                    ->with(['merchant' => function($query){
                        $admin_id = $this->auth->id;
                        $query->where('agent_id', '=', $admin_id);
                    },'admin'])
                    ->where($where)
                    ->order($sort, $order)
                    ->paginate($limit);
            }else{
               $list = $this->model
                    ->with(['merchant','admin'])
                    ->where($where)
                    ->order($sort, $order)
                    ->paginate($limit);
            }
           

            foreach ($list as $row) {
                
                $row->getRelation('merchant')->visible(['mer_id','mer_name']);
                $row->getRelation('admin')->visible(['id','username','nickname']);
            }

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 编辑
     *
     * @param $ids
     * @return string
     * @throws DbException
     * @throws \think\Exception
     */
    public function edit($ids = null)
    {
        $row = $this->model->get($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds) && !in_array($row[$this->dataLimitField], $adminIds)) {
            $this->error(__('You have no permission'));
        }
        if (false === $this->request->isPost()) {
            $this->view->assign('row', $row);
            return $this->view->fetch();
        }
        $params = $this->request->post('row/a');
        if (empty($params)) {
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $params = $this->preExcludeFields($params);
        $result = false;
        Db::startTrans();
        try {
            //是否采用模型验证
            if ($this->modelValidate) {
                $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : $name) : $this->modelValidate;
                $row->validateFailException()->validate($validate);
            }

            //未通过 返回余额
            // if ($params['status'] == -1) {
            //     $this->merchantModel->money($params['money'],$params['mer_id'],$params['extract_sn'],1,'extract','提现审核未通过');
            // }

            $result = $row->allowField(true)->save($params);
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
                $this->merchantModel->money($row['extract_price'],$row['mer_id'],$row['extract_sn'],1,'extract',"The user failed to withdraw the return balance ".$row['extract_price']);
            }

            //给商户发通知
            $notice = [
                'mer_id' => $row['mer_id'],
                'type' => 1,
                'user' => '系统',
                'title' => '提现审核通知',
                'content' => '您的提现已审核，请注意查看',
            ];
            $this->noticeModel->create($notice);

            $this->success("审核成功");
        }
        return $this->view->fetch();
    }
}
