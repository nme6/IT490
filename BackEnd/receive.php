<?php

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

#Establish the new connection to the RabbitMQ hosting machine and apply credentials
$connection = new AMQPStreamConnection('192.168.191.111', 5672, 'admin', 'admin');
$channel = $connection->channel();

#Declaring the queue with the associated marker
$channel->queue_declare('MS4', false, false, false, false);

#Passive message to signify that the machine is listening for incoming connections
echo " [*] Waiting for messages. To exit press CTRL+C\n";

#Function for when a message is read from the queue
$callback = function ($msg) {
	echo ' [x] Received ', $msg->body, "\n";
};

#Calling the function when the message is consumed from the queue
$channel->basic_consume('MS4', '', false, true, false, false, $callback);

#Loop to keep the connection alive and listening until the user exits 
while ($channel->is_open()) {

	$channel->wait();
}

#Close the channel and connection
$channel->close();
$connection->close();
?>
