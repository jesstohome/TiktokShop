<?php

namespace app\api\controller;

use app\admin\model\product\Category as CategoryModel;
use app\admin\model\merchant\Product as MerProductModel;
use app\api\model\Product as ProductModel;
use app\api\model\Merchant as MerchantModel;
use app\api\model\Order as OrderModel;
use app\common\controller\Api;
use fast\Http;
use think\Queue;
use think\Db;
use app\common\controller\Excel;

/**
 * 首页接口
 */
class Test extends Api
{
    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['*'];


    public function ttt(){
        die;
        $src = '/excel/9999.xlsx';
        $excel = new Excel();
        $ret = $excel->importExecl('.'.$src); 
        $data = [];
        $i=0;
        foreach($ret as $v){
            $data[$i]['gj'] = $v['A'];
            $data[$i]['addr'] = $v['B'];
            $data[$i]['name'] = $v['C'];
            $data[$i]['phone'] = $v['D'];
            $data[$i]['created_at'] = time();
           $i++; 
        }
        Db::name('addr')->insertAll($data);
    }
    /**
     * 首页
     *
     */
    public function index()
    {
        $category = $this->request->get('category');
        $page = $this->request->get('page');
        $url = "https://api-gw.onebound.cn/shopee/item_search/?key=t3101918532&&q=".$category."&page=".$page."&sort=&country=.com.my&lang=en&secret=20240412";
//        $url = "https://api-gw.onebound.cn/shopee/item_search/?key=t3101918532&&q=数码产品&page=1&sort=&country=.com.my&lang=en&secret=20240412";

        $result = Http::get($url);
        $arr = json_decode($result,true);
//        print_r($arr);
        $list = $arr['items']['item'];
        $detail_urls = [];
        foreach ($list as $v){
            // 替换字符串
            $num_iid = str_replace('https://shopee.com.my/product/','',$v['detail_url']);
//            $this->detail($num_iid);
        }

//        $detail_urls = json_encode($detail_urls);
//        print_r($list);
//        print_r($detail);

        $this->success('请求成功');
    }

    /**
     * 首页
     *
     */
    public function detail($num_iid = ''){
//        $num_iid = $this->request->get('num_iid');
//        $item = @file_get_contents("php://input");
//        $detail_urls = json_decode($detail_urls,true);
//        $num_iid = $detail_urls[0];
        $detail_url = "https://api-gw.onebound.cn/shopee/item_get/?key=t3101918532&&num_iid=$num_iid&country=.com.my&lang=en&secret=20240412";
        $result = Http::get($detail_url);
        $arr = json_decode($result,true);
//        var_dump();
//        $arr = json_decode($item,true);
//        print_r($arr);die;
//        print_r($arr['item']);

        $detail = $arr['item'];

        // 处理规格
        $skus = $detail['skus'];

        $skus_arr = array();
        if($skus['sku']){
            $qs = 0;
            foreach($skus['sku'] as $sku){
                $qs+=$sku['quantity'];
            }
            foreach($skus['sku'] as $sku){
                if(!$sku['quantity'] && !$qs) $sku['quantity']=999;
                $skus_arr[] = array(
                    'sku_id' => $sku['sku_id'],
                    'properties' => $sku['properties'],
                    //$sku['properties_name'],
                    'price' => $sku['price'],
                    'quantity' => (int)$sku['quantity'],
                    'orginal_price' => $sku['orginal_price']
                );
            }
        }
        // print_r($skus_arr);

        $prop_imgs_arr = array();
        if($detail['prop_imgs']){
            $prop_imgs = json_decode(json_encode($detail['prop_imgs']),true);
            if($prop_imgs['prop_img']){
                foreach($prop_imgs['prop_img'] as $prop_img){
                    $prop_imgs_arr[$prop_img['properties']] = $prop_img['url'];
                }
            }
        }

        $prop_array=array();
        foreach($detail['props_list'] as $dk=>$val){
            $dks=explode(':',$dk);
            $vals=explode(':',$val);
            $prop_img = !empty($prop_imgs_arr[$dks[1].':'.$dks[0]])?$prop_imgs_arr[$dks[1].':'.$dks[0]]:'';

            $prop_array[$dks[0]][$dks[1]]=[
                'prop_key'=>$dks[0],
                'prop_val'=>$dks[1],
                'name'=>$vals[0],
                'value'=>$vals[1],
                'pic_url'=>$prop_img,
            ];

            foreach ($skus_arr as &$sku){
                if($dk == $sku['properties']){
                    $sku['spec'] = str_replace(':',',',$val);
                    $sku['prop_img'] = $prop_img;
                }
            }
        }

        // print_r($skus_arr);
        $specTableList = '';
        $stock = 0;
        if($skus_arr) {
            $specList = [];
            foreach ($skus_arr as $v) {
                $specList[] = [
                    'code' => $v['sku_id'],
                    'image' => $v['prop_img'],
                    'market_price' => $v['orginal_price'],
                    'sales_price' => $v['price'],
                    'cost_price' => 0,
                    'sales' => 0,
                    'stock' => $v['quantity'],
                    'value' => explode(',', $v['spec'])
                ];
                $stock += $v['quantity'];
            }
            $specTableList = json_encode($specList);
        }
        $use_spec = $specTableList ? 1 : 0;

        $model = new \app\api\model\Product;
        $data = [
            'category_id' => 1,
            'code' => $detail['num_iid'],
            'title' => $detail['title'],
            'title_en' => $detail['title'],
            'image' => $detail['pic_url'],
            'sales_price' => $detail['price'],
            'cost_price' => $detail['orginal_price'],
            'market_price' => $detail['orginal_price'],
            'content' => $detail['desc'],
            'sales' => $detail['sales'],
            'look' => $detail['sales'],
            'real_sales' => $detail['sales'],
            'real_look' => $detail['sales'],
            'stock' => $stock,
            'use_spec' => $use_spec,
            'specTableList' => $specTableList,
            'switch' => 1
        ];

        $model->save($data);
    }

    /**
     * 测试队列
     */
    public function test(){
        // 订单自动归档加入队列
        // $list = \app\admin\model\order\Delivery::where(['delivery_no' => '60005'])->select();
        // foreach ($list as $row){
        //     $delay = $row['interval'] * $row['step'];
        //     Queue::later($delay, 'app\admin\job\DeliveryAutoFinish' , $row , 'DeliveryAutoFinish');
        // }
        // $str = "ewogICJjdXN0b21JRCIgOiAiQGltNjc4Njc4IiwKICAiYXZhdGFyIiA6ICJodHRwczpcL1wvcDE2LXNpZ24tc2cudGlrdG9rY2RuLmNvbVwvdG9zLWFsaXNnLWF2dC0wMDY4XC9iNjljNzhlY2E1ZDZmZjM4OTQxOTZjY2I4YTExYjZlZX5jNV8zMDB4MzAwLndlYnA/bGszcz1hNWQ0ODA3OCZ4LWV4cGlyZXM9MTcxMzg3MzYwMCZ4LXNpZ25hdHVyZT10T0dkSXNtZXBlcFZlZDhRJTJCNFRKUENWZTRuNCUzRCIsCiAgImxhbmciIDogInpoIiwKICAibmlja25hbWUiIDogImltNjc4Njc4IiwKICAidGltZXpvbmUiIDogIkdNVCswODowMCIsCiAgInRpa3Rva19pZCIgOiAiNzI1NTc0NTk5NzcyMzc4MDA5OCIsCiAgInZlcnNpb24iIDogInYwLjAuMyIsCiAgInRpbWVzdGFtcCIgOiAxNzEzNzAyMDQwNjU3LAogICJyb3V0ZSIgOiAic2hvcENlbnRlciIKfQ==";
        // $res = base64_decode($str);
        // $res = json_decode($res,true);
        // $this->success('请求成功',$res);

        echo json_encode([1,2,3]);
    }

    public function addProduct()
    {
        $data = file_get_contents('php://input');
        $arr = json_decode($data,true);

        $model = new \app\api\model\Product;


        foreach ($arr as $v){
            $add[] = [
                'category_id' => 10,
                'code' => $v['ID'],
                'title' => $v['name'],
                'title_en' => $v['name'],
                'image' => $v['thumbnail_img'],
                'images' => $v['photos'],
                'sales_price' => $v['purchase_price'],
                'cost_price' => 0.00,
                'market_price' => $v['unit_price'],
                'content' => $v['description'],
                'sales' => 0,
                'look' => 0,
                'real_sales' => 0,
                'real_look' => 0,
                'stock' => $v['current_stock'],
                'use_spec' => 0,
                'switch' => 1
            ];
        }

        $res = $model->allowField(true)->saveAll($add);

        $this->success('请求成功',$res);
    }
}
