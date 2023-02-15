<?php

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection('192.168.191.22', 5672, 'admin', 'admin');
$channel = $connection->channel();


$channel->queue_declare('declare', false, false, false, false);


echo " [*] Waiting for messages. To exit press CTRL+C\n";

$callback = function ($msg) {
	echo ' [x] Received ', $msg->body, "\n";
};

$channel->basic_consume('declare', '', false, true, false, false, $callback);

while ($channel->is_open()) {

	$channel->wait();
}
$channel->close();
$connection->close();
?>
