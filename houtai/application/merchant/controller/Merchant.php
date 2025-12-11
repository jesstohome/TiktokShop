<?php

namespace app\merchant\controller;

use app\common\controller\Mer;
use app\common\library\Ems;
use app\common\library\Sms;
use fast\Random;
use think\Config;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;
use think\Validate;
use app\merchant\model\merchant\Merchant as MerchantModel;
use app\merchant\model\merchant\Bill as BillModel;
use app\merchant\model\Affiche as AfficheModel;
use app\merchant\model\merchant\Recharge as RechargeModel;
use app\merchant\model\merchant\Extract as ExtractModel;
use app\merchant\model\order\OrderRefund as OrderRefundModel;
use app\merchant\model\order\Order as OrderModel;

/**
 * 商户接口
 */
class Merchant extends Mer
{
    protected $noNeedLogin = ['login', 'mobilelogin', 'register', 'resetpwd', 'changeemail', 'changemobile', 'third', 'tkregister', 'verify_code', 'language_list', 'set_language'];
    protected $noNeedRight = '*';

    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 会员中心
     */
    public function index()
    {
        $this->success('', ['welcome' => $this->auth->nickname]);
    }

    /**
     * 商户登录
     *
     * @ApiMethod (POST)
     * @param string $account  账号
     * @param string $password 密码
     */
    public function login()
    {
        $account = $this->request->post('account');
        $password = $this->request->post('password');
        if (!$account || !$password) {
            $this->error(__('参数错误'));
        }
        $ret = $this->auth->login($account, $password);
        if ($ret) {
            $data = ['merinfo' => $this->auth->getMerinfo()];
            $this->success(__('登录成功'), $data);
        } else {
            $this->error($this->auth->getError());
        }
    }

    /**
     * 注册商户
     *
     * @ApiMethod (POST)
     * @param string $mer_avatar 商户logo
     * @param string $mer_name   商户名称
     * @param string $mer_address   商户地址
     * @param string $real_name   真实姓名
     * @param string $country   国家
     * @param string $images   证件照
     * @param string $mer_email    邮箱
     * @param string $mer_phone   手机号
     * @param string $password 密码
     * @param string $repassword 重复密码
     * @param string $code     代理人邀请码
     * @param string $mer     商户邀请码
     */
    public function register()
    {
        $mer_avatar = $this->request->post('mer_avatar');
        $mer_name = $this->request->post('mer_name');
        $mer_address = $this->request->post('mer_address');
        $real_name = $this->request->post('real_name');
        $country = $this->request->post('country');
        $images = $this->request->post('images');
        $mer_email = $this->request->post('mer_email');
        $mer_phone = $this->request->post('mer_phone');
        $password = $this->request->post('password');
        $repassword = $this->request->post('repassword');
        $code = $this->request->post('code');  //代理人邀请
        $mer = $this->request->post('mer');   //商户邀请
        if (!$real_name || !$password) {
            $this->error(__('参数错误'));
        }
        if ($mer_email && !Validate::is($mer_email, "email")) {
            $this->error(__('邮箱不正确'));
        }
        if (!$mer_phone /*&& !Validate::regex($mer_phone, "^1\d{10}$")*/) {
            $this->error(__('手机号不正确'));
        }
        if (!Validate::regex($mer_phone, "^\d{1,20}$")) {
            $this->error(__('手机号码长度不能超过20个字符'));
        }
        if ($password != $repassword) {
            $this->error(__('两次输入的密码不一致'));
        }

        // 检测商户名、邮箱、手机号是否存在
        if (MerchantModel::getByMername($mer_name)) {
            $this->error(__('商户名已存在'));
        }
        if ($mer_email && MerchantModel::getByEmail($mer_email)) {
            $this->error(__('邮箱已存在'));
        }
        if ($mer_phone && MerchantModel::getByPhone($mer_phone)) {
            $this->error(__('手机号已存在'));
        }

        $agent_id = 0;
        if($code) {
            // 邀请码查询代理人
            $adminModel = new \app\admin\model\Admin;
            $agent = $adminModel->where(['code' => $code])->find();
            if ($agent) {
                $agent_id = $agent['id'];
            } else {
                $this->error(__('无效邀请码'));
            }
        }

        $spread_id = 0;
        if($mer) {
            $spread = MerchantModel::where(['code' => $mer])->find();
            if ($spread) {
                $spread_id = $spread['mer_id'];
            } else {
                $this->error(__('无效邀请码'));
            }
        }

        $salt = Random::alnum();
        $ip = request()->ip();
        $time = time();
        //注册-申请
        $merInfo = [
            'mer_avatar' => $mer_avatar,
            'mer_name' => $mer_name,
            'mer_address' => $mer_address,
            'real_name' => $real_name,
            'country' => $country,
            'images' => $images,
            'mer_email' => $mer_email,
            'mer_phone' => $mer_phone,
            'password' => $this->auth->getEncryptPassword($password, $salt),
            'agent_id' => $agent_id,
            'spread_id' => $spread_id,
            'code' => rand(100000,999999),
            'mer_level'    => 0,
            'salt'      => $salt,
            'logintime' => $time,
            'loginip'   => $ip,
            'prevtime'  => $time,
            'grade'      => '5.0',
            'credit'      => 95,
            'good_rate'      => 98,
            'status'    => 0    //等待审核
        ];

        $result = false;
        Db::startTrans();
        try {
            $merModel = new \app\merchant\model\merchant\Merchant;
            $result = $merModel->allowField(true)->save($merInfo);

            $intention = [
                'mer_id' => $merModel->mer_id,
                'phone' => $mer_phone,
                'email' => $mer_email,
                'mer_name' => $mer_name,
                'name' => $real_name,
                'mark' => null,
                'status' => 0,
                'images' => $images,
            ];
            $intentionModel = new \app\merchant\model\merchant\Intention;
            $res = $intentionModel->allowField(true)->save($intention);

            Db::commit();
        } catch (ValidateException|PDOException|Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        if ($result === false || $res === false) {
            $this->error(__('注册成功'));
        }

        $this->success(__('注册成功，请等待后台审核'));
    }

    /**
     * 验证邀请码
     *
     * @ApiMethod (POST)
     * @param string $code 代理邀请码
     * @param string $mer  商户邀请码
     */
    public function verify_code()
    {
        $code = $this->request->post('code');  //代理人邀请
        $mer = $this->request->post('mer');   //商户邀请
        //$mer = $this->auth->getMerchant();
        $agent_id = 0;
        if($code) {
            // 邀请码查询代理人
            $adminModel = new \app\admin\model\Admin;
            $agent = $adminModel->where(['code' => $code])->find();
            if ($agent) {
                $agent_id = $agent['id'];
            }else {
                $this->error(__('无效邀请码'));
            }
        }

        $spread_id = 0;
        // if($mer) {
        //     $spread = MerchantModel::where(['code' => $mer])->find();
        //     if ($spread) {
        //         $spread_id = $spread['mer_id'];
        //     }else {
        //         $this->error(__('无效邀请码'));
        //     }
        // }

        $data = [
            'agent_id' => $agent_id,
            'spread_id' => $spread_id,
        ];

        $this->success('',$data);
    }

    /**
     * 第三方登录
     *
     * @ApiMethod (POST)
     * @param string $data     data码
     */
    public function third()
    {
        $data = $this->request->post("data");
        $code = $this->request->post('code');  //代理人邀请
        $mer = $this->request->post('mer');   //商户邀请
        $data = str_replace(' ','+',$data);
        $res = base64_decode($data);
        $info = json_decode($res,true);
        //print_r($info);
        if(!$info){
            $this->error(__('第三方获取信息失败'));
        }
        $merModel = new \app\merchant\model\merchant\Merchant;

        $tiktok_id = $info['tiktok_id'];
        $merchant = $merModel->where(['tiktok_id' => $tiktok_id])->find();
        if($merchant){ //已存在账号直接登录
            $this->auth->direct($merchant['mer_id']);
            $merinfo = $this->auth->getMerinfo();
            $merinfo['domain'] = config('site.domain');
            $this->success(__('登录成功'), ['merinfo' => $merinfo]);
        }else{

            $this->success(__('账号不存在，请先注册'), ['status' => 0]);
            // $agent_id = 0;
            // if($code) {
            //     // 邀请码查询代理人
            //     $adminModel = new \app\admin\model\Admin;
            //     $agent = $adminModel->where(['code' => $code])->find();
            //     if ($agent) {
            //         $agent_id = $agent['id'];
            //     } else {
            //         $this->error(__('无效邀请码'));
            //     }
            // }
            //
            // $spread_id = 0;
            // if($mer) {
            //     $spread = MerchantModel::where(['code' => $mer])->find();
            //     if ($spread) {
            //         $spread_id = $spread['mer_id'];
            //     } else {
            //         $this->error(__('无效邀请码'));
            //     }
            // }
            // $mer_avatar = $this->request->post('mer_avatar');
            // $mer_name = $this->request->post('mer_name');
            // $mer_address = $this->request->post('mer_address');
            // $real_name = $this->request->post('real_name');
            // $country = $this->request->post('country');
            // $images = $this->request->post('images');
            // $mer_email = $this->request->post('mer_email');
            // $mer_phone = $this->request->post('mer_phone');
            // $password = $this->request->post('password');
            // $agent_id = $this->request->post('agent_id',0);
            // $spread_id = $this->request->post('spread_id',0);
            //
            // $salt = Random::alnum();
            // $ip = request()->ip();
            // $time = time();
            // //注册-申请
            // $merInfo = [
            //     'mer_avatar' => $mer_avatar,
            //     'mer_name' => $mer_name,
            //     'mer_address' => $mer_address,
            //     'real_name' => $real_name,
            //     'country' => $country,
            //     'images' => $images,
            //     'mer_email' => $mer_email,
            //     'mer_phone' => $mer_phone,
            //     'password' => $this->auth->getEncryptPassword($password, $salt),
            //     'agent_id' => $agent_id,
            //     'spread_id' => $spread_id,
            //     'tiktok_id' => $tiktok_id,
            //     'code' => rand(6,6),
            //     'mer_level'    => 0,
            //     'salt'      => $salt,
            //     'logintime' => $time,
            //     'loginip'   => $ip,
            //     'prevtime'  => $time,
            //     'grade'      => '5.0',
            //     'credit'      => 95,
            //     'good_rate'      => 98,
            //     'status'    => 1    //直接可用
            // ];
            //
            // $result = false;
            // Db::startTrans();
            // try {
            //     $result = $merModel->allowField(true)->save($merInfo);
            //     Db::commit();
            // } catch (ValidateException|PDOException|Exception $e) {
            //     Db::rollback();
            //     $this->error($e->getMessage());
            // }
            // if ($result === false) {
            //     $this->error(__('注册失败，请重试'));
            // }
            //
            // $this->auth->direct($merModel->mer_id);
            // $merinfo = $this->auth->getMerinfo();
            // $merinfo['domain'] = config('site.domain');
            // $this->success(__('注册成功，自动登录'), ['merinfo' => $merinfo]);
        }


    }

    /**
     * 注册商户
     *
     * @ApiMethod (POST)
     * @param string $mer_avatar 商户logo
     * @param string $mer_name   商户名称
     * @param string $mer_address   商户地址
     * @param string $real_name   真实姓名
     * @param string $country   国家
     * @param string $images   证件照
     * @param string $mer_email    邮箱
     * @param string $mer_phone   手机号
     * @param string $password 密码
     * @param string $repassword 重复密码
     * @param string $code     代理人邀请码
     * @param string $mer     商户邀请码
     */
    public function tkregister()
    {

        $data = $this->request->post("data");

        $mer_avatar = $this->request->post('mer_avatar');
        $mer_name = $this->request->post('mer_name');
        $mer_address = $this->request->post('mer_address');
        $mer_phone = $this->request->post('mer_phone');
        $real_name = $this->request->post('real_name');
        $country = $this->request->post('country');
        $images = $this->request->post('images');
        $mer_email = $this->request->post('mer_email');
        $code = $this->request->post('code');  //代理人邀请
        $mer = $this->request->post('mer');   //商户邀请
        $password = '123456';
        $data = str_replace(' ','+',$data);
        $res = base64_decode($data);
        $info = json_decode($res,true);
        $tiktok_id = $info['tiktok_id'];


        // if (!$real_name || !$password) {
        //     $this->error(__('参数错误'));
        // }
        if ($mer_email && !Validate::is($mer_email, "email")) {
            $this->error(__('邮箱不正确'));
        }
        // 检测商户名、邮箱、手机号是否存在
        if (MerchantModel::getByMername($mer_name)) {
            $this->error(__('商户名已存在'));
        }
        if ($mer_email && MerchantModel::getByEmail($mer_email)) {
            $this->error(__('邮箱已存在'));
        }
        $agent_id = 0;
        if($code) {
            // 邀请码查询代理人
            $adminModel = new \app\admin\model\Admin;
            $agent = $adminModel->where(['code' => $code])->find();
            if ($agent) {
                $agent_id = $agent['id'];
            } else {
                $this->error(__('无效邀请码'));
            }
        }
        $spread_id = 0;
        $salt = Random::alnum();
        $ip = request()->ip();
        $time = time();
        //注册-申请
        $merInfo = [
            'mer_avatar' => $mer_avatar,
            'mer_name' => $mer_name,
            'mer_address' => $mer_address,
            'real_name' => $real_name,
            'country' => $country,
            'images' => $images,
            'mer_email' => $mer_email,
            'mer_phone' => $mer_phone,
            'password' => $this->auth->getEncryptPassword($password, $salt),
            'agent_id' => $agent_id,
            'spread_id' => $spread_id,
            'tiktok_id' => $tiktok_id,
            'code' => rand(100000,999999),
            'mer_level'    => 0,
            'salt'      => $salt,
            'logintime' => $time,
            'loginip'   => $ip,
            'prevtime'  => $time,
            'grade'      => '5.0',
            'credit'      => 95,
            'good_rate'      => 98,
            'status'    => 0    //等待审核
        ];

        $result = false;
        Db::startTrans();
        try {
            $merModel = new \app\merchant\model\merchant\Merchant;
            $result = $merModel->allowField(true)->save($merInfo);

            $intention = [
                'mer_id' => $merModel->mer_id,
                'phone' => $mer_phone,
                'email' => $mer_email,
                'mer_name' => $mer_name,
                'name' => $real_name,
                'mark' => null,
                'status' => 0,
                'images' => $images,
            ];
            $intentionModel = new \app\merchant\model\merchant\Intention;
            $res = $intentionModel->allowField(true)->save($intention);

            Db::commit();
        } catch (ValidateException|PDOException|Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        if ($result === false || $res === false) {
            $this->error(__('注册成功'));
        }

        $this->success(__('注册成功，请等待后台审核'));
    }

    /**
     * 退出登录
     * @ApiMethod (POST)
     */
    public function logout()
    {
        if (!$this->request->isPost()) {
            $this->error(__('请求错误'));
        }
        $this->auth->logout();
        $this->success(__('登出成功'));
    }

    /**
     * 修改商户信息
     *
     * @ApiMethod (POST)
     * @param string $mer_avatar 商户logo
     * @param string $mer_name 商户名称
     * @param string $mer_address 商户地址
     * @param string $real_name 真实姓名
     * @param string $country 国家
     * @param string $mer_email 邮箱
     * @param string $mer_phone 手机号
     */
    public function profile()
    {
        $mer = $this->auth->getMerchant();
        $mer_email = $this->request->post('mer_email');
        $mer_phone = $this->request->post('mer_phone');
        $mer_avatar = $this->request->post('mer_avatar');
        $mer_name = $this->request->post('mer_name');
        $mer_address = $this->request->post('mer_address');
        $real_name = $this->request->post('real_name');
        $country = $this->request->post('country');
        if ($mer_email) {
            $exists = MerchantModel::where('mer_email', $mer_email)->where('mer_id', '<>', $this->auth->mer_id)->find();
            if ($exists) {
                $this->error(__('邮箱已存在'));
            }
            $mer->mer_email = $mer_email;
        }
        if ($mer_phone) {
            $exists = MerchantModel::where('mer_phone', $mer_phone)->where('mer_id', '<>', $this->auth->mer_id)->find();
            if ($exists) {
                $this->error(__('手机号已存在'));
            }
            $mer->nickname = $mer_phone;
        }
        $mer->mer_avatar = $mer_avatar;
        $mer->mer_name = $mer_name;
        $mer->mer_address = $mer_address;
        $mer->real_name = $real_name;
        $mer->country = $country;
        $mer->save();
        $this->success(__('修改成功'));
    }

    /**
     * 重置密码
     *
     * @ApiMethod (POST)
     * @param string $oldpassword 旧密码
     * @param string $newpassword 新密码
     */
    public function resetpwd()
    {
        $oldpassword = $this->request->post("oldpassword" ,'');
        $newpassword = $this->request->post("newpassword");
        $renewpassword = $this->request->post("renewpassword");
        $mer_phone = $this->request->post("mer_phone",'');
        if (!$newpassword) {
            $this->error(__('参数错误'));
        }

        if ($newpassword != $renewpassword) {
            $this->error(__('两次输入的密码不一致'));
        }
        //验证Token
        if (!Validate::make()->check(['newpassword' => $newpassword], ['newpassword' => 'require|regex:\S{6,30}'])) {
            $this->error(__('密码长度必须在6-30位之间，不能包含空格'));
        }
        if(!$mer_phone) {
            $ret = $this->auth->changepwd($newpassword, $oldpassword, false);
        }else{
            $userModel = new \app\merchant\model\merchant\Merchant();
            $mer = $userModel->where(['mer_phone' => $mer_phone])->find();
            if(!$mer){
                $this->error(__('商户不存在'));
            }
            $salt = Random::alnum();
            $newpassword = $this->auth->getEncryptPassword($newpassword, $salt);
            $ret = $mer->save(['password' => $newpassword, 'salt' => $salt]);
        }
        //$ret = $this->auth->changepwd($newpassword, $oldpassword, false);
        if ($ret) {
            $this->success(__('重置密码成功'));
        } else {
            $this->error($this->auth->getError());
        }
    }

    /**
     * 修改支付密码
     *
     * @ApiMethod (POST)
     * @param string $old 旧密码
     */
    public function resetPay()
    {
        $new = $this->request->post("new");
        $old = $this->request->post("old");
        $is_verify = $this->request->post("is_verify");
        if (!$new) {
            $this->error(__('参数错误'));
        }

        $mer = $this->auth->getMerchant();

        //验证Token
        if (!Validate::make()->check(['new' => $new], ['new' => 'require|length:6|number'])) {
            $this->error(__('支付密码必须是6位数字'));
        }
        if($is_verify){
            if($mer->password_pay == md5($old)){
                $this->error(__('旧密码错误'));
            }
        }


        if($mer->password_pay == md5($new)){
            $this->error(__('不能与旧密码重复'));
        }

        $mer->password_pay = md5($new);
        $ret = $mer->save();

        if ($ret) {
            $this->success(__('设置支付密码成功'));
        } else {
            $this->error($this->auth->getError());
        }
    }

    /**
     * 获取商户信息
     *
     */
    public function getMerInfo()
    {
        $mer_id = $this->auth->mer_id;
        //商户信息
        $merInfo = MerchantModel::field('mer_id, mer_name,mer_avatar, real_name, mer_phone, mer_email, mer_money, mer_level, follow_count, grade, credit, mer_address,mer_info, mark, agent_id, spread_id,password_pay,status')->where(['mer_id'=>$mer_id])->find();

        $merInfo['have_pay'] = $merInfo['password_pay'] ? 1 : 0;
        unset($merInfo['password_pay']);

        //客服链接
        if($merInfo['agent_id']){
            $adminModel = new \app\admin\model\Admin;
            $agent = $adminModel->where(['id' => $merInfo['agent_id']])->find();
            $merInfo['custom_service'] = $agent['custom_service'];
        }else{
            $merInfo['custom_service'] = config('site.custom_service');
        }

        //未读信息
        $noticeModel = new \app\merchant\model\merchant\Notice;
        $unreadNotice = $noticeModel->where(['mer_id' => $mer_id, 'is_see' => 0])->count();
        $merInfo['unread_notice'] = $unreadNotice;

        //累计收入
        $where = [
            'mer_id' => $mer_id,
            'pm' => 1,
            'type' => 'order'
        ];
        $total_income = BillModel::where($where)->sum('money');
        $merInfo['total_income'] = $total_income ? number_format($total_income,2) : 0.00;

        $this->success(__('商户信息'), $merInfo);
    }

    /**
     * 公告
     *
     */
    public function affiche()
    {
        $list = AfficheModel::where(['status'=>1])->select();
        $this->success(__('请求成功'), $list);
    }

    /**
     * 语言
     *
     */
    public function language()
    {
        $list = ['zh-cn'=>'中文','en'=>'English'];
        $this->success(__('请求成功'), $list);
    }

    /**
     * 切换语言
     *
     * @param string $lang 语言
     */
    public function change_language()
    {
        $lang = $this->request->get('lang', 'zh-cn'); //默认中文
        $this->success(__('请求成功'), $lang);
    }

    /**
     * 资金记录
     *
     * @ApiMethod (GET)
     *
     * @param string $type 类型(all=全部，recharg=充值,extract=提现,order=订单,train=直通车)
     * @param int $page 页码
     * @param int $limit 每页数量
     */
    public function bill()
    {
        $type = $this->request->get('type','all');
        $page = $this->request->get('page',1);
        $limit = $this->request->get('limit',20);

        $where = ['mer_id' => $this->auth->mer_id];

        if($type != 'all'){
            $where['type'] = $type;
        }else{
            $where['type'] = ['in',['recharge','extract','order']];
        }

        $count = BillModel::where($where)->count();
        $billList = BillModel::where($where)->order('id DESC')->page($page,$limit)->select();
        //print_r($billList);
        foreach ($billList as &$row){
            $row['title'] = $row['memo'];
            if($row['type'] == 'recharge'){
                $recharge = RechargeModel::where(['order_id'=>$row['link_id']])->find();
                if($recharge) {
                    if ($recharge['recharge_type'] == 1) {
                        $row['title'] = 'Recharge to blockchain';
                    } else {
                        $bank_card = substr($recharge['bank_card'], -4);
                        $row['title'] = 'Recharge to ' . $recharge['bank_name'] . '(' . $bank_card . ')';
                    }
                }
            }elseif($row['type'] == 'extract'){
                $extract = ExtractModel::where(['extract_sn'=>$row['link_id']])->find();
                if($extract) {
                    if ($extract['extract_type'] == 1) {
                        $row['title'] = 'Withdraw to blockchain';
                    } else {
                        $bank_card = $extract['bank_card'] ? substr($extract['bank_card'], -4) : '';
                        $row['title'] = 'Withdraw to ' . $extract['bank_name'] . '(' . $bank_card . ')';
                    }
                }
            }else{

            }
        }

        $this->success(__('请求成功'),['count'=>$count,'list'=>$billList]);
    }

    /**
     * 充值记录
     *
     * @ApiMethod (GET)
     *
     * @param string $status 状态
     * @param int $page 页码
     * @param int $limit 每页数量
     */
    public function recharge_log(){
        $status = $this->request->get('status','all');
        $page = $this->request->get('page',1);
        $limit = $this->request->get('limit',20);

        $where = ['mer_id' => $this->auth->mer_id];
        if($status != 'all'){
            $where['status'] = $status;
        }

        $count = RechargeModel::where($where)->count();
        $list = RechargeModel::where($where)->order('recharge_id desc')->page($page,$limit)->select();

        $data = [
            'total' => $count,
            'list' => $list
        ];

        $this->success(__('充值记录'), $data);
    }

    /**
     * 充值
     *
     * @ApiMethod (POST)
     *
     * @param float $price 充值金额
     * @param int $recharge_type 充值类型
     * @param string $currency_type 货币类型
     * @param string $image 充值凭证
     */
    public function recharge(){

        $price = $this->request->post('price');
        $recharge_type = $this->request->post('recharge_type');
        $currency_type = $this->request->post('currency_type', 'RMB');
        $image = $this->request->post('image','');


        if (!$price || !$recharge_type || !$currency_type) {
            $this->error(__('参数错误'));
        }

        $mer_id = $this->auth->mer_id;
        $order_id = RechargeModel::getOrderNo();
        $recharge_data = [
            'mer_id' => $mer_id,
            'order_id' => $order_id,
            'price' => $price,
            'recharge_type' => $recharge_type,
            'currency_type' => $currency_type,
            'paid' => 1, //模拟直接充值成功
            'pay_time' => time(),
            'image' => $image,
        ];
        if($recharge_type == 1){
            $network = $this->request->post('network');
            $blockchain = $this->request->post('blockchain');
            $recharge_data['network'] = $network;
            $recharge_data['blockchain'] = $blockchain;
        }else{
            $real_name = $this->request->post('real_name');
            $bank_card = $this->request->post('bank_card');
            $bank_name = $this->request->post('bank_name');
            $recharge_data['real_name'] = $real_name;
            $recharge_data['bank_card'] = $bank_card;
            $recharge_data['bank_name'] = $bank_name;
        }


        $result = false;
        $res = false;
        Db::startTrans();
        try {
            $result = RechargeModel::create($recharge_data);
            // if($result){
            //     //充值成功改变余额
            //     $res = MerchantModel::money($price,$mer_id,$order_id,1,'recharge',"商户充值$price");
            // }

            Db::commit();
        } catch (ValidateException|PDOException|Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        if ($result === false) {
            $this->error(__('充值请求失败'));
        }
        $this->success(__('充值请求已发送，请等待审核'),$result);
    }

    /**
     * 提现记录
     *
     * @ApiMethod (GET)
     *
     * @param string $status 状态
     * @param int $page 页码
     * @param int $limit 每页数量
     */
    public function extract_log(){
        $status = $this->request->get('status','all');
        $page = $this->request->get('page',1);
        $limit = $this->request->get('limit',20);

        $where = ['mer_id' => $this->auth->mer_id];
        if($status != 'all'){
            $where['status'] = $status;
        }

        $count = ExtractModel::where($where)->count();
        $list = ExtractModel::where($where)->order('extract_id desc')->page($page,$limit)->select();

        $data = [
            'total' => $count,
            'list' => $list
        ];

        $this->success(__('提现记录'), $data);
    }

    /**
     * 提现
     *
     * @ApiMethod (POST)
     *
     * @param float $extract_price 提现金额
     * @param int $extract_type 提现类型
     * @param string $currency_type 货币类型
     */
    public function extract(){
        if($this->request->isPost()) {
            $extract_price = $this->request->post('extract_price');
            $extract_type = $this->request->post('extract_type');
            $currency_type = $this->request->post('currency_type', 'RMB');

            if (!$extract_price || !$currency_type) {
                $this->error(__('参数错误'));
            }

            //判断余额是否足够
            $mer_money = $this->auth->mer_money;
            if($mer_money < $extract_price){
                $this->error(__('余额不足'));
            }

            $mer_id = $this->auth->mer_id;
            $extract_sn = ExtractModel::getOrderNo();

            $service_charge = config('site.service_charge');
            $service_charge_price = bcmul($extract_price,$service_charge,2);
            //扣除手续费
            $real_price = bcsub($extract_price,$service_charge_price,2);

            $extract_data = [
                'mer_id' => $mer_id,
                'extract_sn' => $extract_sn,
                'extract_price' => $extract_price,
                'fee' => $service_charge_price,
                'real_price' => $real_price,
                'extract_type' => $extract_type,
                'currency_type' => $currency_type,
                'mark' => __('提现金额')."$extract_price,".__('手续费')."$service_charge_price,".__('实际到账金额').$real_price
            ];

            if($extract_type == 1){
                $network = $this->request->post('network');
                $blockchain = $this->request->post('blockchain');
                $extract_data['network'] = $network;
                $extract_data['blockchain'] = $blockchain;
            }else{
                $real_name = $this->request->post('real_name');
                $bank_card = $this->request->post('bank_card');
                $bank_name = $this->request->post('bank_name');
                $extract_data['real_name'] = $real_name;
                $extract_data['bank_card'] = $bank_card;
                $extract_data['bank_name'] = $bank_name;
            }

            $result = false;
            $res = false;
            Db::startTrans();
            try {
                $result = ExtractModel::create($extract_data);

                if ($result) {
                    //提现申请提交 锁住提现金额
                    $res = MerchantModel::money($extract_price, $mer_id, $extract_sn, 0, 'extract', __('商户提现')."$extract_price");
                }

                Db::commit();
            } catch (ValidateException|PDOException|Exception $e) {
                Db::rollback();
                $this->error($e->getMessage());
            }
            if ($result === false || $res === false) {
                $this->error(__('提现请求失败'));
            }
            $this->success(__('提现请求已发送，请等待审核'), $result);
        }else {
            $mer = $this->auth->getMerinfo();
            $service_charge = config('site.service_charge');

            //银行卡
//            $card_list = \app\merchant\model\merchant\Card::where(['mer_id'=>$this->auth->mer_id])->select();

            $data = [
                'service_charge' => $service_charge,
                'balance' => $mer['mer_money'],
//                'card_list' => $card_list
            ];
            $this->success(__('请求成功'), $data);
        }
    }

    /**
     * 获取网络地址
     *
     */
    public function getBlockchain()
    {
        $model = new \app\admin\model\Network;
        $list = $model->where(['status'=>1])->select();
        $this->success(__('请求成功'), $list);
    }

    /**
     * 退款订单
     *
     * @ApiMethod (GET)
     *
     * @param int $page 页码
     * @param int $limit 每页数量
     */
    public function refund_order(){

        $page = $this->request->get('page',1);
        $limit = $this->request->get('limit',20);

        $where = ['mer_id' => $this->auth->mer_id];

        $list = OrderRefundModel::field('refund_sn,amount,status,receiving_status,refund_explain,createtime')->where($where)->page($page,$limit)->select();
        $this->success(__('请求成功'), $list);
    }

    /**
     * 财务报表
     *
     * @ApiMethod (GET)
     *
     * @param string $range 时间范围(all=全部，today=今天,yesterday=昨天,week=本周,month=本月)
     * @param int $page 页码
     * @param int $limit 每页数量
     */
    public function financial_report(){

        $range = $this->request->get('range', 'all');
        $page = $this->request->get('page',1);
        $limit = $this->request->get('limit',20);

        $where = ['mer_id' => $this->auth->mer_id];

        if ($range == 'today') {
            $startDate = strtotime(date('Y-m-d'));
            $endDate = time();
        } else if ($range == 'yesterday') {
            $startDate = strtotime(date('Y-m-d', strtotime('-1 day')));
            $endDate = strtotime(date('Y-m-d')) - 1;
        } else if ($range == 'week') {
            $startDate = strtotime(date('Y-m-d', strtotime('this week')));
            $endDate = time();
        } else if ($range == 'month') {
            $startDate = strtotime(date('Y-m-01'));
            $endDate = time();
        }else{  //全部
            $first = OrderModel::where($where)->order('order_id ASC')->find();
            $first_time = $first ? $first['createtime'] : date('Y-m-d');
            $startDate =  strtotime($first_time);
            $endDate = time();
        }

        $report = [];
        $all_profit = 0;
        for($date = $endDate; $date >= $startDate; $date = strtotime(date('Y-m-d',$date) . ' -1 day')) {
            $where['createtime'] = ['between', [strtotime(date('Y-m-d',$date) . ' -1 day'), $date]];
            // print_r($where);
            //总订单
            $total_order = OrderModel::where($where)->count();
            //取消订单
            $cancel_order = OrderModel::where($where)->where('status', -1)->count();
            //退款订单
            $refund_order = OrderModel::where($where)->where('status', -2)->count();

            //总销售额
            // $total_amount = OrderModel::where($where)->where('status', 'in',[1,2,3,4])->where('is_pick',1)->sum('total_price');
            //总成本
            // $total_cost = OrderModel::where($where)->where('status', 'in',[1,2,3,4])->where('is_pick',1)->sum('total_cost');
            //总利润
            $total_profit = OrderModel::where($where)->where('status', 'in',[1,2,3,4])->where('is_pick',1)->sum('total_profit');
            // $total_profit = bcsub($total_amount, $total_cost,2);
            $all_profit = bcadd($all_profit, $total_profit,2);
            $report[] = [
                'date' => date('Y-m-d',$date),
                'total_profit' => $total_profit,
                'total_order' => $total_order,
                'cancel_order' => $cancel_order,
                'refund_order' => $refund_order,
            ];
        }

        //待到账金额
        $total_unreceived = OrderModel::where('createtime','between',[$startDate,$endDate])->where('mer_id',$this->auth->mer_id)->where('status', 'in',[1,2])->where('is_pick',1)->sum('total_price');

        //分页
        $total = count($report);
        $report = array_slice($report, ($page - 1) * $limit, $limit);

        $data = [
            'total_unreceived' => number_format($total_unreceived,2),
            'all_profit' => $all_profit,
            'report' => $report,
            'total' => $total,
        ];

        $this->success(__('请求成功'), $data);
    }

    /**
     * 修改基础信息
     *
     * @ApiMethod (POST)
     * @param string $mer_avatar logo
     * @param string $mer_name 商户名称
     * @param string $mer_phone 商户电话
     * @param string $mer_address 商户地址
     * @param string $mer_info 商户介绍
     * @param string $mark 欢迎语
     */
    public function store()
    {
        $data = $this->request->post();
        $mer_id = $this->auth->mer_id;
        $merchant = MerchantModel::where(['mer_id' => $mer_id])->find();
        $merchant->allowField(true)->save($data);
        $this->success(__('修改成功'));
    }

    /**
     * 首页横幅
     *
     * @ApiMethod (POST)
     * @param string $image1 横幅1
     * @param string $image2 横幅2
     * @param string $image3 横幅3
     */
    public function store_banner()
    {
        $image1 = $this->request->post('image1');
        $image2 = $this->request->post('image2');
        $image3 = $this->request->post('image3');

        $images = [];
        if($image1) $images[] = $image1;
        if($image2) $images[] = $image2;
        if($image3) $images[] = $image3;
        //print_r($images);
        $banner = implode(',', $images);
        $merchant = $this->auth->getMerchant();
        $merchant->banner = $banner;
        $merchant->save();
        $this->success(__('修改成功'));
    }

    /**
     * 验证支付密码
     *
     * @ApiMethod (POST)
     * @param string $password_pay 支付密码
     */
    public function verify_pay()
    {
        $password_pay = $this->request->post('password_pay');
        $mer = $this->auth->getMerchant();
        if (!$mer['password_pay']) {
            // $this->error('请先设置支付密码');
            $this->error(__('请先设置支付密码'));
        }
        if (md5($password_pay) != $mer['password_pay']) {
            // $this->error('支付密码错误');
            $this->error(__('支付密码错误'));
        }

        $this->success(__('支付密码正确'));
    }

    /**
     * 语言列表
     *
     * @ApiMethod (GET)
     * @param int $page 页码
     * @param int $limit 每页数量
     */
    public function language_list(){
        $page = $this->request->get('page',1);
        $limit = $this->request->get('limit',20);

        $model = new \app\api\model\Lang;

        $count = $model->where(['status'=>1])->count();
        $list = $model->where(['status'=>1])->order('is_default DESC')->page($page,$limit)->select();


        $this->success(__('请求成功'),['count'=>$count,'list'=>$list]);
    }

    /**
     * 设置语言
     *
     * @ApiMethod (GET)
     * @param string $lang_id 语言id
     */
    public function set_language(){
        $lang = $this->request->get('lang');
        $this->success(__('请求成功'),$lang);
    }
}
