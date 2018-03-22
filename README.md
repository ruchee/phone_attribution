## 手机号码归属地
[![Latest Stable Version](https://img.shields.io/packagist/v/ruchee/phone_attribution.svg)](https://packagist.org/packages/ruchee/phone_attribution)

* 数据文件来源：[https://github.com/xluohome/phonedata/blob/master/phone.dat]
* 解析算法参考：[https://github.com/shitoudev/phone-location/blob/master/src/PhoneLocation.php]

### 安装
```
composer require 'ruchee/phone_attribution'
```

### 使用
```php
<?php

include_once __DIR__.'/vendor/autoload.php';
use Ruchee\PhoneAttribution\Parse;

$parse = new Parse();
$data  = $parse->parseOne('13713462969');
print_r($data);

// 输出结果
Array
(
    [status] => 1
    [info] => 解析成功
    [data] => Array
        (
            [province] => 广东
            [city] => 东莞
            [postcode] => 523000
            [prefix] => 0769
            [spname] => 中国移动
        )

)
```

### 支持同时解析多个号码
```php
<?php

include_once __DIR__.'/vendor/autoload.php';
use Ruchee\PhoneAttribution\Parse;

$parse = new Parse();

$phone_list = [
    '18520796150',
    '18745222111',
];
$data = $parse->parseList($phone_list);
print_r($data);

// 输出结果
Array
(
    [status] => 1
    [info] => 解析成功
    [data] => Array
        (
            [18520796150] => Array
                (
                    [province] => 广东
                    [city] => 广州
                    [postcode] => 510000
                    [prefix] => 020
                    [spname] => 中国联通
                )

            [18745222111] => Array
                (
                    [province] => 黑龙江
                    [city] => 齐齐哈尔
                    [postcode] => 161000
                    [prefix] => 0452
                    [spname] => 中国移动
                )

        )

)
```

### 可自定义解析数据源
```php
<?php

include_once __DIR__.'/vendor/autoload.php';
use Ruchee\PhoneAttribution\Parse;

$parse = new Parse(__DIR__.'/phone.dat');

// 更新数据源
// wget -c https://github.com/xluohome/phonedata/blob/master/phone.dat?raw=true -O phone.dat
```
