<?php

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = null;
$ips = array('192.168.191.111', '192.168.191.67', '192.168.191.215');

foreach ($ips as $ip) {
    try {
        $connection = new AMQPStreamConnection($ip, 5672, 'admin', 'admin');
        echo "Connected to RabbitMQ instance at $ip\n";
        break;
    } catch (Exception $e) {
        continue;
    }
}

if (!$connection) {
    die("Could not connect to any RabbitMQ instance.");
}

$channel = $connection->channel();

$channel->queue_declare('pokeBE2FE', false, true, false, false, ['x-ha-policy'=>'all']);

echo " [*] Waiting for messages. To exit press CTRL+C\n";


$callback = function ($msg) {
    $data = json_decode($msg->getBody(), true);
    echo ' [x] Received ', print_r($data), "\n";
    $choiceNew = $data['choice'];
    echo $choiceNew;
    //echo "Connected to RabbitMQ instance at: $ip\n\n";
};

while (true) {
    try {
        $channel->basic_consume('pokeBE2FE', '', false, true, false, false, $callback);
        while (count($channel->callbacks)) {
            $channel->wait();
        }
    } catch (ErrorException $e) {
        // Handle Error
        echo "Caught ErrorException: " . $e->getMessage();
    } catch (PhpAmqpLib\Exception\AMQPConnectionClosedException $e) {
        // Handle the AMQPConnectionClosedException error
        echo "Caught AMQPConnectionClosedException: " . $e->getMessage() . "\n\n";
        foreach ($ips as $ip) {
            try {
                $connection = new AMQPStreamConnection($ip, 5672, 'admin', 'admin');
                echo "Connected to RabbitMQ instance at $ip\n";
                break;
            } catch (Exception $e) {
                continue;
            }
        }
        if (!$connection) {
            die("Could not connect to any RabbitMQ instance.");
        }
        $channel = $connection->channel();
        $channel->queue_declare('pokeBE2FE', false, true, false, false, ['x-ha-policy'=>'all']);
    }
}

$channel->close();
$connection->close();
?>
