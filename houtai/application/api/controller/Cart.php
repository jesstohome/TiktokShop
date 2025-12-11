<?php

namespace app\api\controller;

use app\common\controller\Api;
use app\api\model\Cart as CartModel;
use app\api\model\Product as ProductModel;
use app\admin\model\merchant\Merchant as MerchantModel;
use app\admin\model\merchant\Product as MerProductModel;

/**
 * 购物车接口
 */
class Cart extends Api
{
    protected $noNeedLogin = [];
    protected $noNeedRight = ['*'];

    /**
     * 获取购物车列表
     *
     */
    public function indexs()
    {
        $user_id = $this->auth->id;
        $mer_ids = CartModel::field('mer_id')->where('user_id', $user_id)->group('mer_id')->select();
        $carts = CartModel::where('user_id', $user_id)->select();

        $data = [];

        $mer_ids = array_column($mer_ids, 'mer_id');

        foreach ($mer_ids as $k => $v){
            $mer = MerchantModel::field('mer_id,mer_name')->where('mer_id', $v)->find(); //商户信息
            $mer_id = $mer ? $mer['mer_id'] : 0;
            $mer_name = $mer ? $mer['mer_name'] : '平台自营';
            $product = [];
            foreach ($carts as $c) {
                if ($c['mer_id'] == $v) {
                    $goods['cart_id'] = $c['cart_id'];
                    $goods = ProductModel::field('product_id,title,title_en,image,sales_price')->where(['product_id' => $c['product_id']])->find();
                    $goods['cart_num'] = $c['cart_num'];
                    $goods['spec'] = $c['spec'];
                    $goods['is_pay'] = $c['is_pay'];
                    $goods['is_fail'] = $c['is_fail'];
                    $product[] = $goods;
                }
            }

            $data[] = [
                'mer_id' => $mer_id,
                'mer_name' => $mer_name,
                'children' => $product
            ];
        }

        return $this->success(__('请求成功'), $data);
    }

    /**
     * 获取购物车列表
     *
     */
    public function index()
    {
        $user_id = $this->auth->id;
        $carts = CartModel::field('cart_id,mer_id,product_id,spec,cart_num,is_fail')->where('user_id', $user_id)->select();

        $total_price = 0;
        foreach ($carts as $c) {
            $c['goods'] = ProductModel::field('product_id,title,title_en,image,sales_price')->where(['product_id' => $c['product_id']])->find();
            $c['total_price'] = bcmul($c['goods']['sales_price'] , $c['cart_num'],2);
            $total_price = bcadd($total_price , $c['total_price'],2);
        }

        return $this->success(__('请求成功'), ['carts' => $carts, 'total_price' => $total_price]);
    }

    /**
     * 购物车删除
     *
     * @ApiMethod (POST)
     * @param string $cart_ids 购物车id(多个用,分隔)
     */
    public function del()
    {
        $cart_ids = $this->request->post('cart_ids');
        $user_id = $this->auth->id;

        CartModel::where('user_id', $user_id)->whereIn('cart_id', $cart_ids)->delete();

        return $this->success(__('删除成功'));
    }

    /**
     * 购物车数量修改
     *
     * @ApiMethod (POST)
     * @param int $cart_id 购物车id
     * @param int $num 数量
     * @param string $type 加/减(add/reduce)
     */
    public function set_num(){

        $cart_id = $this->request->post('cart_id');
        $num = $this->request->post('num', 0);
        $type = $this->request->post('type' ,'');
        $user_id = $this->auth->id;

        if (!$num && $type == 'add') { //默认加一
            CartModel::where('cart_id', $cart_id)->where('user_id', $user_id)->setInc('cart_num');
        } elseif (!$num && $type == 'reduce') { //默认加一
            CartModel::where('cart_id', $cart_id)->where('user_id', $user_id)->setDec('cart_num');
        }else{
            $cart = CartModel::where('cart_id', $cart_id)->where('user_id', $user_id)->find();
            if ($cart) {
                $cart->cart_num = $num;
                $cart->save();
            }
        }
        return $this->success(__('修改成功'));
    }

    /**
     * 加入购物车
     *
     * @ApiMethod (POST)
     * @param int $mer_id 商户id
     * @param int $product_id 商品id
     * @param int $num 数量
     * @param string $spec 规格
     */
    public function add()
    {
        $mer_id = $this->request->post('mer_id', 0);
        $product_id = $this->request->post('product_id');
        $num = $this->request->post('num', 0);
        $user_id = $this->auth->id;

        $productModel = new ProductModel;
        $product = $productModel->where(['product_id' => $product_id, 'switch' => 1])->find();
        if (!$product) {
            $this->error(__('商品不存在或已下架'));
        }

        $spec = $this->request->post('spec', '');

        $productBase =$productModel->getBaseData($product->getData(), $spec);
        if (!$productBase['stock'] || $productBase['stock'] <= $num) {
            $this->error(__('库存不足'));
        }

        $cartModel = new \app\api\model\Cart();
        $cartModel->where(['user_id' => $user_id, 'product_id' => $product_id]);
        $spec && $cartModel->where('spec', $spec);
        $oldCart = $cartModel->find();

        if ($oldCart) {
            $oldCart->cart_num += $num;
            $result = $oldCart->save();
        } else {
            $cartModel->mer_id = $mer_id;
            $cartModel->user_id = $user_id;
            $cartModel->product_id = $product_id;
            $spec && $cartModel->spec = $spec;
            $cartModel->cart_num = $num;
            $result = $cartModel->save();
        }

        if ($result) {
            $this->success(__('添加成功'));
        } else {
            $this->error(__('添加失败'));
        }
    }
}
