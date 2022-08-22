<?php

declare(strict_types=1);

namespace Youbuwei\IPLocation\Api;

use Hyperf\Config\Annotation\Value;
use Psr\Http\Message\RequestInterface;
use Youbuwei\IPLocation\Exception\LocationException;
use Youbuwei\IPLocation\LocationApiInterface;

class TencentLocation implements LocationApiInterface
{
    #[Value('ip-location.tencent')]
    protected array $config;

    /**
     * @param $ip
     * @return RequestInterface
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

        return $location['result'] ?? false;
    }

    /**
     * generate signature.
     * @param $params
     * @return string
     */
    private function sign($params): string
    {
        ksort($params);
        $str = $this->config['uri'] . '?' . http_build_query($params) . $this->config['secret'];

        return md5($str);
    }
}
