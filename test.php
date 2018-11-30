<?php
use Workerman\Worker;

require_once __DIR__ . '/Autoloader.php';

// //创建一个Worker监听2344的端口，使用http协议进行通讯
// $http_worker = new Worker("http://0.0.0.0:2344");

// // 启动4个进程对外进行服务
// $http_worker->count = 4;

// // 接收到浏览器发送的数据时回复hello world给浏览器
// $http_worker->onMessage = function ($connection, $data) {
//     // 向浏览器发送hello world
//     $connection->send('hello world!');
// };

// 聊天室
$global_uid = 0;

// 当客户端连上来时分配uid，并保存连接，并通知所有客户端
function handle_connection($connection)
{
    global $text_worker, $global_uid;
    // 为这个连接分配一个uid
    $connection->uid = ++$global_uid;
}

// 当客户端发送消息过来时，转发给所有人
function handle_message($connection, $data)
{
    global $text_worker;
    foreach ($text_worker->connections as $conn) {
        $conn->send("user[{$connection->uid}] said: $data");
    }
}

// 当客户端断开时，广播给所有客户端
function handle_close($connection)
{
    global $text_worker;
    foreach ($text_worker->connections as $conn) {
        $conn->send("user[{$connection->uid}] logout");
    }
}

// 创建一个文本协议的Worker监听2347接口
$text_worker = new Worker("text://0.0.0.0:2347");

// 只启动1个进程，这样方便客户端之间传输数据
$text_worker->count = 1;

$text_worker->onConnect = 'handle_connection';
$text_worker->onMessage = 'handle_message';
$text_worker->onClose = 'handle_close';


Worker::runAll();
