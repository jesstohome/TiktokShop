<?php

namespace app\api\controller;

use app\api\model\Merchant as MerchantModel;
use app\api\model\Order;
use app\common\controller\Api;
use app\api\model\Product as ProductModel;
use app\admin\model\merchant\Product as MerProductModel;

/**
 * 商品接口
 */
class Product extends Api
{
    protected $noNeedLogin = ['index','search','recommend','detail'];
    protected $noNeedRight = ['*'];

    /**
     * 最近十条搜索关键字
     *
     * @ApiMethod (GET)
     */
    public function index(){
        $searchModel = new \app\api\model\UserSearch;
        $list = $searchModel->where(['user_id'=>$this->auth->id])->order('createtime DESC,num DESC')->limit(10)->select();
        $this->success(__('请求成功'),$list);
    }

    /**
     * 搜索
     *
     * @ApiMethod (GET)
     * @param int $category_id 分类ID
     * @param string $title 商品名称
     * @param int $page 页码
     * @param int $limit 每页数量
     */
    public function search(){
        $category_id = $this->request->get('category_id', 0);
        $title = $this->request->get('title', '');  //查询条件
        $page = $this->request->get('page',1);
        $limit = $this->request->get('limit',10);

        $where = ['product.switch'=>1,'goods.switch'=>1];
        if($category_id){
            $where['goods.category_id'] = $category_id;
        }

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
            ->whereOr($whereOr)
            ->order('sales DESC')
            ->page($page,$limit)
            ->select();

        foreach ($list as $row) {
            $row->getRelation('goods')->visible(['product_id','title','title_en','image','sales_price','cost_price','market_price','real_sales']);
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
     * 搜索---店铺
     *
     * @ApiMethod (GET)
     * @param int $category_id 分类ID
     * @param string $title 商品名称
     * @param int $page 页码
     * @param int $limit 每页数量
     */
    public function searchs(){
        $category_id = $this->request->get('category_id', 0);
        $title = $this->request->get('title', '');  //查询条件
        $page = $this->request->get('page',1);
        $limit = $this->request->get('limit',10);

        $where = ['status'=>1];

        $whereOr = [];
        if($title){
            $where['mer_name'] = ['like', '%'.$title.'%'];
            // $whereOr['goods.title_en'] = ['like', '%'.$title.'%'];
        }

        $merProductModel = new MerchantModel;

        $count = $merProductModel->where($where)->count();
        $list = $merProductModel
            ->where($where)
            // ->whereOr($whereOr)
            ->order('mer_id DESC')
            ->page($page,$limit)
            ->select();

        // foreach ($list as $row) {
        //     $row->getRelation('goods')->visible(['product_id','title','title_en','image','sales_price','cost_price','market_price','real_sales']);
        // }

        // if($title && $this->auth->id) {
        //     //存储最近查询
        //     $searchModel = new \app\api\model\UserSearch;
        //     //查询关键字是否搜索过
        //     $search = $searchModel->where(['user_id' => $this->auth->id, 'keyword' => $title])->find();
        //     if ($search) {
        //         $search->num += 1;
        //         $search->save();
        //     } else {
        //         $searchModel->user_id = $this->auth->id;
        //         $searchModel->keyword = $title;
        //         $searchModel->num = 1;
        //         $searchModel->save();
        //     }
        // }

        $data = [
            'total' => $count,
            'list' => $list
        ];
        $this->success(__('请求成功'),$data);
    }

    /**
     * 推荐商品列表
     *
     * @ApiMethod (GET)
     * @param int $category_id 分类ID
     * @param int $page 页码
     * @param int $limit 每页数量
     */
    public function recommend()
    {
        $category_id = $this->request->get('category_id', 0);
        $page = $this->request->get('page', 1);
        $limit = $this->request->get('limit', 10);

        $merProductModel = new \app\admin\model\merchant\Product;

        $where = ['product.switch'=>1,'goods.switch'=>1,'goods.is_recommend' => 1];
        if($category_id){
            $where['goods.category_id'] = $category_id;
        }

        $count = $merProductModel->with(['goods'])->where($where)->count();
        $list = $merProductModel->alias('product')
            ->field('id,mer_id,product_id,sales,click')
            ->with(['goods'])
            ->where($where)
            ->order('sales DESC')
            ->page($page, $limit)
            ->select();

        foreach ($list as $row) {
            $row->getRelation('goods')->visible(['product_id', 'title', 'title_en', 'image', 'sales_price', 'cost_price', 'market_price', 'is_recommend', 'real_sales']);
        }
        $data = [
            'total' => $count,
            'list' => $list
        ];
        $this->success(__('请求成功'), $data);
    }

    /**
     * 商品详情
     *
     * @ApiMethod (GET)
     * @param int $product_id 商品ID
     * @param int $mer_id 商户ID
     */
    public function detail(){
        $product_id = $this->request->get('product_id');
        $mer_id = $this->request->get('mer_id');
        if(!$product_id || !$mer_id){
            $this->error(__('参数错误'));
        }
        //$info = ProductModel::field('product_id,code,title,title_en,image,images,content,sales_price,cost_price,(sales_price - cost_price) AS profit,stock,look')->where(['product_id'=>$product_id])->find();
        $merProductModel = new MerProductModel;
        $productModel = new ProductModel;

        $info = $merProductModel->field('id,product_id,mer_id,sales,click')->where(['product_id'=>$product_id,'mer_id'=>$mer_id])->find();
        $product = $productModel->field('product_id,category_id,code,title,title_en,image,images,content,sales_price,market_price,stock,look,use_spec,specList,specTableList')->where(['product_id'=>$product_id])->find();
        $info['goods'] = $product;

        //评论列表
        $reply = new \app\admin\model\product\Reply;
        $reply_list = $reply->where(['product_id'=>$product_id,'mer_id'=>$mer_id])->order('createtime DESC')->select();
        $info['reply_list'] = $reply_list;

        //商户信息
        $merchantModel = new MerchantModel;
        $orderModel = new Order;
        $mer = $merchantModel->field('mer_id,mer_name,mer_avatar,follow_count,good_rate,visit')->where(['mer_id'=>$mer_id])->find();

        $total_price = $orderModel->where(['mer_id'=>$mer_id,'status'=>['in',[1,2,3,4]],'is_pick'=>1])->sum('total_price');
        $mer['total_price'] = number_format($total_price,2);
        $info['merchant'] = $mer;

        //判断是否已喜欢
        $likeModel = new \app\admin\model\UserLike;
        $like = $likeModel->where(['user_id'=>$this->auth->id,'product_id'=>$product_id,'mer_id'=>$mer_id,'status'=>1])->count();
        $info['is_like'] = $like ? 1 : 0;

        //该商户其它商品
        $other = $merProductModel->with(['goods'])->field('id,product.product_id,product.mer_id,product.sales,click')->where(['product.mer_id'=>$mer_id,'product.product_id'=>['<>',$product_id]])->where(['goods.category_id'=>$product['category_id']])->order('id DESC')->limit(10)->select();
        foreach ($other as &$row) {
            $row->getRelation('goods')->visible(['product_id','title','title_en','image','sales_price','market_price','stock','look']);
            //$row['goods'] = ProductModel::field('product_id,code,title,title_en,image,images,content,sales_price,cost_price,(sales_price - cost_price) AS profit,stock,look')->where(['product_id'=>$product_id])->find();
        }
        $info['other'] = $other;

        if($this->auth->id){
            //添加访问记录
            $visitModel = new \app\api\model\Visit;
            //查询今天是否访问过
            $visit = $visitModel->where(['user_id'=>$this->auth->id,'product_id'=>$product_id,'mer_id'=>$mer_id])->whereTime('createtime', 'today')->count();
            if($visit <= 0){
                $visitModel->user_id = $this->auth->id;
                $visitModel->product_id = $product_id;
                $visitModel->mer_id = $mer_id;
                $visitModel->save();

                $mer->visit += 1; //商户访问量+1
                $mer->allowField(true)->save();
            }
        }

        $this->success(__('请求成功'),$info);
    }

    /**
     * 喜欢商品/取消喜欢
     *
     * @ApiMethod (POST)
     * @param int $product_id 商品ID
     * @param int $mer_id 商户ID
     */
    public function like(){
        $product_id = $this->request->post('product_id');
        $mer_id = $this->request->post('mer_id');
        $user_id = $this->auth->id;
        $user = $this->auth->getUser();

        $likeModel = new \app\admin\model\UserLike;
        $where = ['user_id'=>$user_id,'product_id'=>$product_id,'mer_id'=>$mer_id];
        //判断是否已存在记录
        $like = $likeModel->where($where)->find();
        if($like){
            $like->delete();
            $user->like -= 1; //用户喜欢数-1
//            if($like['status'] == 1){
//                $like->status = 0;
//                $user->like -= 1; //用户喜欢数-1
//            }else{
//                $like->status = 1;
//                $user->like += 1; //用户喜欢数+1
//            }
//            $like->save();
        }else{
            $likeModel->user_id = $user_id;
            $likeModel->product_id = $product_id;
            $likeModel->mer_id = $mer_id;
            $likeModel->status = 1;
            $likeModel->save();

            $user->like += 1; //用户喜欢数+1
        }
        $user->save();

        $this->success(__('操作成功'));
    }
}
