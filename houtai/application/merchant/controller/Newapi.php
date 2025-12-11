<?php

namespace app\merchant\controller;

use app\common\controller\Mer;
use app\merchant\model\merchant\Merchant as MerchantModel;

/**
 * 接口
 */
class Newapi extends Mer
{
    protected $noNeedLogin = ['get_kf_url','get_shops_kf','get_kf_num','get_kf_urls'];
    protected $noNeedRight = '*';
        public function _initialize()
    {
        parent::_initialize();
    }
        /**
     * 获取客服工作台链接
     *
     * @ApiMethod (POST)
     */
    public function get_kf_url()
    {
         $mer_id = $this->auth->mer_id;
            //商户信息
        $merInfo = MerchantModel::field('mer_id, mer_name,mer_avatar, real_name, mer_phone')->where(['mer_id'=>$mer_id])->find();
        $merInfo = $merInfo->toArray();
        $time = time();
        $data = [];
        $data['uu'] = $merInfo['mer_phone'];
        $data['nn'] = $merInfo['mer_name'];
       
        $data['tt'] = $time;
        $data['ss'] = md5($merInfo['mer_phone'].'123456'.$time.'asd123');
        $r = 'https://kf.shop110.top/service/login/sf_login?'.http_build_query($data);
         $this->success(__('请求成功'), $r);
    }
    
    
    public function get_shops_kf(){
        $mer_phone = '13012345678';
          $time = time();
        $data = [];
        $data['uu'] = $mer_phone;
       
        $data['tt'] = $time;
        $data['ss'] = md5($mer_phone.$time.'asd123');
          $r = file_get_contents('http://127.0.0.3/service/login/shops_kf?'.http_build_query($data));
          $r = json_decode($r,true);
        $data['visiter_id'] = $data['visiter_id'];
        $data['visiter_name'] = $data['visiter_name'];
        $data['avatar'] = $data['avatar'];
       
        
        $url = $r['url'].$data['visiter_id'].$data['visiter_name'].$data['avatar'].$r['groupid'].$r['business_id'];
          
          
         $this->success(__('请求成功'), $url);
    }
    public function get_kf_urls(){
         $mer_id = $this->auth->mer_id;
            //商户信息
        $merInfo = MerchantModel::field('mer_id, mer_name,mer_avatar, real_name, mer_phone')->where(['mer_id'=>$mer_id])->find();
        $merInfo = $merInfo->toArray();
            $time = time();
            $mer_phone = $merInfo['mer_phone'];
        $data = [];
        $data['uu'] = $mer_phone;

        $data['tt'] = $time;
        $data['ss'] = md5($mer_phone.$time.'asd123');
          $r = file_get_contents('http://127.0.0.3/service/login/shops_pt_kf?'.http_build_query($data));
          $r = json_decode($r,true)['data'];
        //   dump($merInfo);
        //   dump($r);die;
        $data['visiter_id'] = $r['visiter_id'].$merInfo['mer_id'];
        $data['visiter_name'] = $r['visiter_name'].$merInfo['real_name'];
        $data['avatar'] = $r['avatar'].$merInfo['mer_avatar'];
       
        
        $url = $r['url'].$data['visiter_id'].$data['visiter_name'].$data['avatar'].$r['groupid'].$r['business_id'].'&special=1';
          $url = "https://kf.shop110.top/index/index/home?visiter_id={$merInfo['mer_id']}&visiter_name={$merInfo['real_name']}&avatar={$merInfo['mer_avatar']}&groupid=0&business_id=1";
        //  $url = 'https://tiktok-kf658vip.xyz/index/index/home?visiter_id=&visiter_name=&avatar=&groupid=0&business_id=1';
        //   dump($url);die;
         $this->success(__('请求成功'), $url);
    }
    
    public function get_kf_num(){
        $mer_id = $this->auth->mer_id;
            //商户信息
        $merInfo = MerchantModel::field('mer_id, mer_name,mer_avatar, real_name, mer_phone')->where(['mer_id'=>$mer_id])->find();
        $merInfo = $merInfo->toArray();
        $time = time();
        $data = [];
        $data['uu'] = $merInfo['mer_phone'];
        $data['tt'] = $time;
        $data['ss'] = md5($merInfo['mer_phone'].$time.'asd123');
        $r = 'http://127.0.0.3/service/login/shop_num?'.http_build_query($data);
        $r = file_get_contents($r);
        // dump($r);die; 
          $r = json_decode($r,true);
        // dump($r);die; 
         $this->success(__('请求成功'), $r['data']);
    }
}