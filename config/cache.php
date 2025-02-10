<?php
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