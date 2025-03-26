<?php

namespace app\admin\model;

use app\model\BaseModel;

class SystemAdmin extends BaseModel
{
    protected function getOptions(): array
    {
        return [
            'deleteTime' => 'delete_time',
        ];
    }

    public array $notes = [
        'login_type' => [
            1 => '密码登录',
            2 => '密码 + 谷歌验证码登录'
        ],
    ];

    public function getAuthIdsAttr($value): array
    {
        if (!$value) return [];
        return explode(',', $value);
    }

    public function getAuthList(): array
    {
        return (new SystemAuth())->removeOption()->where('status', 1)->column('title', 'id');
    }
}
