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
     * @return RequestInterface|bool
     */
    public function makeRequest($ip): RequestInterface|bool
    {
        return false;
    }

    /**
     * @param string $ip
     * @return array|bool
     */
    public function getLocation(string $ip): array|bool
    {
        $ip2long = ip2long($ip);
        $region = $this->localLocationTable->getRegionDirect($ip2long);
        if ($region === false) {
            $region = $this->localLocationTable->getRegion($ip2long);
        }

        return ['ip' => $ip, 'region' => $region];
    }
}
