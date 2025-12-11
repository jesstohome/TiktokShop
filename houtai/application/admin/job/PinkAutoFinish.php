<?php
/**
 * 文件路径： \application\admin\job\PinkAutoFinish.php
 */
namespace app\admin\job;

use app\merchant\model\order;
use app\admin\model\OrderRefundProduct;
use app\api\model\OrderRefund;
use app\api\model\stores\Pink;
use app\api\model\User;
use think\Db;
use think\Exception;
use think\Log;
use think\queue\Job;
use app\api\extend\Hashids;

class PinkAutoFinish {

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
                //$job->release(2); //$delay为延迟时间，表示该任务延迟2秒后再执行
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
        //$data['id'] = Hashids::decodeHex($data['order_id']);
        $orderNoArr = $data;
        $no = array_rand($orderNoArr);
        //被选中的订单
        $orderNoSuccess = $orderNoArr[$no];

        //$data['id']=$data['group_order_sn'];
        //$group_order_id = $data['group_order_id'];

        Log::info('【自动归档】执行begin，订单号【' . $orderNoSuccess . '】');
        # 查询订单信息
        $order = Order::where([
            'out_trade_no' => $orderNoSuccess
        ])
            ->lock(true)
            ->find();
        //团购信息
//        if(!empty($order->combination_id) && !empty($order->pink_id)){
//            $pinkModel = new Pink();
//            $pinkInfo = $pinkModel->where(['id'=>$order->pink_id])->find();
//            if($pinkInfo['k_id'] > 0){
//                $k_id = $pinkInfo['k_id'];
//                $pinkT = $pinkModel->where(['id'=>$k_id,'is_refund'=>0])->find(); //团长拼团信息
//            }else{
//                //$k_id = $pinkInfo['id'];
//            }
//
//            $count = $pinkModel->where(['k_id'=>$k_id,'is_refund'=>0])->count();
//        }
        Db::startTrans();
        try {
            if (!$order) {
                throw new Exception('【自动归档】未查询到该订单,订单号【' . $data['id'] . '】');
            }
            if ($order['have_finish'] > 0 ) {
                throw new Exception('【自动归档】订单已完结,订单号【' . $data['id'] . '】');
            }
            if ($order['order_status'] <> Order::STATUS_NORMAL) {
                throw new Exception('【自动归档】订单状态:'.Order::STATUS_NORMAL.'，订单号【' . $data['id'] . '】');
            }
            $order->have_finish = time();
            $order->save();
            //失败账号分佣，退款
            unset($orderNoArr[$no]);
            $orderNoFail = array_values($orderNoArr);
            foreach ($orderNoFail as $fail){
                $orderFail = Order::where(['out_trade_no' => $fail])->find();
                //分佣
                db('user')->where(['id'=>$orderFail['user_id']])->setInc('money',$order['one_brokerage']);
                $user = User::where(['id'=>$orderFail['user_id']])->find();
                $moneylog = array(
                    'user_id' => $user['id'],
                    'type'=> 'order',
                    'order_id'=> $fail,
                    'money' => '+'.$order['one_brokerage'],
                    'before' => $user['money'],
                    'after' => $user['money']+$order['one_brokerage'],
                    'memo' => '订单失败后分佣',
                    'createtime' => time()
                );
                db('user_money_log')->insertGetId($moneylog);
                //退款
                $orderRefund = new OrderRefund();
                $orderRefund->user_id = $orderFail['user_id'];
                $orderRefund->order_id = $orderFail['id'];
                $orderRefund->receiving_status = $orderFail['have_received'];
                $orderRefund->service_type = 0;
                $orderRefund->reason_type = '拼团失败';
                $orderRefund->amount = $order['money'];
                $orderRefund->refund_explain = '拼团失败全额退款';
                $orderRefund->save();

                $pink = Pink::where(['id'=>$orderFail['pink_id']])->find();

                $refundProduct = [];

                $refundProduct['order_product_id'] = $pink['pid'];
                $refundProduct['order_id'] = $orderFail['id'];
                $refundProduct['user_id'] = $orderFail['user_id'];
                $refundProduct['refund_id'] = $orderRefund['id'];
                $refundProduct['createtime'] = time();
                (new OrderRefundProduct)->insertAll($refundProduct);

                $orderFail->status = \app\api\model\Order::STATUS_REFUND;
                $orderFail->refund_status = \app\api\model\Order::REFUND_STATUS_APPLY;
                $order->save();
            }

            Db::commit();
        } catch (Exception $exception) {
            Db::rollback();
            Log::write('【自动归档】归档失败,'.$exception->getMessage());
            return false;
        }
        Log::info('【自动归档】执行end，订单号【' . $data['id'] . '】');
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