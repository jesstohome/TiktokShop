<?php

namespace app\merchant\model\merchant;

use app\common\model\MoneyLog;
use app\merchant\model\merchant\Bill as BillModel;
use think\Db;
use think\Model;


class Merchant extends Model
{

    // 表名
    protected $name = 'merchant';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];

    /**
     * 变更商户余额
     * @param int    $money   余额
     * @param int    $mer_id  商户ID
     * @param int    $link_id  关联ID
     * @param int    $pm      支出/收入
     * @param string $type    账单类型
     * @param string $memo    备注
     */
    public static function money($money, $mer_id, $link_id, $pm, $type, $memo)
    {
        $res = false;
        Db::startTrans();
        try {
            $mer = self::lock(true)->find(['mer_id' => $mer_id]);
            if ($mer && $money != 0) {
                $before = $mer->mer_money;
                //$after = $mer->money + $money;
                if($pm == 1){
                    $after = function_exists('bcadd') ? bcadd($mer->mer_money, $money, 2) : $mer->mer_money + $money;
                }else{
                    $after = function_exists('bcsub') ? bcsub($mer->mer_money, $money, 2) : $mer->mer_money - $money;
                }
                //更新会员信息
                $mer->save(['mer_money' => $after]);
                //写入
                $res = BillModel::create(['mer_id' => $mer_id,'link_id'=>$link_id, 'money' => $money, 'pm' => $pm, 'before' => $before, 'after' => $after,'type' => $type, 'memo' => $memo]);
            }
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            throw new \Exception($e->getMessage());
        }
        return $res;
    }

    /**
     * 判断商户名是否已存在
     * @param string $mer_name 商户名
     * @return boolean
     */
    public static function getByMername($mer_name)
    {
        $count = self::where(['mer_name' => $mer_name])->count();

        return $count > 0 ? true : false;
    }

    /**
     * 判断商户邮箱是否已存在
     * @param string $mer_email 商户邮箱
     * @return boolean
     */
    public static function getByEmail($mer_email)
    {
        $count = self::where(['mer_email' => $mer_email])->count();

        return $count > 0 ? true : false;
    }

    /**
     * 判断商户手机号是否已存在
     * @param string $mer_phone 商户手机号
     * @return boolean
     */
    public static function getByPhone($mer_phone)
    {
        $count = self::where(['mer_phone' => $mer_phone])->count();

        return $count > 0 ? true : false;
    }

    /**
     * 总销售额
     * @param int $mer_id 商户ID
     * @return float
     */
    public static function getTotalSales($mer_id)
    {

        $count = self::where(['mer_id' => $mer_id])->sum('total_price');

        return $count > 0 ? true : false;
    }

    /**
     * 格式化时间
     * @param $value
     * @return false|string
     */
    public function getCreatetimeAttr($value)
    {
        return date('Y-m-d H:i:s', $value);
    }

    /**
     * 处理图片
     * @param $value
     * @return string
     */
    public function getMerAvatarAttr($value) {
        return cdnurl($value,true);
    }
}
