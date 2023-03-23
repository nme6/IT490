<?php

require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

// Create a connection to RabbitMQ
$connection = new AMQPStreamConnection('192.168.191.111', 5672, 'admin', 'admin');
$channel = $connection->channel();

// Declare the queue
$channel->queue_declare('BE2FE', false, false, false, false);

echo "-={[FrontEnd] Waiting for Back-end messages. To exit press CTRL+C}=-\n";

// Define the callback function to process messages from the queue
$callback = function ($message) {
	echo "Received message from Back-end: " . $message->body . "\n";

	$data = json_decode($message->getBody(), true);

	$userExists = $data['userExists'];

	echo "The value of userExists is: " . $userExists . "\n";

	if ($userExists == true){
		echo "\nThe Username / Email is already taken.\n";
	} else {
		echo "\nSuccessfully Registered Account!\n";
	}
};

// Consume messages from the queue
$channel->basic_consume('BE2FE', '', false, true, false, false, $callback);

// Keep consuming messages until the channel is closed
while ($channel->is_open()) {
    $channel->wait();
}

// Close the connection
$channel->close();
$connection->close();

