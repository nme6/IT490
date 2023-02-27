<?php

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('192.168.191.111', 5672, 'admin', 'admin');
$channel = $connection->channel();

#$custom_message = "Hello World from Ellis!";

$channel->queue_declare('MILESTONE 2', false, false, false, false);

$msg = new AMQPMessage("Hello World from Maximilian [Messaging]");
$channel->basic_publish($msg, '', 'MILESTONE 2');


echo " [x] Sent Hello World\n";

$channel->close();
$connection->close();
?>
