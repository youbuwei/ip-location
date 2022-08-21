# 获取IP归属地

## 介绍
- PHP 8.0 以上版本
- Hyperf 3.0

## 安装
```
composer require youbuwei/ip-location
bin/hyperf.php vendor:publish youbuwei/ip-location
```

## 使用
当前使用了 [ip-api](https://ip-api.com/) 和 [腾讯位置服务](https://lbs.qq.com/)

### ip-api
默认使用 ip-api.com，可能间歇性的无法访问或者访问缓慢，暂时未对接 Pro 服务

### 腾讯位置服务
需要先登录[腾讯位置服务](https://lbs.qq.com/)，创建应用并申请一个 key，填入到配置文件中。
如果使用授权签名的方式，需要配置 secret。
