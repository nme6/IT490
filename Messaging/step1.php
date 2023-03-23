<?php
require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$payload = 'Hello World!';

// Create a connection to RabbitMQ
$connection = new AMQPStreamConnection('192.168.191.111', 5672, 'admin', 'admin');
$channel = $connection->channel();

// Declare a queue for sending messages
$channel->queue_declare('FE2BE', false, false, false, false);

// Define the message to send
$message = new AMQPMessage($payload);

// Publish the message to the queue
$channel->basic_publish($message, '', 'FE2BE');

//Echo Msg to console
echo "-={[Front-end] Sent message to the Back-end!}=-\n$payload\n";


// Close the channel and the connection
$channel->close();
$connection->close();
?>

