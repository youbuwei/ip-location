<?php

declare(strict_types=1);

namespace Youbuwei\IPLocation\Storage\Driver;

use Redis;
use Youbuwei\IPLocation\Exception\LocationException;
use Youbuwei\IPLocation\Storage\StorageDriverInterface;

/**
 * @todo
 */
class RedisTable implements StorageDriverInterface
{
    private const CACHE_KEY = 'ip_location:';

    /**
     * @var Redis|null
     */
    private $redis;

    public function getRegionDirect($ip2long): ?array
    {
        $key = $this->getCacheKey($ip2long);
        if ($this->exist($key)) {
            $row = $this->get($key);
            return $row ?? null;
        }

        return null;
    }

    public function getRegion($ip2long): ?array
    {
        $key = $this->getCacheKey($ip2long);
        return null;
    }

    /**
     * @throws \RedisException
     */
    public function getTable(): self
    {
        if (!($this->redis instanceof Redis)) {
            $redis = new Redis();
            $redis->connect(config('redis.default.host'), config('redis.default.port'));
            $redis->auth(config('redis.default.auth'));
            $redis->select(config('redis.default.select', 10));
            $this->redis = $redis;
        }

        return $this;
    }

    private function getData(): Redis
    {
        if ($this->redis === null) {
            $this->getTable();
        }
        return $this->redis;
    }

    public function exist($key): bool
    {
        try {
            $key = $this->getCacheKey($key);
            return $this->getData()->exists($key);
        } catch (\RedisException) {
            return false;
        }
    }

    public function rewind(): void
    {
    }

    public function get($key): array
    {
        try {
            $key = $this->getCacheKey($key);
            $data = $this->getData()->hGetAll($key);
            return [
                self::IP_START => $data[self::IP_START],
                self::IP_END => $data[self::IP_END],
                self::IP_REGION => $data[self::IP_REGION],
                self::IP_ISP => $data[self::IP_ISP],
            ];
        } catch (\RedisException) {
            return [];
        }
    }

    public function set($key, $value): void
    {
        try {
            $key = $this->getCacheKey($key);
            $this->getData()->hMSet($key, $value);
        } catch (\RedisException $e) {
            throw new LocationException("Redis set error: {$e->getMessage()}");
        }
    }

    public function valid(): bool
    {
        return true;
    }

    public function current(): mixed
    {
        return [];
    }

    public function count(): int
    {
        return 0;
    }

    public function next(): void
    {
    }

    private function getCacheKey($key): string
    {
        return self::CACHE_KEY . $key;
    }
}