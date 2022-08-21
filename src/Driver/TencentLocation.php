<?php

declare(strict_types=1);

namespace Youbuwei\IPLocation\Driver;

use Psr\Http\Message\RequestInterface;
use Youbuwei\IPLocation\Exception\LocationException;
use Youbuwei\IPLocation\LocationDriverInterface;

class TencentLocation implements LocationDriverInterface
{
    private array $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * @param $ip
     */
    public function makeRequest($ip): RequestInterface
    {
        $params = [
            'key' => $this->config['key'],
            'ip' => $ip,
        ];

        if (isset($this->config['secret']) && $this->config['secret']) {
            $sign = $this->sign($params);
            $params['sig'] = $sign;
        }

        $query = http_build_query($params);

        return make(\GuzzleHttp\Psr7\Request::class, [
            $this->config['method'],
            $this->config['domain'] . $this->config['uri'] . '?' . $query,
        ]);
    }

    public function getLocation(string $location): array|bool
    {
        $location = json_decode($location, true);

        if (isset($location['status']) && $location['status'] !== 0) {
            throw new LocationException($location['message']);
        }

        return $location['result']['location'] ?? false;
    }

    /**
     * generate signature.
     * @param $params
     */
    private function sign($params): string
    {
        ksort($params);
        $str = $this->config['uri'] . '?' . http_build_query($params) . $this->config['secret'];

        return md5($str);
    }
}
