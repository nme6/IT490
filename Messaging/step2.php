<?php
require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

// Create a connection to RabbitMQ
$connection = new AMQPStreamConnection('192.168.191.111', 5672, 'admin', 'admin');
$channel = $connection->channel();

// Declare a queue for receiving messages
$channel->queue_declare('FE2BE', false, false, false, false);

echo "-={[Back-end] Waiting for Front-end messages. To exit press CTRL+C}=-\n";

// Define the callback function to process received messages
$callback = function ($message) use ($channel) {
    echo "Received message from Front-end: " . $message->body . "\n";

    // Send a new message to the final.php script
    $finalConnection = new AMQPStreamConnection('192.168.191.111', 5672, 'admin', 'admin');
    $finalChannel = $finalConnection->channel();

    $finalChannel->queue_declare('BE2DB', false, false, false, false);

    //$finalMessageBody = "Received msg: " . $message->body;
    $finalMessageBody = $message->body;

    $finalMessage = new AMQPMessage($finalMessageBody);

    $finalChannel->basic_publish($finalMessage, '', 'BE2DB');

    $finalChannel->close();
    $finalConnection->close();
};

// Start consuming messages from the queue
$channel->basic_consume('FE2BE', '', false, true, false, false, $callback);

// Keep consuming messages until the channel is closed
while ($channel->is_open()) {
    $channel->wait();
}

// Close the channel and the connection
$channel->close();
$connection->close();
?>

