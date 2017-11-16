<?php
//引入Workerman/Autoloader.php
use Workerman\Worker;
require_once 'workerman/Autoloader.php';
// 创建一个Worker监听2345端口，使用http协议通讯
$ws_worker = new Worker("websocket://0.0.0.0:2346");
// 启动4个进程对外提供服务
$ws_worker->count = 4;
// 接收到浏览器发送的数据时回复hello world给浏览器
$ws_worker->onMessage = function($connection, $data)
{
    $connection->send('hello world');
};
// 运行worker
Worker::runAll();