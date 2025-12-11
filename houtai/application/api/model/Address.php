<?php

namespace app\api\model;


use think\Model;

/**
 * 收货地址模型
 * Class Favorite
 * @package addons\unishop\model
 */
class Address extends Model
{
    // 表名
    protected $name = 'user_address';

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    // 是否为默认地址？
    const IS_DEFAULT_YES = 1; //是
    const IS_DEFAULT_NO = 0;  //否

    public function province()
    {
        return $this->belongsTo('area', 'province_id', 'id',[],'LEFT')->setEagerlyType(0);
    }

    public function city()
    {
        return $this->belongsTo('area', 'city_id', 'id',[],'LEFT')->setEagerlyType(0);
    }

    public function area()
    {
        return $this->belongsTo('area', 'area_id', 'id',[],'LEFT')->setEagerlyType(0);
    }

    /**
     * Notes:通过地址id拼接完整地理位置
     * Author: licj
     * DateTime: 2021/3/25 0025 15:36
     * function getAddresText
     * @package app\api\model
     */
    public function getAddresText($addressid){
        $address='';
        foreach (self::all(['address_id'=>$addressid],'province,city,area') as $address){
            $address=$address->province->data['name'].$address->city->data['name'].$address->area->data['name'].$address->data['address'];
        }
        return $address;
    }
    /**
     * Notes:通过地址id计算当前经纬度
     * Author: licj
     * DateTime: 2021/3/25 0025 14:20
     * function getLngLat
     * @package app\api\model
     */
    public function getLngLat($addressid){
        $apiWeb=Config::getByName('bdapi_ak')['value'];
        $result=\fast\Http::post($apiWeb.$this->getAddresText($addressid));
        $arrayRes=json_decode($result,true);
        $lghLat=[];
        if (isset($arrayRes['result']['location'])){
            $lghLat['lng']=$arrayRes['result']['location']['lng'];
            $lghLat['lat']=$arrayRes['result']['location']['lat'];
        }
        return $lghLat;
    }

    static public function getValue($id,$field)
    {
        $row = (new self)->get($id,[],true);
        if ($row) {
            return $row->getAttr($field) ?? '';
        }
        return false;
    }
}
