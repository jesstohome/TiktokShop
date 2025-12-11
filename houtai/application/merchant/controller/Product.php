<?php

namespace app\merchant\controller;

use app\common\controller\Mer;
use app\admin\model\product\Category as CategoryModel;
use app\admin\model\product\Product as ProductModel;
use app\merchant\model\merchant\Product as MerProductModel;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;

/**
 * 商品接口
 */
class Product extends Mer
{
    protected $noNeedLogin = [];
    protected $noNeedRight = ['*'];

    /**
     * 热销商品
     *
     */
    public function hot()
    {
        //商品销售量前十
        $list = MerProductModel::field('id,product_id,sales,click')->where(['switch'=>1,'mer_id'=>$this->auth->mer_id])->order('sales DESC')->limit(10)->select();
        foreach ($list as $row) {
            $row['goods'] = ProductModel::field('product_id,title,title_en,image,sales_price')->where(['product_id'=>$row['product_id']])->find();
        }

        $this->success(__('请求成功'),$list);
    }

    /**
     * 商品分类
     *
     */
    public function category()
    {
        //商品分类
        $category = CategoryModel::field('category_id,name,name_en')->where(['is_show'=>1])->select();

        $this->success(__('请求成功'),$category);
    }

    /**
     * 铺货中心
     *
     * @ApiMethod (GET)
     * @param int $category_id 分类ID
     * @param string $title 商品名称
     * @param int $page 页码
     * @param int $limit 每页数量
     */
    public function product(){
        $category_id = $this->request->get('category_id');
        $title = $this->request->get('title');  //查询条件
        $page = $this->request->get('page',1);
        $limit = $this->request->get('limit',10);

        $where = ['switch'=>1];
        if($category_id){
            $where['category_id'] = $category_id;
        }

        $whereOr = [];
        if($title){
            $where['title'] = ['like', '%'.$title.'%'];
            // $whereOr['title_en'] = ['like', '%'.$title.'%'];
        }

        //已铺货的商品
        $already = MerProductModel::where(['mer_id'=>$this->auth->mer_id])->select();
        $product_ids = [];
        foreach ($already as $row) {
            $product_ids[] = $row['product_id'];
        }
        $where['product_id'] = ['not in', $product_ids];

        //可铺货的商品
        $count = ProductModel::where($where)->count();
        $list = ProductModel::field('product_id,title,title_en,image,sales_price,switch,is_hot')->where($where)->whereOr($whereOr)->order('product_id DESC')->page($page,$limit)->select();
        $data = [
            'total' => $count,
            'list' => $list
        ];
        $this->success(__('请求成功'),$data);
    }

    /**
     * 铺货
     *
     * @ApiMethod (POST)
     * @param string $ids 商品ID(多个用，连接)
     */
    public function add(){
        $ids = $this->request->post('ids');
        if(!$ids){
            $this->error(__('参数错误'));
        }
        $mer_id = $this->auth->mer_id;
        $ids = explode(',', $ids);
        $data = [];
        foreach ($ids as $id) {
            $count = (new MerProductModel)->where(['mer_id'=>$mer_id,'product_id'=>$id])->count();
            if($count > 0) {
                continue;
            }
            $data[] = [
                'mer_id' => $mer_id,
                'product_id' => $id,
                'switch' => 1,
                'is_ad' => 0,
            ];
        }

        $result = false;
        Db::startTrans();
        try {
            $result = (new MerProductModel)->allowField(true)->saveAll($data);

            Db::commit();
        } catch (ValidateException|PDOException|Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        if ($result === false) {
            $this->error(__('铺货失败'));
        }

        $this->success(__('铺货成功'));
    }

    /**
     * 一键铺货
     *
     * @ApiMethod (POST)
     */
    public function addAll(){
        // $product_ids = $this->request->post('ids');
        $ids = file_get_contents('php://input');
        $ids_arr = json_decode($ids,true);
        $product_ids = $ids_arr['ids'];
        if(!$product_ids){
            $this->error(__('未选中商品'));
        }

        $where = [
            // 'switch'=>1,
            'product_id'=>['in', $product_ids]
        ];
        //已铺货的商品
        // $already = MerProductModel::where(['mer_id'=>$this->auth->mer_id])->select();
        // $product_ids = [];
        // foreach ($already as $row) {
        //     $product_ids[] = $row['product_id'];
        // }
        // $where['product_id'] = ['not in', $product_ids];

        //可铺货的商品
        $list = ProductModel::field('product_id,title,title_en,image,sales_price')->where($where)->select();

        $mer_id = $this->auth->mer_id;
        $data = [];
        foreach ($list as $row) {
            $count = (new MerProductModel)->where(['mer_id'=>$mer_id,'product_id'=>$row['product_id']])->count();
            if($count > 0) {
                continue;
            }
            $data[] = [
                'mer_id' => $mer_id,
                'product_id' => $row['product_id'],
                'switch' => 1,
                'is_ad' => 0,
            ];
        }

        $result = false;
        Db::startTrans();
        try {
            $result = (new MerProductModel)->allowField(true)->saveAll($data);

            Db::commit();
        } catch (ValidateException|PDOException|Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        if ($result === false) {
            $this->error(__('一键铺货失败'));
        }

        $this->success(__('一键铺货成功'));
    }

    /**
     * 一键铺货热门商品
     *
     * @ApiMethod (POST)
     */
    public function addAllHot(){
        // $product_ids = $this->request->post('ids');

        //已铺货的商品
        $already = (new MerProductModel)->where(['mer_id'=>$this->auth->mer_id])->select();
        $product_ids = [];
        foreach ($already as $v) {
            $product_ids[] = $v['product_id'];
        }
        $where['product_id'] = ['not in', $product_ids];
        $where['is_hot'] = 1;

        //可铺货的商品
        $list = ProductModel::field('product_id,title,title_en,image,sales_price')->where($where)->select();

        $mer_id = $this->auth->mer_id;
        $data = [];
        foreach ($list as $row) {
            // $count = (new MerProductModel)->where(['mer_id'=>$mer_id,'product_id'=>$row['product_id']])->count();
            // if($count > 0) {
            //     continue;
            // }
            $data[] = [
                'mer_id' => $mer_id,
                'product_id' => $row['product_id'],
                'switch' => 1,
                'is_ad' => 0,
            ];
        }

        $result = false;
        Db::startTrans();
        try {
            $result = (new MerProductModel)->allowField(true)->saveAll($data);

            Db::commit();
        } catch (ValidateException|PDOException|Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        if ($result === false) {
            $this->error(__('一键铺货失败'));
        }

        $this->success(__('一键铺货成功'));
    }

    /**
     * 删除橱窗商品
     *
     * @ApiMethod (POST)
     * @param string $ids 商品ID(多个用，连接)
     */
    public function del(){
        $ids = $this->request->post('ids');
        if(!$ids){
            $this->error(__('参数错误'));
        }
        //$ids = explode(',', $ids);
        $result = MerProductModel::where(['mer_id'=>$this->auth->mer_id,'product_id'=>['in', $ids]])->delete();
        if($result === false){
            $this->error(__('删除失败'));
        }
        $this->success(__('删除成功'));
    }

    /**
     * 商品管理
     *
     * @ApiMethod (GET)
     * @param int $switch 状态
     * @param string $title 商品名称
     * @param int $page 页码
     * @param int $limit 每页数量
     */
    public function manage(){
        $switch = $this->request->get('switch', 'all');
        $title = $this->request->get('title');  //查询条件
        $page = $this->request->get('page',1);
        $limit = $this->request->get('limit',10);

        $where = ['product.mer_id'=>$this->auth->mer_id,'goods.switch' => 1];
        if($switch != 'all'){
            $where['product.switch'] = $switch;
        }

        $whereOr = [];
        if($title){
            $where['goods.title'] = ['like', '%'.$title.'%'];
            // $whereOr['goods.title_en'] = ['like', '%'.$title.'%'];
        }

        // $count = MerProductModel::where($where)->count();
        // $list = MerProductModel::field('id,product_id,switch')->where($where)->page($page,$limit)->select();
        // foreach ($list as $row) {
        //     $row['goods'] = ProductModel::field('product_id,title,title_en,image,sales_price,cost_price,(sales_price - cost_price) AS profit')->where(['product_id'=>$row['product_id']])->find();
        // }

        $merProductModel = new \app\admin\model\merchant\Product;

        $count = $merProductModel->with(['goods'])->where($where)->count();
        $list = $merProductModel->alias('product')
            ->field('id,mer_id,product_id,sales,click,switch')
            ->with(['goods'])
            ->where($where)
            ->whereOr($whereOr)
            ->order('id DESC')
            ->page($page,$limit)
            ->select();

        $commission_ratio = config('site.commission_ratio');
        foreach ($list as $row) {
            $row->getRelation('goods')->visible(['product_id','title','title_en','image','sales_price','cost_price']);
            // $row->goods->profit = bcsub($row->goods->sales_price,$row->goods->cost_price,2);
            // $row->profit = bcsub($row->goods->sales_price,$row->goods->cost_price,2);
            $row->profit = bcmul($row->goods->sales_price, $commission_ratio,2);
        }

        $data = [
            'total' => $count,
            'list' => $list
        ];
        $this->success(__('请求成功'),$data);
    }

    /**
     * 商品上下架
     *
     * @ApiMethod (POST)
     * @param int $switch 状态
     * @param int $id 商户商品id
     */
    public function setSwitch(){
        $id = $this->request->post('id');
        $switch = $this->request->post('switch');
        if(!$id){
            $this->error(__('参数错误'));
        }
        MerProductModel::where(['id'=>$id])->update(['switch'=>$switch]);
        $msg = $switch ? __('上架成功') : __('下架成功');
        $this->success($msg);
    }

    /**
     * 商品详情
     *
     * @ApiMethod (GET)
     * @param int $product_id 商品ID
     */
    public function detail(){

        $product_id = $this->request->get('product_id');
        if(!$product_id){
            $this->error(__('参数错误'));
        }
        $info = ProductModel::field('product_id,code,title,title_en,image,images,content,sales_price,cost_price,stock,look,use_spec,specList,specTableList')->where(['product_id'=>$product_id])->find();
        $commission_ratio = config('site.commission_ratio');
        $info->profit = bcmul($info->sales_price, $commission_ratio,2);
        $this->success(__('请求成功'),$info);
    }
}
