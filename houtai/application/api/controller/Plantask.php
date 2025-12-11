<?php

namespace app\api\controller;

use app\common\controller\Api;

use app\merchant\model\merchant\Product as MerProductModel;
use app\api\model\Merchant as MerchantModel;
use fast\Random;
use think\Config;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;
use think\Validate;

/**
 * 会员接口
 */
class Plantask extends Api
{
    protected $noNeedLogin = ['*'];
    protected $noNeedRight = '*';

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\merchant\Merchant;
    }
    
    public function task_auto_num(){
        $r = $this->model->whereNull('auto_data')->select(); 
        $rs = $this->model->where(['auto_data'=>['<>',date('Y-m-d')]])->select(); 
        // $rs = $this->model->select(); 
        // dump($r);
        $r = array_merge($r,$rs);
        $r = json_decode(json_encode($r),true);
        // dump($r);
        
 
        $fw_num = Db::name('config')->where('name','fw_num')->value('value');
        $gz_num = Db::name('config')->where('name','gz_num')->value('value');
        $fw_num = $fw_num?$fw_num/100:0;
        $gz_num = $gz_num?$gz_num/100:0;
        if($r){
            $m = (new \app\admin\model\merchant\Merchant());
            $ms = (new \app\admin\model\merchant\Merchant());
            foreach($r as $k=>$v){
                $mm =  $m->where('mer_id',$v['mer_id'])->find();
                // dump(($mm['follow_count']*$gz_num+$mm['follow_count']).'----'.($mm['visit']*$gz_num+$mm['visit']));
                $ms->where('mer_id',$v['mer_id'])->update(['auto_data'=>date('Y-m-d'),'follow_count'=>$mm['follow_count']*$gz_num+$mm['follow_count'],'visit'=>$mm['visit']*$gz_num+$mm['visit']]);
            }
        }
          echo 'success';
    }
    
    public function task_auto_nums(){
        $r = $this->model->select(); 
        // $rs = $this->model->where(['auto_data'=>['<>',date('Y-m-d')]])->select(); 
        // $rs = $this->model->select(); 
        // dump($r);
        // $r = array_merge($r,$rs);
        $r = json_decode(json_encode($r),true);
        // dump($r);
        
 
        $fw_num = Db::name('config')->where('name','fw_num')->value('value');
        $fw_num_a = Db::name('config')->where('name','fw_num_a')->value('value');
        $fw_num_b = Db::name('config')->where('name','fw_num_b')->value('value');
        $fw_num_c = Db::name('config')->where('name','fw_num_c')->value('value');
        $gz_num = Db::name('config')->where('name','gz_num')->value('value');
        $fw_num = $fw_num?$fw_num/100:0;
        $fw_num_a = $fw_num_a?$fw_num_a/100:0;
        $fw_num_b = $fw_num_b?$fw_num_b/100:0;
        $fw_num_c = $fw_num_c?$fw_num_c/100:0;
        $gz_num = $gz_num?$gz_num/100:0;
        if($r){
            $m = (new \app\admin\model\merchant\Merchant());
            $ms = (new \app\admin\model\merchant\Merchant());
            foreach($r as $k=>$v){
                $mm =  $m->where('mer_id',$v['mer_id'])->find();
                // dump(($mm['follow_count']*$gz_num+$mm['follow_count']).'----'.($mm['visit']*$gz_num+$mm['visit']));
                $ms->where('mer_id',$v['mer_id'])->update(['auto_data'=>date('Y-m-d'),'follow_count'=>$mm['follow_count']*$gz_num+$mm['follow_count'],
                'visit'=>$mm['visit']*$gz_num+$mm['visit'],
                'today_visit'=>$mm['today_visit']*$fw_num_a+$mm['today_visit'],
                'seven_visit'=>$mm['seven_visit']*$fw_num_b+$mm['seven_visit'],
                'thirty_visit'=>$mm['thirty_visit']*$fw_num_c+$mm['thirty_visit']
                
                
                ]);
            }
        }
          echo 'success';
    }
    
    
    
}