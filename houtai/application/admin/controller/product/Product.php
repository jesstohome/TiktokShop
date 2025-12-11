<?php

namespace app\admin\controller\product;

use app\common\controller\Backend;
use think\Db;
use think\Exception;
use think\exception\DbException;
use think\exception\PDOException;
use think\exception\ValidateException;

/**
 * 产品管理
 *
 * @icon fa fa-circle-o
 */
class Product extends Backend
{
    protected $noNeedRight = ['index','select'];
    /**
     * Product模型对象
     * @var \app\admin\model\product\Product
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\product\Product;
        $this->merModel = new \app\admin\model\merchant\Product;

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

            $mer_id = $this->request->get('mer_id' ,0);
            $include = $this->request->get('include', '','trim');
            $whereMer = [];
            if($mer_id && $include){
                $merProducts = $this->merModel->where(['mer_id'=>$mer_id])->select();
                $merProductIds = array_column(collection($merProducts)->toArray(), 'product_id');
                if($merProductIds){
                    if(trim($include) == 'in'){
                        $whereMer['product.product_id'] = ['in', $merProductIds];
                    }elseif(trim($include) == 'not'){
                        $whereMer['product.product_id'] = ['not in', $merProductIds];
                    }
                }else{
                    if(trim($include) == 'in') {
                        $whereMer['product.product_id'] = ['in', $merProductIds];
                    }
                }
            }

            $list = $this->model
                    ->with(['category','merchant'])
                    ->where($where)
                    ->where($whereMer)
                    ->order($sort, $order)
                    ->paginate($limit);

            foreach ($list as $row) {
                
                $row->getRelation('category')->visible(['category_id','name']);
				$row->getRelation('merchant')->visible(['mer_id','mer_name']);

                $commission_ratio = config('site.commission_ratio');
                $row->profit = bcmul($row->sales_price, $commission_ratio,2); //利润
                $row->profit_rate = $commission_ratio ? $commission_ratio * 100 . '%' : '0.00%'; //利润率
            }

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }


    /**
     * 选择附件
     */
    public function select()
    {
        if ($this->request->isAjax()) {
            return $this->index();
        }
        $mer_id_str = $this->request->get('mer_id');
        $include = $this->request->get('include','','trim');
        $mer_ids = explode('?', $mer_id_str);
        $mer_id = $mer_ids[0];
        $this->assignconfig('mer_id', $mer_id);
        $this->assignconfig('include', trim($include));
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
            if ($this->modelValidate) {
                $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : $name) : $this->modelValidate;
                $this->model->validateFailException()->validate($validate);
            }

            // 查看规格有没有添加
            if (!empty($params['use_spec']) && $params['use_spec'] == 1) {
                if (empty($params['specList']) || empty($params['specTableList']) || $params['specList'] == '""' || $params['specTableList'] == '""' || $params['specList'] == '[]' || $params['specTableList'] == '[]') {
                    $this->error(__('Spec can not be empty', ''));
                }
            }

            $result = $this->model->allowField(true)->save($params);
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

            // 查看规格有没有添加
            if (!empty($params['use_spec']) && $params['use_spec'] == 1) {
                if (empty($params['specList']) || empty($params['specTableList']) || $params['specList'] == '""' || $params['specTableList'] == '""' || $params['specList'] == '[]' || $params['specTableList'] == '[]') {
                    $this->error(__('Spec can not be empty', ''));
                }
            }

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
}
