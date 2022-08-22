# 获取IP地址归属地

[![Test](https://github.com/youbuwei/ip-location/actions/workflows/test.yml/badge.svg)](https://github.com/youbuwei/ip-location/actions/workflows/test.yml)
[![Latest Stable Version](http://poser.pugx.org/youbuwei/ip-location/v)](https://packagist.org/packages/youbuwei/ip-location)
[![Total Downloads](http://poser.pugx.org/youbuwei/ip-location/downloads)](https://packagist.org/packages/youbuwei/ip-location)
[![License](http://poser.pugx.org/youbuwei/ip-location/license)](https://packagist.org/packages/youbuwei/ip-location)

## 介绍
- PHP 8.0 或 8.1
- Hyperf 3.0

## 安装
```
composer require youbuwei/ip-location
bin/hyperf.php vendor:publish youbuwei/ip-location
```

## 配置

### 基本配置
因为获取归属地依赖于外部接口，存在不可控的风险，因此设计了一个开关，可以在无可用服务时关闭归属地服务，访问影响其他业务。
```php
'enable' => true,
```

通过在 ```config/autoload/dependencies.php``` 文件中指定使用哪个接口，默认使用 ```\Youbuwei\IPLocation\Api\IP_API::class```。
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
当前使用了 [ip-api](https://ip-api.com/) 和 [腾讯位置服务](https://lbs.qq.com/)

#### [ip-api](https://ip-api.com/)
默认使用 ip-api.com，可能间歇性的无法访问或者访问缓慢，暂时未对接 Pro 服务
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
    public function makeRequest($ip): RequestInterface;

    public function getLocation(string $location): array|bool;
}
```

## 使用
使用非常简单，配置好之后实例化 ```\Youbuwei\IPLocation\Location``` 并调用 ```getLocation(?string $ip)``` 方法即可，```$ip``` 可不填写，自动获取当前请求的 IP 地址
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