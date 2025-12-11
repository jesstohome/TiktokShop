<?php

namespace app\api\controller;

use app\common\controller\Api;
use app\api\model\Product as ProductModel;
use app\admin\model\merchant\Product as MerProductModel;

/**
 * 商户接口
 */
class Merchant extends Api
{
    protected $noNeedLogin = ['detail'];
    protected $noNeedRight = ['*'];

    /**
     * 商品详情
     *
     * @ApiMethod (GET)
     * @param int $mer_id 商户ID
     * @param int $page 页码
     * @param int $limit 每页数量
     */
    public function detail(){
        $mer_id = $this->request->get('mer_id');
        $category_id = $this->request->get('category_id', 0);
        // $title = $this->request->get('title');
        $page = $this->request->get('page',1);
        $limit = $this->request->get('limit',10);
        if(!$mer_id){
            $this->error(__('参数错误'));
        }

        $model = new \app\api\model\Merchant;

        $info = $model->field('mer_id,mer_name,mer_avatar,mer_level,follow_count,visit,grade,credit,good_rate,banner,mer_level')->where(['mer_id'=>$mer_id])->find();
        //商户等级
        $levelModel = new \app\admin\model\merchant\Level;
        $info['level'] = $levelModel->field('level_id,name,image')->where(['level_id'=>$info['mer_level']])->find();

        //商户商品
        $where = ['product.mer_id'=>$mer_id,'product.switch'=>1,'goods.switch'=>1];
        // $whereOr = [];
        // if($title){
        //     $where['title'] = ['like', '%'.$title.'%'];
        //     $whereOr['title_en'] = ['like', '%'.$title.'%'];
        // }
        if($category_id){
            $where['goods.category_id'] = $category_id;
        }
        $merProductModel = new MerProductModel;
        $count = $merProductModel->with(['goods'])->where($where)->count();
        $productList = $merProductModel->alias('product')
            ->field('id,mer_id,product_id,sales,click')
            ->with(['goods'])
            ->where($where)
            ->order('sales DESC')
            ->page($page,$limit)
            ->select();

        foreach ($productList as $row) {
            $row->getRelation('goods')->visible(['product_id','title','title_en','image','sales_price','cost_price','market_price','real_sales']);
        }
        $info['product_list'] = [
            'list'=>$productList,
            'count'=>$count
        ];
        //判断是否已关注
        $followModel = new \app\admin\model\UserFollow;
        $follow = $followModel->where(['user_id'=>$this->auth->id,'mer_id'=>$mer_id,'status'=>1])->count();
        $info['is_follow'] = $follow ? 1 : 0;

        if($this->auth->id) {
            //添加访问记录
            $visitModel = new \app\api\model\Visit;
            //查询今天是否访问过
            $visit = $visitModel->where(['user_id' => $this->auth->id, 'mer_id' => $mer_id])->whereTime('createtime', 'today')->count();
            if ($visit <= 0) {
                $visitModel->user_id = $this->auth->id;
                $visitModel->mer_id = $mer_id;
                $visitModel->save();

                $info->visit += 1; //商户访问量+1
                $info->allowField(true)->save();
            }
        }

        $this->success(__('请求成功'),$info);
    }

    /**
     * 关注商户/取消关注
     *
     * @ApiMethod (POST)
     * @param int $mer_id 商户ID
     */
    public function follow(){
        $mer_id = $this->request->post('mer_id');
        $user_id = $this->auth->id;

        if(!$mer_id){
            $this->error(__('参数错误'));
        }
        $user = $this->auth->getUser();
        $mer = \app\api\model\Merchant::where(['mer_id'=>$mer_id])->find();

        $followModel = new \app\admin\model\UserFollow;
        $where = ['user_id'=>$user_id,'mer_id'=>$mer_id];
        //判断是否已存在记录
        $follow = $followModel->where($where)->find();
        if($follow){
            $follow->delete();
            $user->follow -= 1; //用户关注数-1
            $mer->follow_count -= 1; //商户关注数-1
//            if($follow['status'] == 1){
//                $follow->status = 0;
//                $user->follow -= 1; //用户关注数-1
//                $mer->follow_count -= 1; //商户关注数-1
//            }else{
//                $follow->status = 1;
//                $user->follow += 1;  //用户关注数+1
//                $mer->follow_count += 1; //商户关注数+1
//            }
//            $follow->save();
        }else{
            $followModel->user_id = $user_id;
            $followModel->mer_id = $mer_id;
            $followModel->status = 1;
            $followModel->save();
            $user->follow += 1;  //用户关注数+1
            $mer->follow_count += 1; //商户关注用户+1
        }
        $user->save();
        $mer->save();
        $this->success(__('操作成功'));
    }
}
