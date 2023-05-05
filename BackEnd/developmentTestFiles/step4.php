<?php 
require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

// Create a connection to RabbitMQ
$connection = new AMQPStreamConnection('192.168.191.111', 5672, 'admin', 'admin');
$channel = $connection->channel();

// Declare a queue for receiving messages
$channel->queue_declare('DB2BE', false, false, false, false);

echo "-={[Back-end] Waiting for confirmation from Database. To exit press CTRL+C}=-\n";


$callback = function ($message) {
    echo "Received message from Front-end: " . $message->body . "\n";

    $data = json_decode($message->getBody(), true);

    // Get the data from the message body

    $userExists = $data['userExists'];

    echo "User Status: " . $message->body . "\n";

};
// Start consuming messages from the queue
$channel->basic_consume('DB2BE', '', false, true, false, false, $callback);

// Keep consuming messages until the channel is closed
while ($channel->is_open()) {
    $channel->wait();
}

// Close the channel and the connection
$channel->close();
$connection->close();
?>


