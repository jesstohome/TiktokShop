<?php

namespace app\api\model;


use think\Model;

/**
 * Class Cart 购物车
 * @package app\api\model
 */
class Cart extends Model
{
    // 表名
    protected $name = 'cart';

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;

    /**
     * 关联商品
     */
    public function product(){
        return $this->hasOne('product', 'product_id', 'product_id');
    }

    /**
     * 关联商户
     */
    public function merchant(){
        return $this->hasOne('merchant', 'mer_id', 'mer_id');
    }

    /**
     *  根据购物车id 获取对应的
     *
     * @param  $cart_ids 1，2，3
     * @return void
     */
    public function getCartGoods($cart_ids)
    {
        $carts = $this->where('cart_id','in',$cart_ids)->select();
        $mer_ids = [];
        $product_ids = [];
        $numbers = [];
        $specs = [];
        foreach ($carts as $key => $cart) {
            $mer_ids[] = $cart['mer_id'];
            $product_ids[] = $cart['product_id'];
            $numbers[] = $cart['cart_num'];
            if($cart['spec']){
                $spec = str_replace(',', '|', $cart['spec']);
                $specs[] = $spec;
            }else{
                $specs[] = $cart['spec'];
            }
        }
        $data = [
            'mer_id' => implode(',',$mer_ids),
            'product_id' => implode(',',$product_ids),
            'number' => implode(',',$numbers),
            'spec' => implode(',',$specs)
        ];
        return $data;
    }
}
