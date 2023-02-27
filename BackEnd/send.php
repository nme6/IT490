<?php

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

#Establishing a new connection to the RabbitMQ hosting machine
$connection = new AMQPStreamConnection('192.168.191.111', 5672, 'admin', 'admin');
$channel = $connection->channel();

#Declaring the queue with the associated server
$channel->queue_declare('MILESTONE 2', false, false, false, false);

#Creating the message for milestone 2
$msg = new AMQPMessage('Hello World from Ellis [Backend]');

#Publishing the message for milestone 2 with the associated queue 
$channel->basic_publish($msg, '', 'MILESTONE 2');

#Displaying a successful output to the current terminal
echo " [x] Sent 'Hello World from Ellis!'\n";

#Closing the channel and the connection
$channel->close();
$connection->close();
?>
