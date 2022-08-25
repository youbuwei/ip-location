<?php

declare(strict_types=1);

return [
    'enable' => true,
    'cache' => [
        'enable' => true,
        'expire' => 86400,
    ],
    'ip-api' => [
        'method' => 'GET',
        'domain' => 'http://ip-api.com',
        'uri' => '/json/',
        'lang' => 'zh-CN',
    ],
    'tencent' => [
        'method' => 'GET',
        'domain' => 'https://apis.map.qq.com',
        'uri' => '/ws/location/v1/ip',
        'key' => '',
        'secret' => '',
    ],
    'amap' => [
        'method' => 'GET',
        'domain' => 'https://restapi.amap.com',
        'uri' => '/v3/ip',
        'key' => '',
        'secret' => '',
    ],
    'baidu' => [
        'method' => 'GET',
        'domain' => 'https://api.map.baidu.com',
        'uri' => '/location/ip',
        'ak' => '',
        'sk' => '',
        'use_sn' => false,
        'coor' => '',
    ],
];
