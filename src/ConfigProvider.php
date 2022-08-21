<?php

declare(strict_types=1);

namespace Youbuwei\IPLocation;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
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
                    'description' => 'The config for ip location.',
                    'source' => __DIR__ . '/../publish/ip-location.php',
                    'destination' => BASE_PATH . '/config/autoload/ip-location.php',
                ],
            ],
        ];
    }
}
