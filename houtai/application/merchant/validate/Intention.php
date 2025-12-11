<?php

namespace app\merchant\validate;

use think\Validate;

class Intention extends Validate
{
    /**
     * 验证规则
     */
    protected $rule = [
        'phone'   => 'require|regex:\S{20}',
    ];
    /**
     * 提示消息
     */
    protected $message = [
    ];
    /**
     * 验证场景
     */
    protected $scene = [
        'add'  => ['phone'],
        'edit' => [],
    ];

    public function __construct(array $rules = [], $message = [], $field = [])
    {
        $this->message = array_merge($this->message, [
            'phone.regex' => __('手机号码长度不能超过20个字符'),
        ]);
        parent::__construct($rules, $message, $field);
    }
}
