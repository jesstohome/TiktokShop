<?php
/**
 * 文件路径： \application\admin\job\OrderAutoReceived.php
 */
namespace app\admin\job;

use app\admin\model\order\Order;
use think\Db;
use think\Exception;
use think\Log;
use think\queue\Job;

class OrderAutoReceived {

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

        $isJobDone = $this->autoReceived($data);

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
        if($order->status == 3){ //已手动收货，删除任务
            return false;
        }
        return true;
    }

    /**
     * 根据消息中的数据进行实际的业务处理...
     * 订单自动收货
     */
    private function autoReceived($data)
    {
        Log::info('【自动收货】执行begin，订单号【' . $data['order_sn'] . '】');
        # 查询订单信息
        $order = Order::where([
            'order_sn' => $data['order_sn']
        ])
            ->lock(true)
            ->find();
        Db::startTrans();
        try {
            if (!$order) {
                throw new Exception('【自动收货】未查询到该订单,订单号【' . $data['order_sn'] . '】');
            }
            $order->status = 3;
            $order->received_time = time();
            $order->verify_time = time();
            $order->save();

            //用户确认收货，商户增加余额(订单总金额 * 手续费)
            $merchantModel = new \app\merchant\model\merchant\Merchant;
            //$money = $order->total_price * (config('site.order_charge'));
            $merchantModel->money($order->total_price, $order->mer_id, $order->order_sn, 1, 'order', "订单确认收货增加余额".$order->total_price);

            Db::commit();
        } catch (Exception $exception) {
            Db::rollback();
            Log::write('【自动收货】确认收货失败,'.$exception->getMessage());
            return false;
        }
        Log::info('【自动收货】执行end，订单号【' . $data['order_sn'] . '】');
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