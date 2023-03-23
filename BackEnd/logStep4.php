<?php

require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

// Create a connection to RabbitMQ
$connection = new AMQPStreamConnection('192.168.191.111', 5672, 'admin', 'admin');
$channel = $connection->channel();

// Declare the queue
$channel->queue_declare('logDB2BE', false, false, false, false);

echo "-={[BackEnd Log4] Waiting for Database confirmation. To exit press CTRL+C}=-\n";

// Define the callback function to process messages from the queue
$callback = function ($message) use ($channel) {

    //data= $message->body;
	
    		
    $data  = json_decode($message->getBody(), true);

    $password = $data['password'];
    $username = $data['username'];
    $hash = $data['hash'];
    $userFound = $data['userFound'];


    if(password_verify($password, $hash) && $userFound == true) {
	    echo "Username and Password are valid" . "\n";
	    $userAuth = true;
	    $isValid = true;
    } else {
	    echo "Username or Password is invalid" . "\n";
	    $userAuth = false;
	    $isValid = true;
    }

    $authBody = json_encode (
	    [
		    'userAuth' => $userAuth,
		    'username' => $username,
		    'isValid' => $isValid,
	    ]
    );



   // $userExists = $data['userExists'];    
    //echo "Received user status: " . $message->body . "\n";



    $userStatusConnection = new AMQPStreamConnection('192.168.191.111', 5672, 'admin', 'admin');
    $userStatusChannel = $userStatusConnection->channel();
    $userStatusChannel->queue_declare('logBE2FE', false, false, false, false);
    $userStatusMessage = new AMQPMessage($authBody);
    $userStatusChannel->basic_publish($userStatusMessage, '', 'logBE2FE');
    $userStatusChannel->close();
    $userStatusConnection->close();
};

// Consume messages from the queue
$channel->basic_consume('logDB2BE', '', false, true, false, false, $callback);

// Keep consuming messages until the channel is closed
while ($channel->is_open()) {
    $channel->wait();
}

// Close the connection
$channel->close();
$connection->close();
