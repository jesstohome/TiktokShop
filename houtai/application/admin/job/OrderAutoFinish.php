<?php
/**
 * 文件路径： \application\admin\job\OrderAutoFinish.php
 */
namespace app\admin\job;

use app\admin\model\order\Order;
use think\Db;
use think\Exception;
use think\Log;
use think\queue\Job;

class OrderAutoFinish {

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
        $order = Order::where(['order_sn' => $data['order_sn']])->lock(true)->find();
        if($order->status == 4){ //已手动完成，删除任务
            return false;
        }
        return true;
    }

    /**
     * 根据消息中的数据进行实际的业务处理...
     * 订单自动收货
     */
    private function autoFinish($data)
    {
        //$data['id'] = Hashids::decodeHex($data['order_id']);
        $data['id']=$data['order_sn'];
        Log::info('【自动归档】执行begin，订单号【' . $data['id'] . '】');
        # 查询订单信息
        $order = Order::where([
            'order_sn' => $data['id']
        ])
            ->lock(true)
            ->find();
        Db::startTrans();
        try {
            if (!$order) {
                throw new Exception('【自动归档】未查询到该订单,订单号【' . $data['id'] . '】');
            }
            if ($order['status'] == 4) {
                throw new Exception('【自动归档】订单已完结,订单号【' . $data['id'] . '】');
            }
            if ($order['status'] == 2) {
                throw new Exception('【自动归档】订单还未确认收货,订单号【' . $data['id'] . '】');
            }
            if ($order['status'] == -1) {
                throw new Exception('【自动归档】订单已取消,订单号【' . $data['id'] . '】');
            }
            if ($order['refund_status'] == 1) {
                throw new Exception('【自动归档】订单还未完成售后,订单号【' . $data['id'] . '】');
            }
            if ($order['refund_status'] == 2 && $order['status'] == -2) {
                throw new Exception('【自动归档】订单还未完成售后,订单号【' . $data['id'] . '】');
            }

            $order->status = 4;
            $order->finish_time = time();
            $order->save();
            //分润到经销商
//            if (Config::getByName('sto_switch') && Order::STATUS_NORMAL){
//                $rebate=Config::getByName('sto_rebate');
//                db('user')->where(['id'=>$order['store_id']])->setInc('money',$order['money']*$rebate);
//                $moneylog = array(
//                    'user_id' => $order['store_id'],
//                    'type'=>'order',
//                    'order_id'=>$order['out_trade_no'],
//                    'money' => '+'.$order['money']*$rebate,
//                    'before' => $order['money'],
//                    'after' => $order['money']+$order['money']*$rebate,
//                    'memo' => '订单分润',
//                    'createtime' => time()
//                );
//                db('user_money_log')->insertGetId($moneylog);
//            }
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