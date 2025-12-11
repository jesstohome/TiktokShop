<?php

namespace app\api\controller;

use app\common\controller\Api;
use app\common\library\Ems;
use app\common\library\Sms;
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
class User extends Api
{
    protected $noNeedLogin = ['login', 'mobilelogin', 'register', 'resetpwd', 'changeemail', 'changemobile', 'third', 'help_support', 'law', 'language_list', 'set_language'];
    protected $noNeedRight = '*';

    public function _initialize()
    {
        parent::_initialize();

        if (!Config::get('fastadmin.usercenter')) {
            $this->error(__('User center already closed'));
        }

    }

    /**
     * 会员中心
     */
    public function index()
    {
        $this->success('', ['welcome' => $this->auth->nickname]);
    }

    /**
     * 会员登录
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
            $data = ['userinfo' => $this->auth->getUserinfo()];
            $this->success(__('登录成功'), $data);
        } else {
            $this->error($this->auth->getError());
        }
    }

    /**
     * 手机验证码登录
     *
     * @ApiMethod (POST)
     * @param string $mobile  手机号
     * @param string $captcha 验证码
     */
    public function mobilelogin()
    {
        $mobile = $this->request->post('mobile');
        $captcha = $this->request->post('captcha');
        if (!$mobile || !$captcha) {
            $this->error(__('参数错误'));
        }
        if (!Validate::regex($mobile, "^1\d{10}$")) {
            $this->error(__('手机号不正确'));
        }
        if (!Sms::check($mobile, $captcha, 'mobilelogin')) {
            $this->error(__('Captcha is incorrect'));
        }
        $user = \app\common\model\User::getByMobile($mobile);
        if ($user) {
            if ($user->status != 'normal') {
                $this->error(__('Account is locked'));
            }
            //如果已经有账号则直接登录
            $ret = $this->auth->direct($user->id);
        } else {
            $ret = $this->auth->register($mobile, Random::alnum(), '', $mobile, []);
        }
        if ($ret) {
            Sms::flush($mobile, 'mobilelogin');
            $data = ['userinfo' => $this->auth->getUserinfo()];
            $this->success(__('登录成功'), $data);
        } else {
            $this->error($this->auth->getError());
        }
    }

    /**
     * 注册会员
     *
     * @ApiMethod (POST)
     * @param string $username 用户名
     * @param string $nickname 昵称
     * @param string $password 密码
     * @param string $email    邮箱
     * @param string $mobile   手机号
     */
    public function register()
    {
        $username = $this->request->post('username');
        $nickname = $this->request->post('nickname');
        $password = $this->request->post('password');
        $email = $this->request->post('email');
        $mobile = $this->request->post('mobile');
        if (!$username || !$password) {
            $this->error(__('参数错误'));
        }
        if ($email && !Validate::is($email, "email")) {
            $this->error(__('邮箱不正确'));
        }
        if (!$mobile /*&& !Validate::regex($mobile, "^1\d{10}$")*/) {
            $this->error(__('手机号不正确'));
        }
        if (!Validate::regex($mobile, "^\d{1,20}$")) {
            $this->error(__('手机号码长度不能超过20个字符'));
        }
        $ret = $this->auth->register($username, $nickname, $password, $email, $mobile, []);
        if ($ret) {
            $data = ['userinfo' => $this->auth->getUserinfo()];
            $this->success(__('注册成功'), $data);
        } else {
            $this->error($this->auth->getError());
        }
    }

    /**
     * 退出登录
     * @ApiMethod (POST)
     */
    public function logout()
    {
        if (!$this->request->isPost()) {
            $this->error(__('参数错误'));
        }
        $this->auth->logout();
        $this->success(__('登出成功'));
    }

    /**
     * 修改用户个人信息
     *
     * @ApiMethod (POST)
     * @param string $avatar   头像地址
     * @param string $username 用户名
     * @param string $nickname 昵称
     * @param string $email    邮箱
     * @param string $birthday 生日
     * @param string $gender 性别(1=Male,0=Female)
     * @param string $bio      个人简介
     */
    public function profile()
    {
        $user = $this->auth->getUser();
        $username = $this->request->post('username');
        $nickname = $this->request->post('nickname');
        $email = $this->request->post('email');
        $birthday = $this->request->post('birthday');
        $gender = $this->request->post('gender');
        $bio = $this->request->post('bio');
        $avatar = $this->request->post('avatar', '', 'trim,strip_tags,htmlspecialchars');
        if ($username) {
            $exists = \app\common\model\User::where('username', $username)->where('id', '<>', $this->auth->id)->find();
            if ($exists) {
                $this->error(__('用户名已存在'));
            }
            $user->username = $username;
        }
        if ($nickname) {
            $exists = \app\common\model\User::where('nickname', $nickname)->where('id', '<>', $this->auth->id)->find();
            if ($exists) {
                $this->error(__('昵称已存在'));
            }
            $user->nickname = $nickname;
        }
        if ($email) {
            $exists = \app\common\model\User::where('email', $email)->where('id', '<>', $this->auth->id)->find();
            if ($exists) {
                $this->error(__('邮箱已存在'));
            }
            $user->email = $email;
        }
        if ($avatar) {
            $user->avatar = $avatar;
        }
        if ($birthday) {
            $user->birthday = $birthday;
        }
        if ($gender) {
            $user->gender = $gender;
        }
        if ($bio) {
            $user->bio = $bio;
        }
        $user->save();
        $this->success();
    }

    /**
     * 修改邮箱
     *
     * @ApiMethod (POST)
     * @param string $email   邮箱
     * @param string $captcha 验证码
     */
    public function changeemail()
    {
        $user = $this->auth->getUser();
        $email = $this->request->post('email');
        $captcha = $this->request->post('captcha');
        if (!$email || !$captcha) {
            $this->error(__('参数错误'));
        }
        if (!Validate::is($email, "email")) {
            $this->error(__('邮箱不正确'));
        }
        if (\app\common\model\User::where('email', $email)->where('id', '<>', $user->id)->find()) {
            $this->error(__('邮箱已存在'));
        }
        $result = Ems::check($email, $captcha, 'changeemail');
        if (!$result) {
            $this->error(__('Captcha is incorrect'));
        }
        $verification = $user->verification;
        $verification->email = 1;
        $user->verification = $verification;
        $user->email = $email;
        $user->save();

        Ems::flush($email, 'changeemail');
        $this->success();
    }

    /**
     * 修改手机号
     *
     * @ApiMethod (POST)
     * @param string $mobile  手机号
     * @param string $captcha 验证码
     */
    public function changemobile()
    {
        $user = $this->auth->getUser();
        $mobile = $this->request->post('mobile');
        $captcha = $this->request->post('captcha');
        if (!$mobile || !$captcha) {
            $this->error(__('参数错误'));
        }
        if (!Validate::regex($mobile, "^1\d{10}$")) {
            $this->error(__('手机号不正确'));
        }
        if (\app\common\model\User::where('mobile', $mobile)->where('id', '<>', $user->id)->find()) {
            $this->error(__('Mobile already exists'));
        }
        $result = Sms::check($mobile, $captcha, 'changemobile');
        if (!$result) {
            $this->error(__('Captcha is incorrect'));
        }
        $verification = $user->verification;
        $verification->mobile = 1;
        $user->verification = $verification;
        $user->mobile = $mobile;
        $user->save();

        Sms::flush($mobile, 'changemobile');
        $this->success();
    }

    /**
     * 第三方登录
     *
     * @ApiMethod (POST)
     * @param string $platform 平台名称
     * @param string $code     Code码
     */
    public function third()
    {
        $url = url('user/index');
        $platform = $this->request->post("platform");
        $code = $this->request->post("code");
        $config = get_addon_config('third');
        if (!$config || !isset($config[$platform])) {
            $this->error(__('参数错误'));
        }
        $app = new \addons\third\library\Application($config);
        //通过code换access_token和绑定会员
        $result = $app->{$platform}->getUserInfo(['code' => $code]);
        if ($result) {
            $loginret = \addons\third\library\Service::connect($platform, $result);
            if ($loginret) {
                $data = [
                    'userinfo'  => $this->auth->getUserinfo(),
                    'thirdinfo' => $result
                ];
                $this->success(__('登录成功'), $data);
            }
        }
        $this->error(__('Operation failed'), $url);
    }

    /**
     * 重置密码
     *
     * @ApiMethod (POST)
     * @param string $newpassword 新密码
     */
    public function resetpwd()
    {
        $mobile = $this->request->post("mobile",'');
        $oldpassword = $this->request->post("oldpassword");
        $newpassword = $this->request->post("newpassword");
        if (!$newpassword) {
            $this->error(__('参数错误'));
        }
        //验证Token
        if (!Validate::make()->check(['newpassword' => $newpassword], ['newpassword' => 'require|regex:\S{6,30}'])) {
            $this->error(__('密码长度必须在6-30位之间，不能包含空格'));
        }
        if(!$mobile) {
            $ret = $this->auth->changepwd($newpassword, $oldpassword, false);
        }else{
            $userModel = new \app\common\model\User;
            $user = $userModel->where(['mobile' => $mobile])->find();
            if(!$user){
                $this->error(__('用户不存在'));
            }
            $salt = Random::alnum();
            $newpassword = $this->auth->getEncryptPassword($newpassword, $salt);
            $ret = $user->save(['loginfailure' => 0, 'password' => $newpassword, 'salt' => $salt]);
        }
        if ($ret) {
            $this->success(__('重置密码成功'));
        } else {
            $this->error($this->auth->getError());
        }
    }

    /**
     * 设置支付密码
     *
     * @ApiMethod (POST)
     * @param string $newpassword 新密码
     */
    public function resetPay()
    {
        $newpassword = $this->request->post("newpassword");
        $oldpassword = $this->request->post("oldpassword");
        $is_verify = $this->request->post("is_verify");
        if (!$newpassword) {
            $this->error(__('参数错误'));
        }

        $user = $this->auth->getUser();

        if($is_verify){
            if($user->password_pay != md5($oldpassword)){
                $this->error(__('旧密码错误'));
            }
        }

        if($user->password_pay == md5($newpassword)){
            $this->error(__('不能与旧密码重复'));
        }

        $user->password_pay = md5($newpassword);
        $ret = $user->save();

        if ($ret) {
            $this->success(__('设置支付密码成功'));
        } else {
            $this->error($this->auth->getError());
        }
    }

    /**
     * 获取用户信息
     *
     */
    public function getUserInfo()
    {
        $user_id = $this->auth->id;
        $userModel = new \app\common\model\User;
        //用户信息
        $userInfo = $userModel->field('id,username,nickname,avatar,email,mobile,gender,money,follow,like,password_pay,lang_id')->where(['id' => $user_id])->find();
        $userInfo['have_pay'] = $userInfo['password_pay'] ? 1 : 0;
        unset($userInfo['password_pay']);

        $orderModel = new \app\api\model\Order;
        //待收货订单数
        $unreceived = $orderModel->where(['user_id'=>$user_id,'status'=>2])->count();
        $userInfo['order_unreceived'] = $unreceived;
        //待支付订单数
        $unpaid = $orderModel->where(['user_id'=>$user_id,'status'=>0])->count();
        $userInfo['order_unpaid'] = $unpaid;

        //默认地址
        $addressModel = new \app\api\model\Address;
        // $areaModel = new \app\api\model\Area;
        $address = $addressModel->where(['user_id'=>$user_id,'is_default'=>1])->find();
        //省市区
        // $address['province_name'] = $areaModel->where(['id'=>$address['province_id']])->value('name');
        // $address['city_name'] = $areaModel->where(['id'=>$address['city_id']])->value('name');
        // $address['area_name'] = $areaModel->where(['id'=>$address['area_id']])->value('name');
        $userInfo['address'] = $address;

        //默认语言
        $langModel = new \app\api\model\Lang;
        if($userInfo['lang_id']){
            $lang = $langModel->where(['id'=>$userInfo['lang_id']])->find();
        }else{
            $lang = $langModel->where(['is_default'=>1])->find();
        }
        $userInfo['lang'] = $lang;

        $this->success(__('用户信息'), $userInfo);
    }


    /**
     * 关注商户列表
     *
     * @ApiMethod (GET)
     * @param int $page 页码
     * @param int $limit 每页数量
     */
    public function follow_list(){
        $page = $this->request->get('page',1);
        $limit = $this->request->get('limit',10);
        $user_id = $this->auth->id;

        $followModel = new \app\admin\model\UserFollow;

        $count = $followModel->where(['user_id'=>$user_id,'status'=>1])->count();
        $followList = $followModel->where(['user_id'=>$user_id,'status'=>1])->order('id DESC')->page($page,$limit)->select();

        foreach ($followList as $row){
            //商户信息
            $merModel = new \app\api\model\Merchant;
            $merInfo = $merModel->field('mer_id,mer_name,mer_avatar,follow_count')->where(['mer_id'=>$row['mer_id']])->find();
            $row['merchant'] = $merInfo;
        }

        $this->success(__('请求成功'),['count'=>$count,'list'=>$followList]);
    }

    /**
     * 喜欢商品列表
     *
     * @ApiMethod (GET)
     * @param int $page 页码
     * @param int $limit 每页数量
     */
    public function like_list(){
        $page = $this->request->get('page',1);
        $limit = $this->request->get('limit',10);
        $user_id = $this->auth->id;

        $likeModel = new \app\admin\model\UserLike;

        $count = $likeModel->where(['user_id'=>$user_id,'status'=>1])->count();
        $likeList = $likeModel->where(['user_id'=>$user_id,'status'=>1])->order('id DESC')->page($page,$limit)->select();

        foreach ($likeList as $row){
            //商品信息
            $merModel = new \app\admin\model\merchant\Product;
            $merProduct = $merModel->field('id,mer_id,product_id,sales,click')->where(['product_id'=>$row['product_id'],'mer_id'=>$row['mer_id']])->find();

            $productModel = new \app\api\model\Product;
            $productInfo = $productModel->field('product_id,code,title,title_en,image,sales_price,cost_price,market_price')->where(['product_id'=>$row['product_id']])->find();

            $productInfo = array_merge($merProduct->toArray(),$productInfo->toArray());

            $row['product'] = $productInfo;
        }

        $this->success(__('请求成功'),['count'=>$count,'list'=>$likeList]);
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
     * 充值
     *
     * @ApiMethod (POST)
     * @param int $recharge_type 充值类型 0:银行卡 1:链上充值
     * @param float $price 充值金额
     */
    public function recharge(){
        $recharge_type = $this->request->post('recharge_type',0);
        $price = $this->request->post('price',0.00);
        $image = $this->request->post('image','');
        $user_id = $this->auth->id;

        $model = new \app\admin\model\UserRecharge;

        $data = [
            'order_sn'=>$model->getOrderNo(),
            'user_id'=>$user_id,
            'recharge_type'=>$recharge_type,
            'price'=>$price,
            'image' => $image,
        ];
        if($recharge_type == 1){
            $network = $this->request->post('network');
            $blockchain = $this->request->post('blockchain');
            $data['network'] = $network;
            $data['blockchain'] = $blockchain;
        }else{
            $real_name = $this->request->post('real_name');
            $bank_card = $this->request->post('bank_card');
            $bank_name = $this->request->post('bank_name');
            $data['real_name'] = $real_name;
            $data['bank_card'] = $bank_card;
            $data['bank_name'] = $bank_name;
        }
        //模拟充值成功
        $data['paid'] = 1;
        $data['pay_time'] = time();
        $model->save($data);

        // if($res) {
        //     //增加余额
        //     $userModel = new \app\common\model\User;
        //     $userModel->money($price, $user_id, $data['order_sn'],1,'recharge',"User recharge for $$price");
        // }

        $this->success('充值请求已发送，请等待审核');
    }

    /**
     * 充值记录
     *
     * @ApiMethod (GET)
     *
     * @param int $page 页码
     * @param int $limit 每页数量
     */
    public function recharge_log(){
        $status = $this->request->get('status','all');
        $page = $this->request->get('page',1);
        $limit = $this->request->get('limit',20);

        $where = ['user_id' => $this->auth->id];

        if($status != 'all'){
            $where['status'] = $status;
        }

        $rechargeModel = new \app\admin\model\UserRecharge;

        $count = $rechargeModel->where($where)->count();
        $list = $rechargeModel->where($where)->order('recharge_id desc')->page($page,$limit)->select();
        foreach ($list as &$row){
            $row['createtime'] = date('Y-m-d H:i:s',$row['createtime']);
        }

        $data = [
            'total' => $count,
            'list' => $list
        ];

        $this->success(__('请求成功'), $data);
    }

    /**
     * 提现
     *
     */
    public function extract(){
        if($this->request->isPost()){
            $extract_type = $this->request->post('extract_type',0);
            $price = $this->request->post('price',0.00); //提现金额
            $user_id = $this->auth->id;

            //判断余额是否足够
            $money = $this->auth->money;
            if($money < $price){
                $this->error(__('Insufficient balance'));
            }

            $service_charge = config('site.service_charge');
            $service_charge_price = bcmul($price,$service_charge,2);
            //扣除手续费
            $real_price = bcsub($price,$service_charge_price,2);

            $model = new \app\admin\model\UserExtract;
            $data = [
                'extract_sn'=>$model->getOrderNo(),
                'user_id'=>$user_id,
                'extract_type'=>$extract_type,
                'extract_price'=>$price,
                'fee' => $service_charge_price,
                'real_price' => $real_price,
                'mark' => __('提现金额')." $price,".__('手续费')." $service_charge_price,".__('实际到账金额')." $real_price"
            ];
            if($extract_type == 1){
                $network = $this->request->post('network');
                $blockchain = $this->request->post('blockchain');
                $data['network'] = $network;
                $data['blockchain'] = $blockchain;
            }else{
                $real_name = $this->request->post('real_name');
                $bank_card = $this->request->post('bank_card');
                $bank_name = $this->request->post('bank_name');
                $data['real_name'] = $real_name;
                $data['bank_card'] = $bank_card;
                $data['bank_name'] = $bank_name;
            }

            // $data['status'] = 1;
            $res = $model->save($data);
            //模拟提现成功
            if($res) {
                //减少余额
                $userModel = new \app\common\model\User;
                $userModel->money($price, $user_id, $data['extract_sn'],0,'extract',__('用户提现')." $price");
            }
            $this->success('提现请求已发送，请等待审核');
        }else {
            $user = $this->auth->getUserinfo();
            $service_charge = config('site.service_charge');

            //银行卡
            $card_list = \app\api\model\UserCard::where(['user_id'=>$this->auth->id])->select();

            $data = [
                'service_charge' => $service_charge,
                'balance' => $user['money'],
                'card_list' => $card_list
            ];
            $this->success(__('请求成功'), $data);
        }
    }

    /**
     * 提现记录
     *
     * @ApiMethod (GET)
     *
     * @param int $page 页码
     * @param int $limit 每页数量
     */
    public function extract_log(){
        $status = $this->request->get('status','all');
        $page = $this->request->get('page',1);
        $limit = $this->request->get('limit',20);

        $where = ['user_id' => $this->auth->id];
        if($status != 'all'){
            $where['status'] = $status;
        }

        $extractModel = new \app\admin\model\UserExtract;

        $count = $extractModel->where($where)->count();
        $list = $extractModel->where($where)->order('extract_id desc')->page($page,$limit)->select();
        foreach ($list as &$row){
            $row['createtime'] = date('Y-m-d H:i:s',$row['createtime']);
        }

        $data = [
            'total' => $count,
            'list' => $list
        ];

        $this->success(__('请求成功'), $data);
    }

    /**
     * 我的资金记录
     *
     * @ApiMethod (GET)
     * @param int $type 类型(all=全部，recharge=充值，extract=提现)
     * @param int $page 页码
     * @param int $limit 每页数量
     */
    public function bill(){
        $type = $this->request->get('type','all');
        $page = $this->request->get('page',1);
        $limit = $this->request->get('limit',20);
        $user_id = $this->auth->id;

        $model = new \app\api\model\UserBill;

        $where = ['user_id'=>$user_id];

        if($type != 'all'){
            $where['type'] = $type;
        }else{
            $where['type'] = ['in',['recharge','extract']];
        }

        $count = $model->where($where)->count();
        $billList = $model->where($where)->order('id DESC')->page($page,$limit)->select();

        foreach ($billList as &$row){
            if($row['type'] == 'recharge'){
                $recharge = \app\admin\model\UserRecharge::where(['order_sn'=>$row['link_id']])->find();
                if($recharge['recharge_type'] == 1){
                    $row['title'] = __('充值到区块链');
                }else{
                    $bank_card = substr($recharge['bank_card'], -4);
                    $row['title'] = __('充值到').' '.$recharge['bank_name'].'('.$bank_card.')';
                }
            }else{
                $extract = \app\admin\model\UserExtract::where(['extract_sn'=>$row['link_id']])->find();
                if($extract['extract_type'] == 1){
                    $row['title'] = __('提现到区块链');
                }else{
                    $bank_card = substr($extract['bank_card'], -4);
                    $row['title'] = __('提现到').' '.$extract['bank_name'].'('.$bank_card.')';
                }
            }
        }

        $this->success(__('请求成功'),['count'=>$count,'list'=>$billList]);
    }

    /**
     * 我的订单
     *
     * @ApiMethod (GET)
     * @param int $status 状态
     * @param int $page 页码
     * @param int $limit 每页数量
     */
    public function order_list(){
        $status = $this->request->get('status','all');
        $page = $this->request->get('page',1);
        $limit = $this->request->get('limit',20);
        $user_id = $this->auth->id;

        $orderModel = new \app\api\model\Order;

        $where = ['user_id'=>$user_id];

        if($status == 'all'){
            //$where['status'] = $status;
        }elseif($status == '-4'){
            //$where['status'] = ['in',[-2,-3]];
            $where['refund_status'] = ['in',[1,2]];
        }else{
            $where['refund_status'] = 0;
            $where['status'] = $status;
        }


        $count = $orderModel->where($where)->count();
        $orderList = $orderModel->field('order_id,order_sn,total_num,total_price,total_cost,status,refund_status')->where($where)->order('order_id DESC')->page($page,$limit)->select();

        foreach ($orderList as $row){
            //订单商品
            $orderProductModel = new \app\api\model\OrderProduct;
            $productModel = new \app\api\model\Product;
            $orderProduct = $orderProductModel->field('order_product_id,order_id,product_id,spec,product_num,cost,product_price,total_price')->where(['order_id' => $row['order_id']])->select();
            foreach ($orderProduct as &$v) {
                $goods = $productModel->field('title,title_en,image')->where(['product_id'=>$v['product_id']])->find();
                $v['title'] = $goods['title'];
                $v['title_en'] = $goods['title_en'];
                $v['image'] = $goods['image'];
            }
            $row['product'] = $orderProduct;
        }

        $this->success(__('请求成功'),['count'=>$count,'list'=>$orderList]);
    }

    /**
     * 未支付订单去支付
     *
     * @return json
     */
    public function pay()
    {
        $order_id = $this->request->post('order_id');  //订单id

        $orderModel = new \app\api\model\Order;

        $order = $orderModel->where(['order_id' => $order_id])->find();
        if (!$order) {
            $this->error(__('订单不存在'));
        }
        if ($order['status'] == 1 || $order['paid'] == 1)  {
            $this->error(__('订单已支付'));
        }

        //余额是否充足
        if ($this->auth->money < $order['total_price']) {
            $this->error(__('余额不足，请先充值'));
        }

        //扣余额
        $user_id = $this->auth->id;
        $userModel = new \app\common\model\User;
        $userModel->money($order['total_price'],$user_id,$order['order_sn'],0,'order',__('购买商品'));

        Db::startTrans();
        try {
            // 修改订单组状态已支付
            $order->paid = 1;
            $order->pay_time = time();
            $order->pay_type = 0;
            $order->status = 1;
            $order->save();

            Db::commit();

        } catch (\Exception $e) {
            Db::rollback();
            $this->error($e->getMessage(), false);
        }

        $this->success(__('支付成功'));
    }

    /**
     * 我的地址
     *
     * @ApiMethod (GET)
     * @param int $page 页码
     * @param int $limit 每页数量
     */
    public function address_list(){
        $page = $this->request->get('page',1);
        $limit = $this->request->get('limit',20);
        $user_id = $this->auth->id;

        $model = new \app\api\model\Address;

        $where = ['user_id'=>$user_id];

        $count = $model->with(['province','city','area'])->where($where)->count();
        $list = $model->with(['province','city','area'])->where($where)->order('is_default DESC,address_id DESC')->page($page,$limit)->select();

        foreach ($list as $row){
            $row->getRelation('province')->visible(['id','name']);
            $row->getRelation('city')->visible(['id','name']);
            $row->getRelation('area')->visible(['id','name']);
        }

        $this->success(__('请求成功'),['count'=>$count,'list'=>$list]);
    }

    /**
     * 省市区列表
     *
     * @ApiMethod (GET)
     */
    public function area(){
        $province_id = $this->request->get('province_id',0);
        $city_id = $this->request->get('city_id',0);

        $model = new \app\api\model\Area;

        $where['pid'] = 0;
        if($province_id && $city_id){
            $where['pid'] = $city_id;
        }elseif ($province_id && !$city_id){
            $where['pid'] = $province_id;
        }
        $list = $model->where($where)->select();
        $this->success(__('请求成功'),$list);
    }


    /**
     * 添加地址
     *
     * @ApiMethod (POST)
     */
    public function address_add(){
        $user_id = $this->auth->id;
        $name = $this->request->post('name');
        $mobile = $this->request->post('mobile');
        $country = $this->request->post('country');
        $detail = $this->request->post('detail');
        $province_id = $this->request->post('province_id',0);
        $city_id = $this->request->post('city_id',0);
        $area_id = $this->request->post('area_id',0);
        $tag = $this->request->post('tag','');
        $is_default = $this->request->post('is_default');
        $address_id = $this->request->post('address_id' ,0);

        if (!Validate::regex($mobile, "^\d{1,20}$")) {
            $this->error(__('手机号码长度不能超过20个字符'));
        }

        $model = new \app\api\model\Address;

        $data = [
            'user_id' => $user_id,
            'name' => $name,
            'mobile' => $mobile,
            'country' => $country,
            'detail' => $detail,
            'province_id' => $province_id,
            'city_id' => $city_id,
            'area_id' => $area_id,
            'tag' => $tag,
            'is_default' => $is_default,
        ];

        $result = false;
        Db::startTrans();
        try {
            if($is_default == 1){ //新增地址设置默认，其他地址改为非默认
                $address = $model->where(['user_id'=>$user_id,'is_default'=>1])->find();
                if($address){
                    $address->is_default = 0;
                    $address->save();
                }
            }

            if($address_id){
                $address = $model->where(['address_id'=>$address_id])->find();
                $result = $address->allowField(true)->save($data);
            }else{
                $result = $model->allowField(true)->save($data);
            }

            Db::commit();
        } catch (ValidateException|PDOException|Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        if ($result === false) {
            $this->error(__('提交失败'));
        }
        $this->success(__('提交成功'));
    }

    /**
     * 地址信息
     *
     * @ApiMethod (GET)
     */
    public function address(){
        $address_id = $this->request->get('address_id' );
        $model = new \app\api\model\Address;


        $address = $model->where(['address_id'=>$address_id])->find();
        if(!$address){
            $this->error(__('地址不存在'));
        }

        $this->success(__('请求成功'),$address);
    }

    /**
     * 地址信息
     *
     * @ApiMethod (GET)
     */
    public function address_del(){
        $address_id = $this->request->get('address_id' );
        $model = new \app\api\model\Address;

        $address = $model->where(['address_id'=>$address_id])->find();
        if(!$address){
            $this->error('The address does not exist');
        }
        $res = $address->delete();
        if(!$res){
            $this->error(__('删除失败'));
        }else{
            $this->success(__('删除成功'));
        }
    }

    /**
     * 设置默认地址
     *
     * @ApiMethod (POST)
     */
    public function address_set_default(){
        $address_id = $this->request->post('address_id');
        $user_id = $this->auth->id;
        $model = new \app\api\model\Address;
        $model->where(['user_id'=>$user_id,'is_default'=>1])->update(['is_default'=>0]); //默认地址改为非默认
        $model->where(['address_id'=>$address_id])->update(['is_default'=>1]); //设置当前地址为默认
        $this->success(__('操作成功'));
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

        if($this->auth->id){
            $user = $this->auth->getUser();
            if($user['lang_id']) {
                foreach ($list as &$row) {
                    if ($row['id'] == $user['lang_id']) {
                        $row['is_default'] = 1;
                    }else{
                        $row['is_default'] = 0;
                    }
                }
            }
        }

        $this->success(__('请求成功'),['count'=>$count,'list'=>$list]);
    }

    /**
     * 设置语言
     *
     * @ApiMethod (GET)
     * @param string $lang_id 语言id
     */
    public function set_language(){
        $lang_id = $this->request->get('lang_id');
        $lang = $this->request->get('lang');

        // \app\api\model\Lang::where(['status'=>1])->update(['is_default'=>0]);
        // \app\api\model\Lang::where(['id'=>$lang_id])->update(['is_default'=>1]);
        if($this->auth->id){
            $user = $this->auth->getUser();
            $user->lang_id = $lang_id;
            $user->save();
        }

        $this->success(__('设置成功'),$lang);
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
        $user = $this->auth->getUser();

        if (!$user['password_pay']) {
            $this->error(__('请先设置支付密码'));
        }
        if (md5($password_pay) != $user['password_pay']) {
            $this->error(__('支付密码错误'));
        }

        $this->success(__('支付密码正确'));
    }

    /**
     * 帮助和支持
     *
     * @ApiMethod (GET)
     */
    public function help_support()
    {
        $model = new \app\admin\model\general\News;

        $list = $model->where(['status'=>1])->order('createtime DESC')->select();
        $this->success(__('请求成功'),$list);
    }

    /**
     * 法律和政策
     *
     * @ApiMethod (GET)
     */
    public function law()
    {
        $law = config('site.law');
        $this->success(__('请求成功'),$law);
    }


    public function get_shops_kf(){
        
        $user_id = $this->auth->id;
        $userModel = new \app\common\model\User;
          $mer_id = $this->request->get('pid');
        //用户信息
        $userInfo = $userModel->field('id,username,nickname,avatar,email,mobile,gender,money,follow,like,password_pay,lang_id')->where(['id' => $user_id])->find();
        
        
             //商户信息
        $merchantModel = new MerchantModel;
        $mer = $merchantModel->field('mer_id,mer_name,mer_avatar,follow_count,good_rate,visit,mer_phone')->where(['mer_id'=>$mer_id])->find();
        
        $mer_phone = $mer['mer_phone'];
        // $mer_phone = '13012345678';
          $time = time();
        $data = [];
        $data['uu'] = $mer_phone;
        $data['nn'] = $mer['mer_name'];
        $data['tt'] = $time;
        $data['ss'] = md5($mer_phone.$time.'asd123');
          $r = file_get_contents('http://127.0.0.3/service/login/shops_kf?'.http_build_query($data));
          $r = json_decode($r,true)['data'];
        //   dump($r);die;
        $data['visiter_id'] = $r['visiter_id'].$userInfo['id'];
        $data['visiter_name'] = $r['visiter_name'].$userInfo['nickname'];
        $data['avatar'] = $r['avatar'].$userInfo['avatar'];
       
        
        $url = $r['url'].$data['visiter_id'].$data['visiter_name'].$data['avatar'].$r['groupid'].$r['business_id'];
          
        //   dump($url);die;
         $this->success(__('请求成功'), $url);
    }
}
