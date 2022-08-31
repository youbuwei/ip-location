# IP地址归属地/IP定位

[![Test](https://github.com/youbuwei/ip-location/actions/workflows/test.yml/badge.svg)](https://github.com/youbuwei/ip-location/actions/workflows/test.yml)
[![Latest Stable Version](http://poser.pugx.org/youbuwei/ip-location/v)](https://packagist.org/packages/youbuwei/ip-location)
[![Php Version](https://img.shields.io/badge/php-%3E=8.0-brightgreen.svg?maxAge=2592000)](https://www.php.net)
[![Swoole Version](https://img.shields.io/badge/swoole-%3E=4.5-brightgreen.svg?maxAge=2592000)](https://github.com/swoole/swoole-src)
[![Hyperf Version](https://img.shields.io/badge/Hyperf-%3E=3.0-brightgreen.svg?maxAge=2592000)](https://github.com/hyperf/hyperf)
[![Total Downloads](http://poser.pugx.org/youbuwei/ip-location/downloads)](https://packagist.org/packages/youbuwei/ip-location)
[![License](https://img.shields.io/github/license/youbuwei/ip-location.svg?maxAge=2592000)](https://github.com/youbuwei/ip-location/blob/master/LICENSE)

## 安装
```
composer require youbuwei/ip-location
bin/hyperf.php vendor:publish youbuwei/ip-location
```

## 配置

### 基本配置
默认使用了[纯真IP数据库](https://github.com/youbuwei/ip-location/releases/download/0.1.0/ip.txt)，不保证数据的准确性，可自己通过其他方式获取IP地址数据。此外还对接了几个常用的服务商，可配置使用。

将IP数据文件路径配置到`config/ip-location.php`如下位置中：
```php
'cz88' => [
    'db-path' => '',
]
```

因为获取归属地依赖于外部接口，存在不可控的风险，因此设计了一个开关，可以在无可用服务时关闭归属地服务，访问影响其他业务。
```php
'enable' => true,
```

通过在 ```config/autoload/dependencies.php``` 文件中指定使用哪个接口，默认使用 ```\Youbuwei\IPLocation\Api\LocalLocation.php::class```
```php
<?php

declare(strict_types=1);

return [
    \Youbuwei\IPLocation\LocationApiInterface::class => \Youbuwei\IPLocation\Api\TencentLocation::class,
];

```

### 缓存
IP地址归属地在一定时间内是不变化的，因此可以缓存归属地信息，提高性能，减少接口的调用。
```php
'cache' => [
    'enable' => true,
    'expire' => 86400,
],
```

### 对接的服务商
当前支持 [ip-api](https://ip-api.com/) 、 [腾讯位置服务](https://lbs.qq.com/)、[百度地图开放平台](https://lbs.baidu.com/)、[高德开放平台](https://lbs.amap.com/)

#### [ip-api](https://ip-api.com/)
ip-api.com，无需配置，使用简单，但可能间歇性的无法访问或者访问缓慢，暂时未对接 Pro 服务
```php
'ip-api' => [
    'method' => 'GET',
    'domain' => 'http://ip-api.com',
    'uri' => '/json/',
    'lang' => 'zh-CN',
],
```

#### [腾讯位置服务](https://lbs.qq.com/)
使用了腾讯位置服务的IP定位接口，需要首先登录创建应用并申请一个key，如果使用授权签名的方式，还需要需要配置 secret。
```php
'tencent' => [
    'method' => 'GET',
    'domain' => 'https://apis.map.qq.com',
    'uri' => '/ws/location/v1/ip',
    'key' => '',
    'secret' => '',
],
```

### [百度地图开放平台](https://lbs.baidu.com/)
百度地图配置参数ak时如果选择使用sn，需要同时配置sk，配置如下：
```php
'baidu' => [
    'method' => 'GET',
    'domain' => 'https://api.map.baidu.com',
    'uri' => '/location/ip',
    'ak' => '',
    'sk' => '',
    'use_sn' => false,
    'coor' => '',
],
```

### [高德开放平台](https://lbs.amap.com/)
```php
'amap' => [
    'method' => 'GET',
    'domain' => 'https://restapi.amap.com',
    'uri' => '/v3/ip',
    'key' => '',
    'secret' => '',
],
```

#### 自定义对接其他服务商
如果需要对接其他服务商，需要在
- 配置文件添加配置项，例如：
```php
'custom' => [
    'method' => 'GET',
    'domain' => '',
    'uri' => '',
    'other' => '',
],
```

- 继承接口 ```\Youbuwei\IPLocation\LocationApiInterface::class```
```php
<?php

declare(strict_types=1);

namespace Youbuwei\IPLocation;

use Psr\Http\Message\RequestInterface;

interface LocationApiInterface
{
    public function makeRequest($ip): RequestInterface|bool;

    public function getLocation(string $location): array|bool;
}
```

## 使用
使用非常简单，配置好之后实例化 ```\Youbuwei\IPLocation\Location``` 并调用 ```getLocation(string $ip)``` 方法即可
```php
<?php

declare(strict_types=1);

namespace App\Controller;

use Youbuwei\IPLocation\Location;

class IndexController extends AbstractController
{
    public function __construct(
        protected Location $location
    ) {}

    public function index()
    {
        $location = $this->location->getLocation('66.66.66.66');

        return [
            'location' => $location,
        ];
    }
}
```