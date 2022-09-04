<?php

declare(strict_types=1);

namespace Youbuwei\IPLocation\Api;

use Hyperf\Config\Annotation\Value;
use Psr\Http\Message\RequestInterface;
use Youbuwei\IPLocation\Exception\LocationException;
use Youbuwei\IPLocation\LocationApiInterface;

class BaiduLocation implements LocationApiInterface
{
    #[Value('ip-location.baidu')]
    protected array $config;

    protected const ERROR = [
        0 => '正常',
        1 => '服务器内部错误',
        10 => '上传内容超过8M',
        101 => 'AK参数不存在',
        102 =>	'Mcode参数不存在，mobile类型mcode参数必需',
        200 =>	'APP不存在，AK有误请检查再重试',
        201 =>	'APP被用户自己禁用，请在控制台解禁',
        202 =>	'APP被管理员删除',
        203 =>	'APP类型错误',
        210 =>	'APP IP校验失败',
        211 =>	'APP SN校验失败',
        220 =>	'APP Referer校验失败',
        230 =>	'APP Mcode码校验失败',
        240 =>	'APP 服务被禁用',
        250 =>	'用户不存在',
        251 =>	'用户被自己删除',
        252 =>	'用户被管理员删除',
        260 =>	'服务不存在',
        261 =>	'服务被禁用',
        301 =>	'永久配额超限，限制访问',
        302 =>	'天配额超限，限制访问',
        401 =>	'当前并发量已经超过约定并发配额，限制访问',
        402 =>	'当前并发量已经超过约定并发配额，并且服务总并发量也已经超过设定的总并发配额，限制访问',
        1001 => '没有IPv6地址访问的权限',
    ];

    /**
     * @param $ip
     * @return RequestInterface|null
     */
    public function makeRequest($ip): ?RequestInterface
    {
        $params = [
            'ip' => $ip,
            'ak' => $this->config['ak'],
            /**
             * 设置返回位置信息中，经纬度的坐标类型，分别如下：
             * coor不出现、或为空：百度墨卡托坐标，即百度米制坐标
             * coor = bd09ll：百度经纬度坐标，在国测局坐标基础之上二次加密而来
             * coor = gcj02：国测局02坐标，在原始GPS坐标基础上，按照国家测绘行业统一要求，加密后的坐标
             * 注意：百度地图的坐标类型为bd09ll，如果结合百度地图使用，请注意坐标选择
             */
            'coor' => $this->config['coor'] ?? 'bd09ll',
        ];

        if (isset($this->config['use_sn']) && $this->config['use_sn']) {
            $params['sn'] = $this->sn($params);
        }

        $query = http_build_query($params);

        return make(\GuzzleHttp\Psr7\Request::class, [
            $this->config['method'],
            $this->config['domain'] . $this->config['uri'] . '?' . $query,
        ]);
    }

    /**
     * @param string $location
     * @return array|null
     */
    public function getLocation(string $location): ?array
    {
        $location = json_decode($location, true);

        if (isset($location['status']) && $location['status'] !== 0) {
            $error = self::ERROR[$location['status']] ?? '接口返回异常';
            throw new LocationException('百度服务接口错误: ' . $error);
        }

        return $location;
    }

    /**
     * 生成SN
     * @param $params
     * @return string
     */
    private function sn($params): string
    {
        ksort($params);
        $querystring = http_build_query($params);
        return md5(urlencode($this->config['uri'] . '?' . $querystring . $this->config['sk']));
    }
}