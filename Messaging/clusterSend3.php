<?php

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = null;
$ips = array('192.168.191.111', '192.168.191.67');

foreach ($ips as $ip) {
    try {
	$connection = new AMQPStreamConnection($ip, 5672, 'admin', 'admin');
	echo "Connected to RabbitMQ instance at: $ip\n";
        break;
    } catch (Exception $e) {
        continue;
    }
}

if (!$connection) {
    die("Could not connect to any RabbitMQ instance.");
}

$channel = $connection->channel();

//$channel->queue_declare('MILESTONE 2', false, true, false, false);

$msg = new AMQPMessage("Hello World from Maximilian [Messaging]");
$channel->basic_publish($msg, '', 'MILESTONE 2');

echo " [x] Sent Hello World\n";

$channel->close();
$connection->close();

?>
