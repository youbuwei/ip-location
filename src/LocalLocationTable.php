<?php

declare(strict_types=1);

namespace Youbuwei\IPLocation;

use Hyperf\Config\Annotation\Value;
use Youbuwei\IPLocation\Exception\LocationException;
use Youbuwei\IPLocation\Storage\StorageDriverInterface;

class LocalLocationTable
{
    public const KEY = 'key';

    public const VALUE = 'value';

    #[Value('ip-location.cz88')]
    public array $config;

    public function __construct(
        protected StorageDriverInterface $storageDriver
    ) {
    }

    /**
     * @param $ip
     * @return array|null
     */
    public function getRegion($ip): ?array
    {
        $ip2long = $this->ip2long($ip);

        $location = $this->storageDriver->getRegionDirect($ip2long);

        if ($location === null) {
            $location = $this->storageDriver->getRegion($ip2long);
        }

        return $location;
    }

    /**
     * 加载数据
     * @return $this
     */
    public function loadData(): static
    {
        if ($this->storageDriver->count() !== 0) {
            return $this;
        }

        if (file_exists($this->config['db-path']) === false) {
            throw new LocationException("File {$this->config['db-path']} not found");
        }

        $handle = fopen($this->config['db-path'], 'r+');
        if (is_resource($handle) === false) {
            throw new LocationException("File {$this->config['db-path']} open failed");
        }

        while (!feof($handle)) {
            $line = fgets($handle);

            if (($data = $this->splitLineData($line)) === null) {
                continue;
            }

            $this->storageDriver->exist($data[self::KEY]) || $this->storageDriver->set($data[self::KEY], $data[self::VALUE]);
        }
        fclose($handle);

        return $this;
    }

    /**
     * split data
     * @param $line
     * @return array|null
     */
    private function splitLineData($line): ?array
    {
        $data = mb_str_split($line, 16);
        if (!(isset($data[0]) && isset($data[1]) && isset($data[2]))) {
            return null;
        }

        $start = $this->ip2long(trim($data[0]));
        $end = $this->ip2long(trim($data[1]));
        $description = trim($data[2] . ($data[3] ?? '') . ($data[4] ?? '') . ($data[5] ?? ''));
        $description = explode(' ', $description);
        $region = $description[0];
        $isp = $description[1] ?? '';

        return [
            self::KEY => (string) $start,
            self::VALUE => [
                StorageDriverInterface::IP_START => $start,
                StorageDriverInterface::IP_END => $end,
                StorageDriverInterface::IP_REGION => $region,
                StorageDriverInterface::IP_ISP => $isp,
            ],
        ];
    }

    /**
     * convert signed int to unsigned int if on 32 bit operating system
     * @param $ip
     * @return int|null
     */
    private function ip2long($ip): ?int
    {
        $ip = ip2long($ip);

        if ($ip === false) {
            return null;
        }

        if ($ip < 0 && PHP_INT_SIZE == 4) {
            $ip = (int) sprintf("%u", $ip);
        }

        return $ip;
    }
}