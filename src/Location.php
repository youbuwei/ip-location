<?php

declare(strict_types=1);

namespace Youbuwei\IPLocation;

use GuzzleHttp\Exception\GuzzleException;
use Hyperf\Contract\ConfigInterface;
use Psr\SimpleCache\InvalidArgumentException;
use Youbuwei\IPLocation\Exception\LocationException;

class Location
{
    private const DRIVERS = [
        'ip-api' => Driver\IP_API::class,
        'tencent' => Driver\TencentLocation::class,
    ];

    private LocationDriverInterface $locationDriver;

    public function __construct(
        protected HttpClient $httpClient,
        protected ConfigInterface $config,
    ) {
        $this->locationDriver = $this->getDriver();
    }

    /**
     * @throws GuzzleException|LocationException
     * @throws InvalidArgumentException
     */
    public function getLocation(?string $ip): bool|array
    {
        if ($this->isEnable() === false) {
            return false;
        }

        $ip = $ip ?: $this->httpClient->getClientIP();

        if (filter_var($ip, FILTER_VALIDATE_IP) === false) {
            throw new LocationException('Invalid IP');
        }

        $request = $this->locationDriver->makeRequest($ip);

        $response = $this->httpClient->getHttpClient()->send($request);

        if ($response->getStatusCode() === 200) {
            $location = $response->getBody()->getContents();
        } else {
            throw new LocationException('IP Location request error');
        }

        return $this->locationDriver->getLocation($location);
    }

    private function getDriver(): LocationDriverInterface
    {
        $config = $this->config->get('ip-location');
        $driver = self::DRIVERS[$config['use']] ?? null;

        if (is_null($driver)) {
            throw new LocationException('No Location driver found');
        }

        if (isset($config[$config['use']]) === false) {
            throw new LocationException('No Location driver config found');
        }

        if (class_exists($driver) === false) {
            throw new LocationException('No Location driver class found');
        }

        return make($driver, ['config' => $config[$config['use']]]);
    }

    private function isEnable()
    {
        return $this->config->get('ip-location.enable');
    }
}
