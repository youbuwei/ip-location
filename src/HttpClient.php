<?php

declare(strict_types=1);

namespace Youbuwei\IPLocation;

use Hyperf\Context\Context;
use Hyperf\Guzzle\ClientFactory;
use Psr\Http\Message\RequestInterface;

class HttpClient
{
    protected ClientFactory $clientFactory;

    public function __construct(ClientFactory $clientFactory)
    {
        $this->clientFactory = $clientFactory;
    }

    public function getClientIP()
    {
        $request = Context::get(RequestInterface::class);
        $ip = $request->header('host');
        $ip = filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) ? $ip : null;
        return $request->header('x-real-ip', $ip);
    }

    public function getHttpClient(): \GuzzleHttp\Client
    {
        return $this->clientFactory->create([
            'verify' => false,
        ]);
    }
}
