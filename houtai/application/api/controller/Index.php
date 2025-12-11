<?php

namespace app\api\controller;

use app\admin\model\product\Category as CategoryModel;
use app\admin\model\merchant\Product as MerProductModel;
use app\api\model\Product as ProductModel;
use app\api\model\Merchant as MerchantModel;
use app\api\model\Order as OrderModel;
use app\common\controller\Api;
use fast\Http;

/**
 * 首页接口
 */
class Index extends Api
{
    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['*'];

    /**
     * 首页
     *
     */
    public function index()
    {
        $data = [
            'logo' => cdnurl(config('site.logo'),true),
            'name' => config('site.name'),
            'custom_service' => config('site.custom_service'),
            'merchant_link' => config('site.merchant_link'),
        ];
        $this->success(__('请求成功'),$data);
    }

    /**
     * 首页轮播
     */
    public function banner(){
        $banner = implode(',',config('site.banner'));

        $list = explode(',',$banner);
        foreach ($list as &$v){
            $v = cdnurl($v,true);
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
     * 首页商品
     *
     * @ApiMethod (GET)
     * @param int $category_id 分类ID
     * @param string $title 商品名称
     * @param int $page 页码
     * @param int $limit 每页数量
     */
    public function product(){
        $category_id = $this->request->get('category_id', 0);
        $title = $this->request->get('title');  //查询条件
        $page = $this->request->get('page',1);
        $limit = $this->request->get('limit',10);

        $where = ['product.switch'=>1,'goods.switch'=>1];
        if($category_id){
            $where['goods.category_id'] = $category_id;
        }else{ //无分类展示推荐商品
            $where['goods.is_recommend'] = 1;
        }

        $whereOr = [];
        if($title){
            $where['goods.title'] = ['like', '%'.$title.'%'];
            // $whereOr['goods.title_en'] = ['like', '%'.$title.'%'];
        }

        //商品
        //$count = ProductModel::where($where)->count();
        //$list = ProductModel::field('product_id,title,title_en,image,sales_price')->where($where)->whereOr($whereOr)->page($page,$limit)->select();

        $merProductModel = new \app\admin\model\merchant\Product;

        $count = $merProductModel->with(['goods'])->where($where)->count();
        $list = $merProductModel->alias('product')
            ->field('id,mer_id,product_id,sales,click')
            ->with(['goods'])
            ->where($where)
            ->whereOr($whereOr)
            ->order('sales DESC')
            ->page($page,$limit)
            ->select();

        foreach ($list as $row) {
            $row->getRelation('goods')->visible(['product_id','title','title_en','image','sales_price','cost_price','market_price','is_recommend','real_sales']);
        }

        $data = [
            'total' => $count,
            'list' => $list
        ];
        $this->success(__('请求成功'),$data);
    }

    /**
     * 热门商品
     * @ApiMethod (GET)
     *
     * @param string $title 商品名称
     * @param int $page 页码
     * @param int $limit 每页数量
     */
    public function hot_product()
    {
        $title = $this->request->get('title');  //查询条件
        $page = $this->request->get('page',1);
        $limit = $this->request->get('limit',10);

        $where = ['product.switch'=>1,'goods.switch'=>1,'goods.is_hot'=>1];

        $whereOr = [];
        if($title){
            $where['goods.title'] = ['like', '%'.$title.'%'];
            // $whereOr['goods.title_en'] = ['like', '%'.$title.'%'];
        }

        $merProductModel = new \app\admin\model\merchant\Product;

        $count = $merProductModel->with(['goods'])->where($where)->count();
        $list = $merProductModel->alias('product')
            ->field('id,mer_id,product_id,sales,click')
            ->with(['goods'])
            ->where($where)
            // ->where($whereOr)
            ->order('sales DESC')
            ->page($page,$limit)
            ->select();

        foreach ($list as $row) {
            $row->getRelation('goods')->visible(['product_id','title','title_en','image','market_price','sales_price','cost_price','real_sales']);
        }

        if($title && $this->auth->id) {
            //存储最近查询
            $searchModel = new \app\api\model\UserSearch;
            //查询关键字是否搜索过
            $search = $searchModel->where(['user_id' => $this->auth->id, 'keyword' => $title])->find();
            if ($search) {
                $search->num += 1;
                $search->save();
            } else {
                $searchModel->user_id = $this->auth->id;
                $searchModel->keyword = $title;
                $searchModel->num = 1;
                $searchModel->save();
            }
        }

        $data = [
            'total' => $count,
            'list' => $list
        ];
        $this->success(__('请求成功'),$data);
    }

    /**
     * 热门商户
     *
     * @ApiMethod (GET)
     * @param int $page 页码
     * @param int $limit 每页数量
     */
    public function hot_merchant()
    {

        $page = $this->request->get('page',1);
        $limit = $this->request->get('limit',10);

        $where = ['status'=>1];
        //热门商户
        $count = MerchantModel::where($where)->count();
        $list = MerchantModel::field('mer_id,mer_name,mer_avatar,follow_count,good_rate')->where($where)->order('follow_count')->page($page,$limit)->select();

        foreach ($list as $value){
            //销售总额
            $total_price = OrderModel::where(['mer_id'=>$value['mer_id'],'status'=>['in',[1,2,3,4]]])->sum('total_price');
            $value['total_price'] = $total_price;
        }

        $data = [
            'total' => $count,
            'list' => $list
        ];
        $this->success(__('请求成功'),$data);
    }

    /**
     * 自动添加访问量和关注量
     *
     * @ApiMethod (GET)
     */
    public function setInc(){
        $model = new \app\merchant\model\merchant\Merchant;
        $modelp = new \app\merchant\model\merchant\Product;
        $set_inc = config('site.set_inc');
        $model->where('status','in',[0,1])->setInc('visit',$set_inc);
        $model->where('status','in',[0,1])->setInc('follow_count',$set_inc);
        $modelp->where('switch','in',[0,1])->setInc('click',$set_inc);
        $modelp->where('switch','in',[0,1])->setInc('sales',$set_inc);
        $this->success(__('添加成功'));
    }

    /**
     * 自动清除访问量和关注量
     *
     * @ApiMethod (GET)
     */
    public function clear(){
        $model = new \app\merchant\model\merchant\Merchant;
        $modelp = new \app\merchant\model\merchant\Product;
        $model->where('status','in',[0,1])->update(['visit'=>0,'follow_count'=>0]);
        $modelp->where('switch','in',[0,1])->update(['click'=>0,'sales'=>0]);
        $this->success(__('清零成功'));
    }
}
