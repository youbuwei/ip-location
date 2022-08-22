<?php

declare(strict_types=1);

namespace Youbuwei\IPLocation;

use Psr\Http\Message\RequestInterface;

interface LocationApiInterface
{
    public function makeRequest($ip): RequestInterface;

    public function getLocation(string $location): array|bool;
}
