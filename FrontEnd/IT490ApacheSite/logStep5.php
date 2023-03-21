<?php

require_once '/home/neil/IT490/IT490/FrontEnd/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

// Create a connection to RabbitMQ
$connection = new AMQPStreamConnection('192.168.191.111', 5672, 'admin', 'admin');
$channel = $connection->channel();

// Declare the queue
$channel->queue_declare('logBE2FE', false, false, false, false);

echo "-={[FrontEnd] Waiting for Back-end messages. To exit press CTRL+C}=-\n";

// Define the callback function to process messages from the queue
$callback = function ($message) {

	$data = json_decode($message->getBody(),true);
	$isValid = $data['isValid'];


        if ($isValid == false)
        {
		echo "\n[Incorrect format for Username/Password ]\n";

		//TODO for Neil: Redirects Page
        }


        if ($isValid == true)
        {
                $userAuth = $data['userAuth'];

		//echo "The value of userAuth is: " . $userAuth . "\n";

                if ($userAuth == false){
			echo "\nInvalid Username or Password.\n";

			//TODO for Neil: Redirects Page

                } else {
			echo "\nSuccessfully Logged in!\n";

			//TODO for Neil: Redirects Page
		
                }
        }

};

// Consume messages from the queue
$channel->basic_consume('logBE2FE', '', false, true, false, false, $callback);

// Keep consuming messages until the channel is closed
while ($channel->is_open()) {
    $channel->wait();
}

// Close the connection
$channel->close();
$connection->close();

