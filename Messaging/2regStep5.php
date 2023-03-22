<?php

require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('192.168.191.111', 5672, 'admin', 'admin');
$channel = $connection->channel();

// Declare the queue
$channel->queue_declare('regBE2FE', false, false, false, false);

// Define the callback function
$callback = function ($message) {
    echo "Received message from Back-end: " . $message->body . "\n";

    $data = json_decode($message->getBody(), true);

    $isValid = $data['isValid'];
    $userExists = $data['userExists'];

    if ($isValid == false)
        {
        echo "\n[Incorrect format for Registration Info]\n";

        //TODO for Neil: Redirects Page

        }

        if ($isValid == true)
        {
                $userExists = $data['userExists'];

                if ($userExists == false){
            echo "\nSuccessfully Registered!\n";

            //TODO for Neil:

                } else {
            echo "\nUsername / Email is already taken!\n";

            //TODO for Neil
                }
        }
};

// Consume messages from the queue
$channel->basic_consume('regBE2FE', '', false, true, false, false, $callback);

// Keep consuming messages until the channel is closed or 3 seconds have elapsed
$timeout = 3; // Timeout in seconds
$start_time = time(); // Get current time
while ($channel->is_open()) {
    $read = array($connection->getIO()->getStream()); // Select the connection
    $write = null;
    $except = null;
    $timeout_seconds = $timeout - (time() - $start_time); // Calculate remaining time
    if ($timeout_seconds <= 0) {
        break; // Break the loop if the timeout has elapsed
    }
    // Wait until a message arrives or the timeout has elapsed
    if (stream_select($read, $write, $except, $timeout_seconds)) {
        $channel->wait(); // Consume the message
    }
}

// Close the connection
$channel->close();
$connection->close();
