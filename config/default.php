<?php
/**
 * Created by PhpStorm.
 * User:
 * Date: 2020/1/20
 * Time: 11:03 上午
 */

return [
    'http' => [
        'host' => '0.0.0.0',
        'port' => 9501,
        'tcpEnable' => 1, // 在开启http服务的时候是否同时开启tcp服务
        'setting' => [
            'worker_num' => 2, // swoole启动的work进程数
        ],
    ],
    'tcp' => [
        'host' => '0.0.0.0',
        'port' => 9502,
        'setting' => [
            'worker_num' => 2
        ]
    ],
    'auto_reload' => true,// 是否自动重新加载文件
];