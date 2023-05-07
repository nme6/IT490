<?php

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection('192.168.191.111', 5672, 'admin', 'admin');
$channel = $connection->channel();

//Set parameter 3 to true, keeps the queue durable during restart
$channel->queue_declare('testQueue', false, true, false, false);

echo " [*] Waiting for messages. To exit press CTRL+C\n";

//new sleep addition lets it wait each time there is a period. Useful in testing multitasking
$callback = function ($msg) {
    echo ' [x] Received ', $msg->body, "\n";
    sleep(substr_count($msg->body, '.'));
    echo " [x] Done\n";
    //this ack keeps the queue alive until the ack is sent. So if the consumer stops for any reason, another consumer will pick it up if no ack was sent.
    $msg->ack();
};

//this keeps the workflow from getting backed up, the 1 refers to the prefetch, which is the number of tasks a worker can do at a single time. If the worker is busy, rabbitmq will send the task to another worker who is not busy. useful for when there are large tasks and multiple smaller tasks-> dont want the small ones getting backed up on a machine that is stuck doing the larger tasks.
$channel->basic_qos(null, 1, null);
$channel->basic_consume('testQueue', '', false, false, false, false, $callback);

while ($channel->is_open()) {
    $channel->wait();
}

$channel->close();
$connection->close();
