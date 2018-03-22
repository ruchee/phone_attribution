<?php

include_once __DIR__.'/vendor/autoload.php';
use Ruchee\PhoneAttribution\Parse;

$parse = new Parse();  // 使用默认数据源
// $parse = new Parse(__DIR__.'/data/phone.dat');  // 使用自定义数据源


$phone = '13713462969';
$data  = $parse->parseOne($phone);
print_r($data);


$phone_list = [
    '18520796150',
    '18745222111',
];
$data = $parse->parseList($phone_list);
print_r($data);
