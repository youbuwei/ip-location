<?php

namespace Youbuwei\IPLocation;

use Hyperf\Config\Annotation\Value;
use Swoole\Table;

class LocalLocationTable
{
    /**
     * @var Table|null
     */
    private static $table;

    public const KEY = 'key';

    public const IP_START = 'ip_s';

    public const IP_END = 'ip_e';

    public const IP_DESCRIPTION = 'ip_d';

    #[Value('ip-location.cz88')]
    public array $config;

    public function getRegionDirect($ip2long)
    {
        $ip2longStr = (string) $ip2long;
        if ($this->getTable()->exist($ip2longStr)) {
            $row = $this->getTable()->get($ip2longStr);
            return $row[LocalLocationTable::IP_DESCRIPTION];
        }

        return false;
    }

    /**
     * 查询IP
     * @todo 可考虑使用更高效的查询算法
     * @param $ip2long
     * @return string
     */
    public function getRegion($ip2long): string
    {
        $this->getTable()->rewind();

        $region = '';
        $pre = null;
        while ($this->getTable()->valid()) {
            $row = $this->getTable()->current();
            if ($ip2long > $row[LocalLocationTable::IP_START] && $row[LocalLocationTable::IP_END] > $ip2long) {
                $region = $row[LocalLocationTable::IP_DESCRIPTION];
                break;
            }
            $pre = $row;
            $this->getTable()->next();
        }

        return $region;
    }

    /**
     * 获取内存表
     * @return Table
     */
    public function getTable(): Table
    {
        if (!self::$table instanceof Table) {
            $table = new Table(1000000);
            $table->column(self::IP_START, Table::TYPE_INT);
            $table->column(self::IP_END, Table::TYPE_INT);
            $table->column(self::IP_DESCRIPTION, Table::TYPE_STRING, 200);
            $table->create();
            self::$table = $table;
        }
        return self::$table;
    }

    /**
     * 加载数据
     * @return $this
     */
    public function loadData(): static
    {
        if ($this->getTable()->count() !== 0) {
            return $this;
        }

        $handle = fopen($this->config['db-path'], 'r+');
        while ($line = fgets($handle)) {
            if (($data = $this->splitLineData($line)) === false) {
                continue;
            }
            $this->getTable()->set(
                $data[self::KEY], [
                self::IP_START => $data[self::IP_START],
                self::IP_END => $data[self::IP_END],
                self::IP_DESCRIPTION => $data[self::IP_DESCRIPTION],
            ]);
        }
        fclose($handle);

        return $this;
    }

    /**
     * split data
     * @param $line
     * @return array|bool
     */
    private function splitLineData($line): array|bool
    {
        $data = mb_str_split($line, 16);
        if (!(isset($data[0]) && isset($data[1]) && isset($data[2]))) {
            return false;
        }

        $ip_s = ip2long(trim($data[0]));
        $ip_e = ip2long(trim($data[1]));
        $ip_d = trim($data[2] . ($data[3] ?? '') . ($data[4] ?? '') . ($data[5] ?? ''));

        return [
            self::KEY => (string) $ip_s,
            self::IP_START => $ip_s,
            self::IP_END => $ip_e,
            self::IP_DESCRIPTION => $ip_d,
        ];
    }
}