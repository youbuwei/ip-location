<?php

declare(strict_types=1);

namespace Youbuwei\IPLocation\Api;

use Psr\Http\Message\RequestInterface;
use Youbuwei\IPLocation\LocationApiInterface;
use Youbuwei\IPLocation\LocalLocationTable;

class LocalLocation implements LocationApiInterface
{
    public function __construct(
        protected LocalLocationTable $localLocationTable
    ) {
    }

    /**
     * @param $ip
     * @return RequestInterface|null
     */
    public function makeRequest($ip): ?RequestInterface
    {
        return null;
    }

    /**
     * @param string $ip
     * @return array|null
     */
    public function getLocation(string $ip): ?array
    {
        $region = $this->localLocationTable->getRegion($ip);

        return ['ip' => $ip, 'region' => $region];
    }
}
