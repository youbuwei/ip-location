<?php

declare(strict_types=1);

namespace Youbuwei\IPLocation;

use Youbuwei\IPLocation\Api\IP_API;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                LocationApiInterface::class => IP_API::class
            ],
            'commands' => [
            ],
            'annotations' => [
                'scan' => [
                    'paths' => [
                        __DIR__,
                    ],
                ],
            ],
            'publish' => [
                [
                    'id' => 'ip-location',
                    'description' => 'The config of ip location.',
                    'source' => __DIR__ . '/../publish/ip-location.php',
                    'destination' => BASE_PATH . '/config/autoload/ip-location.php',
                ],
            ],
        ];
    }
}
