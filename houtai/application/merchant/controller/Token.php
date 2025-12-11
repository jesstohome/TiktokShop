<?php

namespace app\merchant\controller;

use app\common\controller\Mer;
use fast\Random;

/**
 * Token接口
 */
class Token extends Mer
{
    protected $noNeedLogin = [];
    protected $noNeedRight = '*';

    /**
     * 检测Token是否过期
     *
     */
    public function check()
    {
        $token = $this->auth->getToken();
        $tokenInfo = \app\merchant\library\Token::get($token);
        $this->success('', ['token' => $tokenInfo['token'], 'expires_in' => $tokenInfo['expires_in']]);
    }

    /**
     * 刷新Token
     *
     */
    public function refresh()
    {
        //删除源Token
        $token = $this->auth->getToken();
        \app\merchant\library\Token::delete($token);
        //创建新Token
        $token = Random::uuid();
        \app\merchant\library\Token::set($token, $this->auth->id, 2592000);
        $tokenInfo = \app\merchant\library\Token::get($token);
        $this->success('', ['token' => $tokenInfo['token'], 'expires_in' => $tokenInfo['expires_in']]);
    }
}
