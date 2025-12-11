<?php

namespace app\admin\controller\merchant;

use app\common\controller\Backend;
use think\Db;
use think\exception\DbException;
use think\exception\PDOException;
use think\exception\ValidateException;

/**
 * 商户申请
 *
 * @icon fa fa-circle-o
 */
class Intention extends Backend
{

    /**
     * Intention模型对象
     * @var \app\admin\model\merchant\Intention
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\merchant\Intention;
        $this->typeModel = new \app\admin\model\merchant\Type;
        $this->adminModel = new \app\admin\model\merchant\Admin;
        $this->merModel = new \app\admin\model\merchant\Merchant;

        $this->view->assign("statusList", $this->model->getStatusList());

        $typeList = $this->typeModel->order('mer_type_id desc')->select();
        $this->view->assign("typeList", $typeList);
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
            // dump(json_decode(json_encode($where),true));die;
            
              $isSuperAdmin = $this->auth->isSuperAdmin();
            $whereAgent = [];
            if(!$isSuperAdmin){
                $admin_id = $this->auth->id;
                $whereAgent['agent_id'] = $admin_id;
            }
            
            $list = $this->model
                    ->with(['user','merchant','type'])
                    ->where($where)
                    ->where($whereAgent)
                    ->order($sort, $order)
                    ->paginate($limit);

            foreach ($list as $row) {
                
                $row->getRelation('user')->visible(['username']);
				$row->getRelation('merchant')->visible(['mer_name']);
				$row->getRelation('type')->visible(['type_name']);
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
            $result = $row->allowField(true)->save($params);

            //审核通过生成账号
            //if($params['status'] == 1){
                //$this->adminModel->addAccount($params);
            //}
            //审核通过商户账号启用
            if($params['status'] == 1){
                $this->merModel->where(['mer_id'=>$row['mer_id']])->update(['status'=>1]);
            }

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

}
