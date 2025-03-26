<?php

namespace app\model;

use think\Model;
use think\model\concern\SoftDelete;

class BaseModel extends Model
{
    /**
     * 软删除
     */
    use SoftDelete;

    protected function getOptions(): array
    {
        return [
            'autoWriteTimestamp' => true,
            'createTime'         => 'create_time',
            'updateTime'         => 'update_time',
            'deleteTime'         => false,
        ];
    }
}
