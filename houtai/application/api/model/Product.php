<?php
/**
 * Created by PhpStorm.
 * User: licj
 * Date: 2019/11/10
 * Time: 11:45 上午
 */


namespace app\api\model;

use think\Exception;
use think\Model;
use traits\model\SoftDelete;

/**
 * 商品模型
 * Class Product
 * @package app\api\model
 */
class Product extends Model
{
    use SoftDelete;

    // 表名
    protected $name = 'product';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        //'spec_list',
        //'spec_table_list',
    ];

    // 隐藏属性
    protected $hidden = [

    ];

    /**
     * 处理图片
     * @param $value
     * @return string
     */
    public function getImageAttr($value) {
        return Config::getImagesFullUrl($value);
    }

    /**
     * 处理图片
     * @param $value
     * @param $data
     * @return string
     */
    public function getImagesAttr($value, $data){
        $images = explode(',', $data['images']);
        foreach ($images as &$image) {
            $image = Config::getImagesFullUrl($image);
        }
        return implode(',', $images);
        //return $images;
    }

    /**
     * 获取销售量
     * @param $value
     * @param $data
     */
    public function getSalesAttr($value, $data) {
        return $data['sales'] + $data['real_sales'];
    }

    /**
     * 处理规格属性
     * @param $value
     * @param $data
     * @return mixed
     */
    public function getSpecListAttr($value, $data) {
        return !empty($data['specList']) ? json_decode($data['specList'], true) : [];
    }

    /**
     * 处理规格值
     * @param $value
     * @param $data
     * @return mixed
     */
    public function getSpecTableListAttr($value, $data) {
        $specs = !empty($data['specTableList']) ? json_decode($data['specTableList'], true) : [];
        foreach ($specs as &$spec) {
            $spec['image'] = Config::getImagesFullUrl($spec['image']);
        }
        return $specs;
    }

    /**
     * 获取创建订单需要的产品信息
     * @param string $spec
     * @param int $number
     * @return array
     * @throws Exception
     */
    public function getDataOnCreateOrder(string $spec = '', $number = 1)
    {
        $data = (new \app\api\extend\Product)->getBaseData($this->getData(), $spec);
        if ($data['stock'] < 1) {
            throw new Exception('产品库存不足');
        }
        $product = $this->getData();
        $data['title'] = $product['title'];
        $data['spec'] = $spec;
        $data['number'] = $number;
        $data['id'] = Hashids::encodeHex($product['id']);
        return $data;
    }

    public function getProductSpecInfo(string $spec = '', $number = 1)
    {
        $data = $this->getBaseData($this->getData(), $spec);
        if ($data['stock'] < 1) {
            throw new Exception(__('库存不足'));
        }
        $product = $this->getData();
        $data['title'] = $product['title'];
        $data['spec'] = $spec;
        $data['number'] = $number;
        //$data['weight'] = round($number * $product['weight'],2);
        //$data['id'] = Hashids::encodeHex($product['id']);
        //$data['store_id'] = $product['store_id'];;
        //$data['store'] = $this->store ? $this->store->toArray() : [];
        return $data;
    }

    /**
     * 获取商品的基础信息
     * @param array $product 商品信息数组
     * @param string $spec 规格值，用,号隔开
     * @param string $key 要获取的字段
     * @return array
     */
    public function getBaseData(array $product, string $spec = '', string $key = '')
    {
        if (!$product) {
            return [];
        }
        $data = [];
        if ($spec && $product['use_spec'] == 1 && !empty($product['specTableList'])) {
            $specValueArr = json_decode($product['specTableList'], true);
            foreach ($specValueArr as $k => $specItem) {
                if (implode(',', $specItem['value']) == $spec) {
                    if ($key) {
                        $data = $specItem[$key];
                    } else {
                        $data = $specItem;
                        $data['key'] = $k;
                    }
                }
            }
        }
        if (empty($data)) {
            if ($key) {
                $data = $product[$key];
            } else {
                $data['cost_price'] = $product['cost_price'];
                $data['sales_price'] = $product['sales_price'];
                $data['stock'] = $product['stock'];
                $data['sales'] = $product['sales'];
                $data['image'] = cdnurl($product['image'],true);
            }
        }
        if (is_array($data)){
            $data['image'] = $data['image'] ? cdnurl($data['image'],true) : cdnurl($product['image'],true);
        }
        return $data;
    }

    /**
     * 获取商品信息数据
     *
     * @param  int $id 对应的类型信息的id
     * @return array
     */
    public function getProductInfo($id)
    {
        $productInfo = $this
            ->where(['product_id' => $id, 'switch' => 1, 'deletetime' => null])
            ->find();
        if (!$productInfo) {
            throw new Exception(__("商品不存在"), 1);
        }
        //  商品规格参数
        $spec = input('spec', '');
        $number = input('number');
        $mer_id = input('mer_id');

        $res = $productInfo->getProductSpecInfo($spec, $number);
        $res['mer_id'] = $mer_id;
        $res['product_id'] = $id;

        $data[] = $res;
        return $data;
    }

    /**
     * 根据cart id 获取对应的商品信息数据
     *
     * @param string $ids
     * @return array
     */
    public function getProductByCart(string $ids)
    {
        $where = [
            'cart_id' => ['IN',$ids],
        ];
        $cartProduct = Cart::where($where)
            ->with(['product','merchant'])
            ->order(['cart_id' => 'desc'])
            ->select();
        if (!$cartProduct) {
            return [];
        }
        $data = [];
        foreach ($cartProduct as $k=> $cart) {
            if ($cart->product instanceof Product) {
                $res = $cart->product->getProductSpecInfo($cart->spec ? $cart->spec : '', $cart->cart_num);
                if($cart->merchant){
                    $res['mer_id'] = $cart->merchant->mer_id;
                    $res['mer_name'] = $cart->merchant->mer_name;
                }
                $res['cart_id'] = $cart->cart_id;
                $res['product_id'] = $cart->product_id;
                $data[] = $res;
//                $data[$cart['cart_id']] = $res;
            }

        }
        return $data;
    }
}
