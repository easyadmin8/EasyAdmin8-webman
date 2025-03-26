<?php

namespace app\admin\model;

use app\model\BaseModel;

class MallCate extends BaseModel
{
    protected function getOptions(): array
    {
        return [
            'deleteTime' => 'delete_time',
        ];
    }
}
