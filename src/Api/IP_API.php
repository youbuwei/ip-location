<?php

declare(strict_types=1);

namespace Youbuwei\IPLocation\Api;

use Hyperf\Config\Annotation\Value;
use Psr\Http\Message\RequestInterface;
use Youbuwei\IPLocation\Exception\LocationException;
use Youbuwei\IPLocation\LocationApiInterface;

class IP_API implements LocationApiInterface
{
    #[Value('ip-location.ip-api')]
    protected array $config;

    /**
     * @param $ip
     * @return RequestInterface
     */
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

    /**
     * @param string $location
     * @return array|bool
     */
    public function getLocation(string $location): array|bool
    {
        $location = json_decode($location, true);

        if (isset($location['status']) && $location['status'] !== 'success') {
            throw new LocationException($location['message']);
        }

        return $location;
    }
}
