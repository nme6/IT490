<?php

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$userExists = true;
$isValid = true;

// Create a connection to RabbitMQ
$connection = new AMQPStreamConnection('192.168.191.111', 5672, 'admin', 'admin');
$channel = $connection->channel();

// Declare a queue for sending messages
$channel->queue_declare('regBE2FE', false, false, false, false);

// Publish the message to the queue
$messageBody = json_encode
(
	[
   		'userExists' => $userExists,
		'isValid' => $isValid
	]
);

// Define the message to send
$message = new AMQPMessage($messageBody);
//$message = new AMQPMessage("Hello World from mock step4");



// Publish the message to the queue
$channel->basic_publish($message, '', 'regBE2FE');

//Echo Msg to console
echo "-={[Front-end] Sent message to the Back-end!}=-\n$messageBody\n";

//echo "\nSent Hello World!\n";

// Close the channel and the connection
$channel->close();
$connection->close();
?>
