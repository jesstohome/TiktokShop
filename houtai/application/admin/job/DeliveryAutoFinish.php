<?php
/**
 * 文件路径： \application\admin\job\OrderAutoFinish.php
 */
namespace app\admin\job;

use app\admin\model\order\Delivery as OrderDelivery;
use app\admin\model\order\Order;
use think\Db;
use think\Exception;
use think\Log;
use think\queue\Job;

class DeliveryAutoFinish {

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
        $id = $data['id'];
        Log::info('自动执行begin，物流单号【' . $data['delivery_no'] . '】');
        # 查询订单信息
        $info = OrderDelivery::where([
            'id' => $id
        ])->lock(true)->find();
        Db::startTrans();
        try {
            $info->status = 2;
            $info->save();

            //检测是否全部执行
            $count = OrderDelivery::where(['delivery_no' => $data['delivery_no'],'status'=>1])->count();
            if($count == 0){ //执行完成更新订单物流状态
                $order_id = $data['order_id'];
                $order = Order::where(['order_id' => $order_id])->find();
                $order->delivery_status = 4;
                $order->save();

                OrderDelivery::where(['delivery_no' => $data['delivery_no']])->update(['status'=>3]);  //最后一步左右已执行物流信息状态改为已完成
            }

            Db::commit();
        } catch (Exception $exception) {
            Db::rollback();
            return false;
        }
        Log::info('自动执行end，物流单号【' . $data['delivery_no'] . '】');
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