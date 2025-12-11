<?php

namespace app\merchant\controller;

use app\common\controller\Mer;
use app\merchant\model\merchant\Bill;
use app\merchant\model\merchant\Merchant as MerchantModel;
use app\merchant\model\order\Order as OrderModel;
use app\merchant\model\merchant\Product as MerProductModel;
use app\merchant\model\merchant\Visit as VisitModel;
use app\merchant\model\merchant\Notice as NoticeModel;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;
use think\Queue;

/**
 * 首页接口
 */
class Index extends Mer
{
    protected $noNeedLogin = [];
    protected $noNeedRight = ['*'];

    /**
     * 首页  统计
     *
     */
    public function index()
    {
        //商户信息
        $merInfo = $this->auth->getMerchant();

        //总销售额
        $totalSales = OrderModel::where(['mer_id' => $merInfo->mer_id])->where('status','IN',[1,2,3,4])->where('is_pick',1)->sum('total_price');
        $totalSales = bcadd($totalSales, 0,2);
        //总利润 (总销售-总成本)
        // $totalCost = OrderModel::where(['mer_id' => $merInfo->mer_id])->where('status','IN',[1,2,3,4])->where('is_pick',1)->sum('total_cost'); //总成本
        $totalProfit = OrderModel::where(['mer_id' => $merInfo->mer_id])->where('status','IN',[1,2,3,4])->where('is_pick',1)->sum('total_profit'); //总利润
        // $totalProfit = bcsub($totalSales, $totalCost,2); //总利润

        //今日访客数
        // $todayVisit = VisitModel::where(['mer_id' => $merInfo->mer_id])->whereTime('createtime', 'today')->count();
        $todayVisit = $merInfo->today_visit;
        //七日访客数
        // $sevenVisit = VisitModel::where(['mer_id' => $merInfo->mer_id])->whereTime('createtime', '-7days')->count();
        $sevenVisit = $merInfo->seven_visit;
        //30日访客数
        // $thirtyVisit = VisitModel::where(['mer_id' => $merInfo->mer_id])->whereTime('createtime', '-30days')->count();
        $thirtyVisit = $merInfo->thirty_visit;

        //今日售出订单
        $todayOrder = OrderModel::where(['mer_id' => $merInfo->mer_id])->where('status','IN',[0,1,2,3,4])->whereTime('createtime', 'today')->count();

        //今日总销售额
        $todaySales = OrderModel::where(['mer_id' => $merInfo->mer_id])->where('status','IN',[1,2,3,4])->where('is_pick',1)->whereTime('createtime', 'today')->sum('total_price');
        $todaySales = bcadd($todaySales, 0,2);

        //今日预计利润 (今日销售-今日成本)
        // $todayCost = OrderModel::where(['mer_id' => $merInfo->mer_id])->where('status','IN',[1,2,3,4])->where('is_pick',1)->whereTime('createtime', 'today')->sum('total_cost'); //今日成本
        $todayProfit = OrderModel::where(['mer_id' => $merInfo->mer_id])->where('status','IN',[1,2,3,4])->where('is_pick',1)->whereTime('createtime', 'today')->sum('total_profit'); //今日成本
        // $todayProfit = bcsub($todaySales, $todayCost,2); //今日利润

        $data = [
            'total_sales' => $totalSales, //总销售额
            'total_profit' => $totalProfit, //总利润
            'today_visit' => $todayVisit, //今日访客数
            'seven_visit' => $sevenVisit, //七日访客数
            'thirty_visit' => $thirtyVisit, //30日访客数
            'today_order' => $todayOrder, //今日售出订单
            'today_sales' => $todaySales, //今日总销售额
            'today_profit' => $todayProfit, //今日预计利润
        ];

        $this->success(__('请求成功'), $data);
    }

    /**
     * 首页  消息通知
     *
     * @ApiMethod (GET)
     * @param int $is_see 查看状态
     * @param int $page 页码
     * @param int $limit 每页数量
     */
    public function notice()
    {
        $is_see = $this->request->get('is_see', 'all'); //查看状态
        $page = $this->request->get('page', 1); //页码
        $limit = $this->request->get('limit', 20); //每页数量

        $where = ['mer_id' => $this->auth->mer_id];
        if($is_see != 'all'){
            $where['is_see'] = $is_see;
        }

        $count = NoticeModel::where($where)->count();
        $list = NoticeModel::where($where)->page($page, $limit)->order('id DESC')->select();

        $data = [
            'total' => $count,
            'list' => $list
        ];
        $this->success(__('请求成功'),$data);
    }

    /**
     * 首页  消息通知详情
     *
     * @ApiMethod (GET)
     * @param int $id
     */
    public function notice_detail(){
        $id = $this->request->get('id'); //id

        $where = ['id' => $id];
        $info = NoticeModel::where($where)->find();
        if(!$info){
            $this->error('未找到该消息');
        }

        $this->success(__('请求成功'),$info);
    }

    /**
     * 首页  查看消息
     *
     * @ApiMethod (POST)
     * @param string $ids 通知消息id(多个用逗号隔开,all表示全部已读)
     */
    public function see_notice()
    {
        $ids = $this->request->post('ids'); //通知消息id
        if(empty($ids)){
            $this->error(__('参数错误'));
        }

        // if($ids == 'all'){ //全部已读
        //     $where['mer_id'] = $this->auth->mer_id;
        //
        // }else{
        //     $where['id'] = ['IN',$ids];
        // }
        //
        // $res = NoticeModel::where($where)->update(['is_see' => 1,'see_time' => time()]);
        // if($res){
        //     $this->success('操作成功');
        // }else{
        //     $this->error('操作失败');
        // }

        $where = ['id' => $ids];
        $info = NoticeModel::where($where)->find();
        if(!$info){
            $this->error(__('通知不存在'));
        }
        $info->is_see = 1;
        $info->see_time = time();
        $info->save();

        $this->success(__('请求成功'),$info);
    }

    /**
     * 图表信息
     *
     * @ApiMethod (GET)
     * @param string $range 时间范围(today=今天,seven=近七天,thirty=近三十天天)
     */
    public function echart(){
        $range = $this->request->get('range', 'today'); //时间范围
        if($range == 'today'){
            $startDate = strtotime(date('Y-m-d'));
            $endDate = time();
        }else if($range == 'seven') {
            $startDate = strtotime('-7days');
            $endDate = time();
        }else if($range == 'thirty') {
            $startDate = strtotime('-30days');
            $endDate = time();
        }

        $data = [];
        $x = [];
        $y = [];
        for($date = $startDate; $date <= $endDate; $date = strtotime(date('Y-m-d',$date) . ' +1 day')) {
            $where['createtime'] = ['between', [$date, $date+86400-1]];
            $orderCount = OrderModel::where(['mer_id' => $this->auth->mer_id])->where($where)->count(); //当天销售量
            $visitCount = VisitModel::where(['mer_id' => $this->auth->mer_id])->where($where)->count(); //当天浏览量

            $key = date('Y-m-d',$date);
            $x[] = $key;
            $y[] = $orderCount;
//            $data[] = [
//                'date' => $key,
//                'order_count' => $orderCount,
//                'visit_count' => $visitCount
//            ];
        }

        $data = [
            'x' => $x,
            'y' => $y
        ];

        $this->success(__('请求成功'),$data);
    }

    /**
     * 商户等级
     *
     * @ApiMethod (GET)
     */
    public function level(){
        $model = new \app\merchant\model\merchant\Level;

        $list = $model->where(['status'=>1])->order('level_id DESC')->select();
        $this->success(__('请求成功'),$list);
    }

    /**
     * 商户直通车
     *
     * @ApiMethod (GET)
     */
    public function train(){
        $model = new \app\merchant\model\merchant\Train;

        $list = $model->order('price DESC')->select();
        $this->success(__('请求成功'),$list);
    }

    /**
     * 购买直通车
     *
     * @ApiMethod (POST)
     * @param int $id id
     */
    public function buy_train(){
        $id = $this->request->post('id'); //id

        $model = new \app\admin\model\merchant\Train;

        $train = $model->where('id',$id)->find();
        if(!$train){
            $this->error(__('未找到该直通车'));
        }
        //$mer = $this->auth->getMerinfo();
        $mer_id = $this->auth->mer_id;

        //判断余额是否充足
        if($train->price > $this->auth->mer_money){
            $this->error(__('余额不足，请先充值'));
        }

        //获取商户橱窗商品随机取1
        $list = MerProductModel::where(['mer_id'=>$mer_id])->select();
        if(!$list){
            $this->error(__('商户未铺货任何商品，无法自动下单'));
        }
        $key = array_rand($list,1);
        $product_id = $list[$key]['product_id'];

        // 生成扣款记录
        $MerchantModel = new MerchantModel;
        $res = $MerchantModel->money($train['price'], $mer_id, $id, 0, 'train', 'Buy the through train for '.$train['price']);
        if($res == false){
            $this->error(__('购买失败'));
        }

        $hour = $train['hour']; //小时
        $order_num = $train['order_num']; //订单数

        $start_time = time();
        //创建自动下单记录
        $params = [
            'mer_id' => $mer_id,
            'user_id' => 1,
            'product_id' => $product_id,
            'num' => $order_num,
            'start_time' => $start_time,
            'status' => 1
        ];
        $result = false;
        Db::startTrans();
        try {
            $autoModel = new \app\admin\model\order\Auto;
            //是否采用模型验证
            $result = $autoModel->allowField(true)->save($params);

            //添加队列
            if($result) {
                $second = bcmul($hour, 3600, 0);  //秒数
                // $second_interval = bcdiv($second, $order_num,0);
                // for($i=0;$i<$order_num;$i++){
                //     $delay = $second_interval * $i;
                //     $delay = min($delay, $second);
                    $delay = bcdiv($second, 2,0);
                    $data = [
                        'id' => $autoModel->id,
                        'mer_id' => $params['mer_id'],
                        'user_id' => $params['user_id'],
                        'product_id' => $params['product_id'],
                        'num' => $order_num,
                        // 'step' => $i+1,
                        // 'order_num' => $order_num,
                    ];
                    Queue::later($delay, 'app\admin\job\OrderAutoCreate', $data, 'OrderAutoCreate');
                // }
            }

            Db::commit();
        } catch (ValidateException|PDOException|Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        if ($result === false) {
            $this->error(__('Purchase failed'));
        }

        $this->success(__('购买成功'));
    }

    /**
     * 购买直通车记录
     *
     * @ApiMethod (GET)
     *
     * @param string $type 类型(all=全部，recharg=充值,extract=提现,order=订单,train=直通车)
     * @param int $page 页码
     * @param int $limit 每页数量
     */
    public function buy_train_bill()
    {
        $type = $this->request->get('type','train');
        $page = $this->request->get('page',1);
        $limit = $this->request->get('limit',20);

        $where = ['mer_id' => $this->auth->mer_id,'type' => $type];

        $count = Bill::where($where)->count();
        $billList = Bill::where($where)->order('id DESC')->page($page,$limit)->select();

        $trainModel = new \app\admin\model\merchant\Train;
        foreach ($billList as &$row){
            if($row['type'] == 'train'){
                $row['train'] = $trainModel->where('id',$row['link_id'])->find();
            }else{
                $row['train'] = [];
            }
        }

        $this->success(__('直通车购买记录'),['count'=>$count,'list'=>$billList]);
    }

    /**
     * 创业联盟
     *
     * @ApiMethod (GET)
     */
    public function coalition(){
        $mer = $this->auth->getMerchant();

        $code = $mer->code;
        $url = config('site.domain')."/login?mer=".$code;

        //下级商户
        $where['spread_id'] = $mer->mer_id;

        $list = MerchantModel::field('mer_id,mer_name,spread_id,createtime')->where($where)->select();
        foreach ($list as $k => &$v){
            $orderCount = OrderModel::where(['mer_id' => $v->mer_id])->where('status','IN',[3,4])->count();
            $v['order_count'] = $orderCount;

            $income = Bill::where(['mer_id' => $v->mer_id,'pm'=>1,'type'=>'order'])->sum('money');
            $v['income'] = $income;
        }

        $data = [
            'url' => $url,
            'code' => $code,
            'list' => $list
        ];
        $this->success(__('请求成功'),$data);
    }
}
