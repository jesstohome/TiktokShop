<?php

namespace app\merchant\library;

use app\merchant\model\merchant\Merchant;
use fast\Random;
use think\Config;
use think\Db;
use think\Exception;
use think\Hook;
use think\Request;
use think\Validate;

class Auth
{
    protected static $instance = null;
    protected $_error = '';
    protected $_logined = false;

    protected $_mer = null;
    protected $_token = '';
    //Token默认有效时长
    protected $keeptime = 2592000;
    protected $requestUri = '';
    protected $rules = [];
    //默认配置
    protected $config = [];
    protected $options = [];
//    protected $allowFields = ['id', 'username', 'nickname', 'mobile', 'avatar', 'score'];
    protected $allowFields = ['mer_id', 'mer_name','mer_avatar', 'real_name', 'mer_phone', 'mer_email', 'mer_money', 'mer_level'];

    public function __construct($options = [])
    {
        if ($config = Config::get('merchant')) {
            $this->config = array_merge($this->config, $config);
        }
        $this->options = array_merge($this->config, $options);
    }

    /**
     *
     * @param array $options 参数
     * @return Auth
     */
    public static function instance($options = [])
    {
        if (is_null(self::$instance)) {
            self::$instance = new static($options);
        }

        return self::$instance;
    }

    /**
     * 获取Merchant模型
     * @return Merchant
     */
    public function getMerchant()
    {
        return $this->_mer;
    }

    /**
     * 兼容调用merchant模型的属性
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->_mer ? $this->_mer->$name : null;
    }

    /**
     * 兼容调用merchant模型的属性
     */
    public function __isset($name)
    {
        return isset($this->_mer) ? isset($this->_mer->$name) : false;
    }

    /**
     * 根据Token初始化
     *
     * @param string $token Token
     * @return boolean
     */
    public function init($token)
    {
        if ($this->_logined) {
            return true;
        }
        if ($this->_error) {
            return false;
        }
        $data = Token::get($token);
        if (!$data) {
            return false;
        }
        $mer_id = intval($data['mer_id']);
        if ($mer_id > 0) {
            $merchant = Merchant::get(['mer_id' => $mer_id]);
            if (!$merchant) {
                $this->setError('Account not exist');
                return false;
            }
            // if ($merchant['status'] != 1) {
            //     $this->setError('Account is locked');
            //     return false;
            // }
            $this->_mer = $merchant;
            $this->_logined = true;
            $this->_token = $token;

            //初始化成功的事件
            Hook::listen("merchant_init_successed", $this->_mer);

            return true;
        } else {
            $this->setError('You are not logged in');
            return false;
        }
    }

    /**
     * 注册商户
     *
     * @param string $mer_name   商户名
     * @param string $password   密码
     * @param string $mer_email  邮箱
     * @param string $mer_phone  手机号
     * @param array  $extend     扩展参数
     * @return boolean
     */
    public function register($mer_name, $password, $mer_email = '', $mer_phone = '', $extend = [])
    {
        // 检测用户名、昵称、邮箱、手机号是否存在
        if (Merchant::getByMername($mer_name)) {
            $this->setError('商户名已存在');
            return false;
        }
        if ($mer_email && Merchant::getByEmail($mer_email)) {
            $this->setError('邮箱已存在');
            return false;
        }
        if ($mer_phone && Merchant::getByPhone($mer_phone)) {
            $this->setError('手机号已存在');
            return false;
        }

        $ip = request()->ip();
        $time = time();

        $data = [
            'mer_name' => $mer_name,
            'password' => $password,
            'mer_email'    => $mer_email,
            'mer_phone'   => $mer_phone,
            'mer_level'    => 0,
        ];
        $params = array_merge($data, [
            'salt'      => Random::alnum(),
            'logintime' => $time,
            'loginip'   => $ip,
            'prevtime'  => $time,
            'status'    => 0
        ]);
        $params['password'] = $this->getEncryptPassword($password, $params['salt']);
        $params = array_merge($params, $extend);

        //账号注册时需要开启事务,避免出现垃圾数据
        Db::startTrans();
        try {
            $mer = Merchant::create($params, true);

            $this->_mer = Merchant::get(['mer_id' => $mer->mer_id]);

            //设置Token
            $this->_token = Random::uuid();
            Token::set($this->_token, $mer->mer_id, $this->keeptime);

            //设置登录状态
            $this->_logined = true;

            //注册成功的事件
            Hook::listen("merchant_register_successed", $this->_mer, $data);
            Db::commit();
        } catch (Exception $e) {
            $this->setError($e->getMessage());
            Db::rollback();
            return false;
        }
        return true;
    }

    /**
     * 商户登录
     *
     * @param string $account  账号,用户名、邮箱、手机号
     * @param string $password 密码
     * @return boolean
     */
    public function login($account, $password)
    {
        $field = Validate::is($account, 'mer_email') ? 'mer_email' : 'mer_phone';
        $mer = Merchant::get([$field => $account]);
        if (!$mer) {
            $this->setError('账号不存在，请先注册');
            return false;
        }

        // if ($mer->status != 1) {
        //     $this->setError('账号还未启用，请联系客服');
        //     return false;
        // }

        if ($mer->password != $this->getEncryptPassword($password, $mer->salt)) {
            $this->setError('密码不正确');
            return false;
        }

        //直接登录会员
        return $this->direct($mer->mer_id);
    }

    /**
     * 退出
     *
     * @return boolean
     */
    public function logout()
    {
        if (!$this->_logined) {
            $this->setError('你当前还未登录');
            return false;
        }
        //设置登录标识
        $this->_logined = false;
        //删除Token
        Token::delete($this->_token);
        //退出成功的事件
        Hook::listen("merchant_logout_successed", $this->_mer);
        return true;
    }

    /**
     * 修改密码
     * @param string $newpassword       新密码
     * @param string $oldpassword       旧密码
     * @param bool   $ignoreoldpassword 忽略旧密码
     * @return boolean
     */
    public function changepwd($newpassword, $oldpassword = '', $ignoreoldpassword = false)
    {
        if (!$this->_logined) {
            $this->setError('你当前还未登录');
            return false;
        }
        //判断旧密码是否正确
        if ($this->_mer->password == $this->getEncryptPassword($oldpassword, $this->_mer->salt) || $ignoreoldpassword) {
            Db::startTrans();
            try {
                $salt = Random::alnum();
                $newpassword = $this->getEncryptPassword($newpassword, $salt);
                $this->_mer->save(['password' => $newpassword, 'salt' => $salt]);

                Token::delete($this->_token);
                //修改密码成功的事件
                Hook::listen("merchant_changepwd_successed", $this->_mer);
                Db::commit();
            } catch (Exception $e) {
                Db::rollback();
                $this->setError($e->getMessage());
                return false;
            }
            return true;
        } else {
            $this->setError('密码不正确');
            return false;
        }
    }

    /**
     * 修改支付密码
     * @param string $new               新密码
     * @param string $old               旧密码
     * @param bool   $ignoreoldpay      忽略旧密码
     * @return boolean
     */
    public function changepay($new, $old = '', $ignoreoldpay = false)
    {
        if (!$this->_logined) {
            $this->setError('You are not logged in');
            return false;
        }
        //判断旧密码是否正确
        if ($this->_mer->password_pay == md5($old) || $ignoreoldpay) {
            Db::startTrans();
            try {
                $password_pay = md5($new);
                $this->_mer->save(['password_pay' => $password_pay]);

                Token::delete($this->_token);
                //修改支付密码成功的事件
                Hook::listen("merchant_changepay_successed", $this->_mer);
                Db::commit();
            } catch (Exception $e) {
                Db::rollback();
                $this->setError($e->getMessage());
                return false;
            }
            return true;
        } else {
            $this->setError('密码不正确');
            return false;
        }
    }

    /**
     * 直接登录账号
     * @param int $mer_id
     * @return boolean
     */
    public function direct($mer_id)
    {
        $mer = Merchant::get(['mer_id' => $mer_id]);
        // if ($mer->status != 1) {
        //     $this->setError('账号还未启用，请联系客服');
        //     return false;
        // }
        if ($mer) {
            Db::startTrans();
            try {
                $ip = request()->ip();
                $time = time();

                $mer->prevtime = $mer->logintime;
                //记录本次登录的IP和时间
                $mer->loginip = $ip;
                $mer->logintime = $time;

                $mer->save();

                $this->_mer = $mer;

                $this->_token = Random::uuid();
                Token::set($this->_token, $mer->mer_id, $this->keeptime);

                $this->_logined = true;

                //登录成功的事件
                Hook::listen("merchant_login_successed", $this->_mer);
                Db::commit();
            } catch (Exception $e) {
                Db::rollback();
                $this->setError($e->getMessage());
                return false;
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * 检测是否是否有对应权限
     * @param string $path   控制器/方法
     * @param string $module 模块 默认为当前模块
     * @return boolean
     */
    public function check($path = null, $module = null)
    {
        if (!$this->_logined) {
            return false;
        }

        $ruleList = $this->getRuleList();
        $rules = [];
        foreach ($ruleList as $k => $v) {
            $rules[] = $v['name'];
        }
        $url = ($module ? $module : request()->module()) . '/' . (is_null($path) ? $this->getRequestUri() : $path);
        $url = strtolower(str_replace('.', '/', $url));
        return in_array($url, $rules);
    }

    /**
     * 判断是否登录
     * @return boolean
     */
    public function isLogin()
    {
        if ($this->_logined) {
            return true;
        }
        return false;
    }

    /**
     * 获取当前Token
     * @return string
     */
    public function getToken()
    {
        return $this->_token;
    }

    /**
     * 获取商户基本信息
     */
    public function getMerinfo()
    {
        $data = $this->_mer->toArray();
        $allowFields = $this->getAllowFields();
        $merinfo = array_intersect_key($data, array_flip($allowFields));
        $merinfo = array_merge($merinfo, Token::get($this->_token));
        return $merinfo;
    }

    /**
     * 获取当前请求的URI
     * @return string
     */
    public function getRequestUri()
    {
        return $this->requestUri;
    }

    /**
     * 设置当前请求的URI
     * @param string $uri
     */
    public function setRequestUri($uri)
    {
        $this->requestUri = $uri;
    }

    /**
     * 获取允许输出的字段
     * @return array
     */
    public function getAllowFields()
    {
        return $this->allowFields;
    }

    /**
     * 设置允许输出的字段
     * @param array $fields
     */
    public function setAllowFields($fields)
    {
        $this->allowFields = $fields;
    }

    /**
     * 删除一个指定商户
     * @param int $mer_id 商户ID
     * @return boolean
     */
    public function delete($mer_id)
    {
        $mer = Merchant::get(['mer_id' => $mer_id]);
        if (!$mer) {
            return false;
        }
        Db::startTrans();
        try {
            // 删除会员
            Merchant::destroy(['mer_id' => $mer_id]);
            // 删除会员指定的所有Token
            Token::clear($mer_id);

            Hook::listen("merchant_delete_successed", $mer);
            Db::commit();
        } catch (Exception $e) {
            Db::rollback();
            $this->setError($e->getMessage());
            return false;
        }
        return true;
    }

    /**
     * 获取密码加密后的字符串
     * @param string $password 密码
     * @param string $salt     密码盐
     * @return string
     */
    public function getEncryptPassword($password, $salt = '')
    {
        return md5(md5($password) . $salt);
    }

    /**
     * 检测当前控制器和方法是否匹配传递的数组
     *
     * @param array $arr 需要验证权限的数组
     * @return boolean
     */
    public function match($arr = [])
    {
        $request = Request::instance();
        $arr = is_array($arr) ? $arr : explode(',', $arr);
        if (!$arr) {
            return false;
        }
        $arr = array_map('strtolower', $arr);
        // 是否存在
        if (in_array(strtolower($request->action()), $arr) || in_array('*', $arr)) {
            return true;
        }

        // 没找到匹配
        return false;
    }

    /**
     * 设置会话有效时间
     * @param int $keeptime 默认为永久
     */
    public function keeptime($keeptime = 0)
    {
        $this->keeptime = $keeptime;
    }

    /**
     * 渲染用户数据
     * @param array  $datalist  二维数组
     * @param mixed  $fields    加载的字段列表
     * @param string $fieldkey  渲染的字段
     * @param string $renderkey 结果字段
     * @return array
     */
    public function render(&$datalist, $fields = [], $fieldkey = 'mer_id', $renderkey = 'merinfo')
    {
        $fields = !$fields ? ['mer_id', 'mer_name', 'real_name', 'mer_phone', 'mer_email'] : (is_array($fields) ? $fields : explode(',', $fields));
        $ids = [];
        foreach ($datalist as $k => $v) {
            if (!isset($v[$fieldkey])) {
                continue;
            }
            $ids[] = $v[$fieldkey];
        }
        $list = [];
        if ($ids) {
            if (!in_array('mer_id', $fields)) {
                $fields[] = 'mer_id';
            }
            $ids = array_unique($ids);
            $selectlist = Merchant::where('mer_id', 'in', $ids)->column($fields);
            foreach ($selectlist as $k => $v) {
                $list[$v['mer_id']] = $v;
            }
        }
        foreach ($datalist as $k => &$v) {
            $v[$renderkey] = $list[$v[$fieldkey]] ?? null;
        }
        unset($v);
        return $datalist;
    }

    /**
     * 设置错误信息
     *
     * @param string $error 错误信息
     * @return Auth
     */
    public function setError($error)
    {
        $this->_error = $error;
        return $this;
    }

    /**
     * 获取错误信息
     * @return string
     */
    public function getError()
    {
        return $this->_error ? __($this->_error) : '';
    }
}
