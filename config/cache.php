<?php

// ThinkPHP 版本请查看 think-cache.php 文件

return [
    'default' => 'file',
    'stores'  => [
        'file'  => [
            'driver' => 'file',
            'path'   => runtime_path('cache')
        ],
        'redis' => [
            'driver'     => 'redis',
            'connection' => 'default'
        ],
        'array' => [
            'driver' => 'array'
        ]
    ]
];