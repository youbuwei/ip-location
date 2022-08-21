<?php

declare(strict_types=1);

namespace Youbuwei\IPLocation\Driver;

use Psr\Http\Message\RequestInterface;
use Youbuwei\IPLocation\Exception\LocationException;
use Youbuwei\IPLocation\LocationDriverInterface;

class IP_API implements LocationDriverInterface
{
    protected string $ip;

    protected array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function makeRequest($ip): RequestInterface
    {
        $params = [
            'lang' => $this->config['lang'],
        ];

        return make(\GuzzleHttp\Psr7\Request::class, [
            $this->config['method'],
            $this->config['domain'] . $this->config['uri'] . $ip . '?' . http_build_query($params),
        ]);
    }

    public function getLocation(string $location): array|bool
    {
        $location = json_decode($location, true);

        if (isset($location['status']) && $location['status'] !== 'success') {
            throw new LocationException($location['message']);
        }

        return $location;
    }
}
