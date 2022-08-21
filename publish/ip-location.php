<?php

declare(strict_types=1);

return [
    'enable' => true,
    'use' => 'ip-api',
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
        'key' => 'AAAA-BBBB-CCCC-DDDD-EEEE-FFFF',
        'secret' => '',
    ],
];