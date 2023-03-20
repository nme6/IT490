<?php

require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

// Create a connection to RabbitMQ
$connection = new AMQPStreamConnection('192.168.191.111', 5672, 'admin', 'admin');
$channel = $connection->channel();

// Declare the queue
$channel->queue_declare('regDB2BE', false, false, false, false);

echo "-={[BackEnd] Waiting for Database confirmation. To exit press CTRL+C}=-\n";

// Define the callback function to process messages from the queue
$callback = function ($message) use ($channel) {

    $data = $message->body;

   // $userExists = $data['userExists'];    
    echo "Received user status: " . $message->body . "\n";



    $userStatusConnection = new AMQPStreamConnection('192.168.191.111', 5672, 'admin', 'admin');
    $userStatusChannel = $userStatusConnection->channel();
    $userStatusChannel->queue_declare('regBE2FE', false, false, false, false);
    $userStatusMessage = new AMQPMessage($data);
    $userStatusChannel->basic_publish($userStatusMessage, '', 'regBE2FE');
    $userStatusChannel->close();
    $userStatusConnection->close();
};

// Consume messages from the queue
$channel->basic_consume('regDB2BE', '', false, true, false, false, $callback);

// Keep consuming messages until the channel is closed
while ($channel->is_open()) {
    $channel->wait();
}

// Close the connection
$channel->close();
$connection->close();
