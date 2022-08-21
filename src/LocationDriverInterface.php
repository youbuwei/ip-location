<?php

declare(strict_types=1);

namespace Youbuwei\IPLocation;

use Psr\Http\Message\RequestInterface;

interface LocationDriverInterface
{
    public function __construct(array $config);

    public function makeRequest($ip): RequestInterface;

    public function getLocation(string $location): array|bool;
}
