<?php

namespace app\api\model;


use think\Model;
use think\Cache;

/**
 * 收货地址模型
 * Class Favorite
 * @package app\api\model
 */
class Area extends Model
{
    // 表名
    protected $name = 'area';

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;

    /**
     * 获取所有地区(树状结构)
     * @return unied
     */
    public static function getCacheTree()
    {
        return self::regionCache()['tree'];
    }

    /**
     * 获取所有地区
     * @return unied
     */
    public static function getCacheAll()
    {
        return self::regionCache()['all'];
    }
    /**
     * 获取地区缓存
     * @return unied
     */
    private static function regionCache()
    {
        if (!Cache::get('area')) {
            // 所有地区
            $all = $allData = self::useGlobalScope(false)->column('id, pid, name, level', 'id');
            // 格式化
            $tree = [];
            foreach ($allData as $pKey => $province) {
                if ($province['level'] === 1) {    // 省份
                    $tree[$province['id']] = $province;
                    unset($allData[$pKey]);
                    foreach ($allData as $cKey => $city) {
                        if ($city['level'] === 2 && $city['pid'] === $province['id']) {    // 城市
                            $tree[$province['id']]['city'][$city['id']] = $city;
                            unset($allData[$cKey]);
                            foreach ($allData as $rKey => $region) {
                                if ($region['level'] === 3 && $region['pid'] === $city['id']) {    // 地区
                                    $tree[$province['id']]['city'][$city['id']]['region'][$region['id']] = $region;
                                    unset($allData[$rKey]);
                                }
                            }
                        }
                    }
                }
            }
            Cache::set('area', compact('all', 'tree'));
        }
        return Cache::get('area');
    }
}
