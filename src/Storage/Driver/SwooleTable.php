<?php

declare(strict_types=1);

namespace Youbuwei\IPLocation\Storage\Driver;

use Swoole\Table;
use Youbuwei\IPLocation\Storage\StorageDriverInterface;

class SwooleTable implements StorageDriverInterface
{
    /**
     * @var Table|null
     */
    private static $table;

    /**
     * 直接获取
     * @param $ip2long
     * @return null|array
     */
    public function getRegionDirect($ip2long): ?array
    {
        $ip2longStr = (string) $ip2long;
        if ($this->exist($ip2longStr)) {
            $row = $this->get($ip2longStr);
            return $row ?? null;
        }

        return null;
    }

    /**
     * 查询IP
     * @todo 可考虑使用更高效的查询算法
     * @param $ip2long
     * @return null|array
     */
    public function getRegion($ip2long): ?array
    {
        $location = null;
        while ($location === null) {
            $location = $this->getRegionDirect($ip2long);
            $ip2long--;
        }

        return $location;
    }

    /**
     * @return $this
     */
    public function getTable(): self
    {
        if (!self::$table instanceof Table) {
            $table = new Table(1000000);
            $table->column(self::IP_START, Table::TYPE_INT);
            $table->column(self::IP_END, Table::TYPE_INT);
            $table->column(self::IP_REGION, Table::TYPE_STRING, 200);
            $table->column(self::IP_ISP, Table::TYPE_STRING, 200);
            $table->create();
            self::$table = $table;
        }

        return $this;
    }

    /**
     * @return Table
     */
    private function getData(): Table
    {
        if (self::$table === null) {
            $this->getTable();
        }
        return self::$table;
    }

    public function exist($key): bool
    {
        return $this->getData()->exist($key);
    }

    public function rewind(): void
    {
        $this->getData()->rewind();
    }

    public function get($key): array
    {
        return $this->getData()->get($key);
    }

    public function set($key, $value): void
    {
        $this->getData()->set($key, $value);
    }

    public function valid(): bool
    {
        return $this->getData()->valid();
    }

    public function count(): int
    {
        return $this->getData()->count();
    }

    public function next(): void
    {
        $this->getData()->next();
    }

    public function current(): mixed
    {
        return $this->getData()->current();
    }
}