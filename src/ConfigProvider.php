<?php

declare(strict_types=1);

namespace Youbuwei\IPLocation;

use Youbuwei\IPLocation\Api\LocalLocation;
use Youbuwei\IPLocation\Listener\LocationTableListener;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                LocationApiInterface::class => LocalLocation::class
            ],
            'listeners' => [
                LocationTableListener::class,
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
