<?php

declare(strict_types=1);

namespace Youbuwei\IPLocation\Storage;

interface StorageDriverInterface
{
    public const IP_START = 'start';

    public const IP_END = 'end';

    public const IP_REGION = 'region';

    public const IP_ISP = 'isp';

    public function getRegionDirect($ip2long): ?array;

    public function getRegion($ip2long): ?array;

    public function getTable(): StorageDriverInterface;

    public function exist($key): bool;

    public function rewind(): void;

    public function get($key): array;

    public function set($key, $value): void;

    public function valid(): bool;

    public function current(): mixed;

    public function count(): int;

    public function next(): void;
}