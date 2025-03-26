<?php

namespace app\admin\model;

use app\common\services\SystemLogService;
use app\model\BaseModel;
use think\model\relation\HasOne;

class SystemLog extends BaseModel
{

    protected array $type = [
        'content'  => 'string',
        'response' => 'string',
    ];

    protected function init(): void
    {
        SystemLogService::instance()->detectTable();
    }
    public function admin(): HasOne
    {
        return $this->hasOne(SystemAdmin::class, 'id', 'admin_id')->field('id,username');
    }

}
