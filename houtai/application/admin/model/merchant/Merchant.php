<?php

namespace app\admin\model\merchant;

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
        'status_text',
    ];

    public function getStatusList()
    {
        return ['0' => __('Status 0'), '1' => __('Status 1')];
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function type()
    {
        return $this->belongsTo('Type', 'type_id', 'mer_type_id', [], 'LEFT')->setEagerlyType(0);
    }

    public function level()
    {
        return $this->belongsTo('Level', 'mer_level', 'level_id', [], 'LEFT')->setEagerlyType(0);
    }

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
                \app\admin\model\merchant\Bill::create(['mer_id' => $mer_id,'link_id'=>$link_id, 'money' => $money, 'pm' => $pm, 'before' => $before, 'after' => $after,'type' => $type, 'memo' => $memo]);
            }
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            throw new \Exception($e->getMessage());
        }
    }
}
