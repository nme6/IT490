<?php
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

// Define the list of RabbitMQ nodes in the cluster
$hosts = array(
    '192.168.191.111',
    '192.168.191.67',
    '192.168.191.240'
);

// Create a new AMQPStreamConnection object that connects to the cluster
$connection = new AMQPStreamConnection($hosts, 5672, 'admin', 'admin');

// Create a channel on the connection
$channel = $connection->channel();

// Declare the queue with the associated server
$channel->queue_declare('MS4', false, false, false, false);

// Create the message for milestone 2
$msg = new AMQPMessage('Hello World from Ellis [Backend]');

// Publish the message for milestone 2 with the associated queue
$channel->basic_publish($msg, '', 'MS4');

// Display a successful output to the current terminal
echo " [x] Sent 'Hello World from Ellis!'\n";

// Close the channel and the connection
$channel->close();
$connection->close();
?>
