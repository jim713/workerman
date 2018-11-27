<?php

/**
 * Created by PhpStorm.
 * User: weiqi
 * Date: 2018/11/27
 * Time: 10:59
 */
ini_set('display_errors', 'on');

use Workerman\Worker;

require_once __DIR__ . '/vendor/autoload.php';

// 创建一个Worker监听2347端口，不使用任何应用层协议
$tcp_worker = new Worker("tcp://0.0.0.0:2347");

// 启动4个进程对外提供服务
$tcp_worker->count = 4;

// 当客户端发来数据时
$tcp_worker->onMessage = function ($connection, $data) {
    // 向客户端发送hello $data
    $connection->send('hello ' . $data);
};

// 运行worker
Worker::runAll();