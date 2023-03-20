<?php
require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;

// Create a connection to RabbitMQ
$connection = new AMQPStreamConnection('192.168.191.111', 5672, 'admin', 'admin');
$channel = $connection->channel();

// Define the exchange and queue names
$exchangeName = 'user_info';
$queueName = 'FE2BE';

// Declare the exchange and queue
$channel->exchange_declare($exchangeName, 'fanout', false, false, false);
$channel->queue_declare($queueName, false, true, false, false);
$channel->queue_bind($queueName, $exchangeName);

// Define the callback function to process messages from the queue
$callback = function ($message) {
    $data = json_decode($message->getBody(), true);

    // Get the username and password from the message body
    	$username = $data['username'];
    	$password = $data['password'];

    // Sanitize the username and password data
    	$sanitizedUsername = filter_var($username, FILTER_SANITIZE_STRING);
    	$sanitizedPassword = filter_var($password, FILTER_SANITIZE_STRING);

    // Process the sanitized data
    	echo "Received message: " . $message->getBody() . "\n";
    	echo "Your username and password is $sanitizedUsername and $sanitizedPassword\n\n";
};

// Consume messages from the queue
$channel->basic_consume($queueName, '', false, true, false, false, $callback);

// Keep consuming messages until the channel is closed
while ($channel->is_open()) {
    $channel->wait();
}

// Close the connection
$channel->close();
$connection->close();
?>
