<?php

namespace app\merchant\library\token\driver;

use app\merchant\library\token\Driver;

/**
 * Token操作类
 */
class Mysql extends Driver
{

    /**
     * 默认配置
     * @var array
     */
    protected $options = [
        'table'      => 'merchant_token',
        'expire'     => 2592000,
        'connection' => [],
    ];


    /**
     * 构造函数
     * @param array $options 参数
     * @access public
     */
    public function __construct($options = [])
    {
        if (!empty($options)) {
            $this->options = array_merge($this->options, $options);
        }
        if ($this->options['connection']) {
            $this->handler = \think\Db::connect($this->options['connection'])->name($this->options['table']);
        } else {
            $this->handler = \think\Db::name($this->options['table']);
        }
        $time = time();
        $tokentime = cache('tokentime');
        if (!$tokentime || $tokentime < $time - 86400) {
            cache('tokentime', $time);
            $this->handler->where('expiretime', '<', $time)->where('expiretime', '>', 0)->delete();
        }
    }

    /**
     * 存储Token
     * @param string $token   Token
     * @param int    $mer_id 商户ID
     * @param int    $expire  过期时长,0表示无限,单位秒
     * @return bool
     */
    public function set($token, $mer_id, $expire = null)
    {
        $expiretime = !is_null($expire) && $expire !== 0 ? time() + $expire : 0;
        $token = $this->getEncryptedToken($token);
        $this->handler->insert(['token' => $token, 'mer_id' => $mer_id, 'createtime' => time(), 'expiretime' => $expiretime]);
        return true;
    }

    /**
     * 获取Token内的信息
     * @param string $token
     * @return  array
     */
    public function get($token)
    {
        $data = $this->handler->where('token', $this->getEncryptedToken($token))->find();
        if ($data) {
            if (!$data['expiretime'] || $data['expiretime'] > time()) {
                //返回未加密的token给客户端使用
                $data['token'] = $token;
                //返回剩余有效时间
                $data['expires_in'] = $this->getExpiredIn($data['expiretime']);
                return $data;
            } else {
                self::delete($token);
            }
        }
        return [];
    }

    /**
     * 判断Token是否可用
     * @param string $token   Token
     * @param int    $mer_id 商户ID
     * @return  boolean
     */
    public function check($token, $mer_id)
    {
        $data = $this->get($token);
        return $data && $data['mer_id'] == $mer_id ? true : false;
    }

    /**
     * 删除Token
     * @param string $token
     * @return  boolean
     */
    public function delete($token)
    {
        $this->handler->where('token', $this->getEncryptedToken($token))->delete();
        return true;
    }

    /**
     * 删除指定用户的所有Token
     * @param int $mer_id
     * @return  boolean
     */
    public function clear($mer_id)
    {
        $this->handler->where('mer_id', $mer_id)->delete();
        return true;
    }

}
