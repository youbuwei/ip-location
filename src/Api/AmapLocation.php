<?php

namespace Youbuwei\IPLocation\Api;

use Hyperf\Config\Annotation\Value;
use Psr\Http\Message\RequestInterface;
use Youbuwei\IPLocation\Exception\LocationException;
use Youbuwei\IPLocation\LocationApiInterface;

class AmapLocation implements LocationApiInterface
{
    #[Value('ip-location.amap')]
    protected array $config;

    /**
     * @param $ip
     * @return RequestInterface
     */
    public function makeRequest($ip): RequestInterface
    {
        $params = [
            'ip' => $ip,
            'key' => $this->config['key'],
            'output' => 'json',
        ];

        $params['sig'] = $this->sign($params);

        $query = http_build_query($params);

        return make(\GuzzleHttp\Psr7\Request::class, [
            $this->config['method'],
            $this->config['domain'] . $this->config['uri'] . '?' . $query,
        ]);
    }

    /**
     * @param string $location
     * @return array|bool
     */
    public function getLocation(string $location): array|bool
    {
        $location = json_decode($location, true);

        if (isset($location['status']) && $location['status'] != 1) {
            throw new LocationException($location['info']);
        }

        return $location;
    }

    /**
     * generate signature.
     * @param $params
     * @return string
     */
    private function sign($params): string
    {
        ksort($params);
        $str = '';
        foreach ($params as $key => $param) {
            $str .= $key . '=' . $param . '&';
        }
        $str = rtrim($str, '&');
        $str = $str . $this->config['secret'];

        return md5($str);
    }
}