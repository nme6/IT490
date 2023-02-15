<?php

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('192.168.191.22', 5672, 'admin', 'admin');
$channel = $connection->channel();

$channel->queue_declare('howdy', false, false, false, false);

$msg = new AMQPMessage('Hello World');
$channel->basic_publish($msg, '', 'howdy');


echo " [x] Sent 'Hello World!'\n";

$channel->close();
$connection->close();
?>
