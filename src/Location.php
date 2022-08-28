<?php

declare(strict_types=1);

namespace Youbuwei\IPLocation;

use GuzzleHttp\Exception\GuzzleException;
use Hyperf\Config\Annotation\Value;
use Hyperf\Guzzle\ClientFactory;
use Psr\SimpleCache\InvalidArgumentException;
use Youbuwei\IPLocation\Exception\LocationException;

class Location
{
    #[Value('ip-location')]
    protected array $config;

    public function __construct(
        protected LocationApiInterface $locationApi,
        protected ClientFactory        $clientFactory,
    ) {
    }

    /**
     * 获取IP地址归属地
     * @throws GuzzleException|LocationException|InvalidArgumentException
     */
    public function getLocation(string $ip): bool|array
    {
        if ($this->isEnable() === false) {
            return false;
        }

        if (filter_var($ip, FILTER_VALIDATE_IP) === false) {
            throw new LocationException('Invalid IP');
        }

        $request = $this->locationApi->makeRequest($ip);

        if ($request === false) {
            return $this->locationApi->getLocation($ip);
        }

        $response = $this->getHttpClient()->send($request);

        if ($response->getStatusCode() === 200) {
            $location = $response->getBody()->getContents();
        } else {
            throw new LocationException('IP Location request error');
        }

        return $this->locationApi->getLocation($location);
    }

    /**
     * Is Enable UP Location
     * @return bool
     */
    public function isEnable(): bool
    {
        return (bool) ($this->config['enable'] ?? false);
    }

    private function getHttpClient(): \GuzzleHttp\Client
    {
        return $this->clientFactory->create([
            'verify' => false,
        ]);
    }
}
