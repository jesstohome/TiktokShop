<?php

namespace app\api\validate;

use think\Validate;

class User extends Validate
{
    /**
     * 验证规则
     */
    protected $rule = [
        'mobile'   => 'require|regex:\S{20}|unique:user'
    ];

    /**
     * 字段描述
     */
    protected $field = [
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
        'add'  => ['mobile'],
        'edit' => [],
    ];

    public function __construct(array $rules = [], $message = [], $field = [])
    {
        $this->message = array_merge($this->message, [
            'mer_phone.regex' => __('手机号码长度不能超过20个字符'),
        ]);
        parent::__construct($rules, $message, $field);
    }

}
