<?php

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = null;
$ips = array('192.168.191.111', '192.168.191.67');
//$ips = array('192.168.191.67');
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

$channel->queue_declare('regFE2BE', false, false, false, false, ['x-ha-policy' => 'all']);

$msg = new AMQPMessage("Hello World from Ellis [BackEnd]");
$channel->basic_publish($msg, '', 'regFE2BE');

echo " [x] Sent Hello World\n";

$channel->close();
$connection->close();

?>

