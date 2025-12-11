<?php
/**
 * 文件路径： \application\admin\job\OrderAutoFinish.php
 */
namespace app\admin\job;

use app\api\model\Order;
use app\api\model\Product;
use app\api\model\OrderProduct;
use think\Db;
use think\Exception;
use think\Log;
use think\queue\Job;

class OrderAutoCreate {

    /**
     * fire方法是消息队列默认调用的方法
     * @param Job            $job      当前的任务对象
     * @param array|mixed    $data     发布任务时自定义的数据
     */
    public function fire(Job $job,$data)
    {
        // 有些消息在到达消费者时,可能已经不再需要执行了
        $isJobStillNeedToBeDone = $this->checkDatabaseToSeeIfJobNeedToBeDone($data);
        if(!$isJobStillNeedToBeDone){
            $job->delete();
            return;
        }

        $isJobDone = $this->autoFinish($data);

        if ($isJobDone) {
            // 如果任务执行成功， 记得删除任务
            $job->delete();
            //print("<info>Hello Job has been done and deleted"."</info>\n");
        }else{
            if ($job->attempts() > 3) {
                //通过这个方法可以检查这个任务已经重试了几次了
                //print("<warn>Hello Job has been retried more than 3 times!"."</warn>\n");

                $job->delete();

                // 也可以重新发布这个任务
                //print("<info>Hello Job will be availabe again after 2s."."</info>\n");
//                $job->release(2); //$delay为延迟时间，表示该任务延迟2秒后再执行
            }
        }
    }

    /**
     * 有些消息在到达消费者时,可能已经不再需要执行了
     * @param array|mixed    $data     发布任务时自定义的数据
     * @return boolean                 任务执行的结果
     */
    private function checkDatabaseToSeeIfJobNeedToBeDone($data){
        return true;
    }

    /**
     * 根据消息中的数据进行实际的业务处理...
     * 订单自动收货
     */
    private function autoFinish($data)
    {
        $auto = \app\admin\model\order\Auto::get($data['id']);
        if(!isset($data['step'])){  //后台自动下单
            $auto->status = 1;
            $auto->save();
        }else{
            if($data['step'] == 1){  //购买直通车自动下单
                $auto->status = 1;
                $auto->save();
            }
        }

        $productModel = new \app\api\model\Product;

        $mer_id = $data['mer_id'];
        $user_id = $data['user_id'];
        $user = \app\common\model\User::get($user_id);
        $product_ids = $data['product_id'];
        $auto_num = $data['num'];
        //Log::info('自动执行begin，订单号【' . $data['delivery_no'] . '】');
        # 待创建订单信息
        $commission_ratio = config('site.commission_ratio');
        $brokerage = config('site.brokerage');
        $products = $productModel->where('product_id', 'in',$product_ids)->select();
        $total_price = 0;
        $total_profit = 0;
        $total_cost = 0;
        $extension_one_all = 0;
        foreach ($products as $pro){
            $sales_price = $pro['sales_price'];
            $sales_profit = number_format($sales_price * $commission_ratio,2);
            $sales_cost = $sales_price - $sales_profit;
            $extension_one_sales = $sales_profit * $brokerage;

            $total_price += $sales_price * $auto_num;
            $total_profit += $sales_profit * $auto_num;
            $total_cost += $sales_cost * $auto_num ;
            $extension_one_all += $extension_one_sales * $auto_num;
        }
        //商户利润
        $total_price = bcmul($total_price,1,2);  //总金额
        $total_profit = bcmul($total_profit,1,2); //总利润
        $total_cost = bcmul($total_cost,1,2); //总成本
        // 一级佣金
        $extension_one_all = bcmul($extension_one_all, 1, 2);
        Db::startTrans();
        try {
            // for($i=1;$i<=$auto_num;$i++) {
                $orderModel = new \app\api\model\Order;
                $order_sn = $orderModel->getOrderTradeNo();
                
               $addr =  Db::name('addr')->orderRaw('rand()')->limit(1)->find(); 
                $mer_order = [
                    'user_id' => $user_id, // 用户id
                    'mer_id' => $mer_id, // 商户id
                    'group_order_id' => '', // 总订单id
                    'order_sn' => $order_sn, // 商户订单号
                    'total_postage' => 0.00, // 运费
                    'total_num' => $auto_num, // 商品总数
                    'total_price' => $total_price, //  订单总金额
                    'total_cost' => $total_cost, //  订单总成本
                    'total_profit' => $total_profit, //  订单总利润
                    'pay_price' => $total_price, //  实际支付金额
                    'extension_one' => $extension_one_all, //  一级佣金
                    // 'real_name' => $user['real_name'], // 收货人
                    'real_name' => $addr['name'], // 收货人
                    // 'user_phone' => $user['user_phone'], // 收货人电话
                    'user_phone' => $addr['phone'], // 收货人电话
                    // 'user_address' => $user['user_address'], // 详细地址
                    'user_address' => $addr['addr'].' '.$addr['gj'], // 详细地址
                    'address_id' => 0, // 地址id
                    'remark' => '',
                    'status' => 1, //直接已支付
                    'paid' => 1,
                    'pay_time' => time(), // 支付时间
                    'is_virtual' => 1  //虚拟订单
                ];
                $orderModel->allowField(true)->save($mer_order);
                $order_id = $orderModel->order_id;
                $orderProduct = [];
                foreach ($products as $p){
                    $profit = number_format($p['sales_price'] * $commission_ratio,2);
                    $cost = $p['sales_price'] - $profit;
                    $extension_one = bcmul($profit, $brokerage, 2);
                    $orderProduct[] = [
                        'user_id' => $user_id,
                        'order_id' => $order_id,
                        'product_id' => $p['product_id'],
                        'product_num' => $auto_num,
                        'spec' => '',
                        'extension_one' => $extension_one,
                        'cost' => $cost,
                        'product_price' => $p['sales_price'],
                        'profit' => $profit ,
                        'total_price' => bcmul($p['sales_price'] , $auto_num,2),
                    ];
                }
                $orderProductModel = new \app\api\model\OrderProduct;
                $orderProductModel->allowField(true)->saveAll($orderProduct);
                // var_dump($i);
            // }
            if(!isset($data['step'])){
                $auto->status = 2;
                $auto->save();
            }else{
                if(isset($data['order_num']) && $data['order_num'] == $data['step']){
                    $auto->status = 2;
                    $auto->save();
                }
            }

            Db::commit();
        } catch (Exception $exception) {
            Db::rollback();
            echo $exception->getMessage();
            return false;
        }
        //Log::info('自动执行end，订单号【' . $data['delivery_no'] . '】');
        return true;
    }
    /**
     * 该方法用于接收任务执行失败的通知，你可以发送邮件给相应的负责人员
     * @param $jobData  string|array|...      //发布任务时传递的 jobData 数据
     */
    public function failed($jobData){
        //send_mail_to_somebody() ;

        print("Warning: Job failed after max retries. job data is :".var_export($jobData,true)."\n");
    }
}