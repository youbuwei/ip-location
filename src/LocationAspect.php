<?php

declare(strict_types=1);

namespace Youbuwei\IPLocation;

use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Hyperf\Di\Exception\Exception;

#[Aspect]
class LocationAspect extends AbstractAspect
{
    public array $classes = [
        \Youbuwei\IPLocation\Location::class,
    ];

    public function __construct(
        private \Hyperf\Contract\ConfigInterface $config,
        private \Psr\SimpleCache\CacheInterface $cache,
    ) {
    }

    /**
     * @throws Exception|\Psr\SimpleCache\InvalidArgumentException
     * @return mixed
     */
    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        // 未使用缓存或者其他方法直接返回
        if ($proceedingJoinPoint->methodName !== 'getLocation'
            || $this->config->get('ip-location.cache.enable') === false) {
            return $proceedingJoinPoint->process();
        }

        // 未开启返回 false
        if ($this->config->get('ip-location.enable', false) === false) {
            return false;
        }

        $args = $proceedingJoinPoint->getArguments();
        $cacheKey = base64_encode('ip-location:' . serialize($args));
        if ($this->cache->has($cacheKey)) {
            return $this->cache->get($cacheKey);
        }

        $result = $proceedingJoinPoint->process();

        if ($result !== false) {
            $this->cache->set($cacheKey, $result, $this->config->get('ip-location.cache.expire', 86400));
        }

        return $result;
    }
}
