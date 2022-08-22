<?php

declare(strict_types=1);

namespace Youbuwei\IPLocation;

use GuzzleHttp\Exception\GuzzleException;
use Hyperf\Contract\ConfigInterface;
use Psr\SimpleCache\InvalidArgumentException;
use Youbuwei\IPLocation\Exception\LocationException;

class Location
{
    public function __construct(
        protected HttpClient           $httpClient,
        protected ConfigInterface      $config,
        protected LocationApiInterface $locationDriver,
    ) {
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

    private function isEnable()
    {
        return $this->config->get('ip-location.enable');
    }
}
