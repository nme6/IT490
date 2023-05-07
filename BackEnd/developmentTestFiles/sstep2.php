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
    //echo "Received message from Front-end: " . $message->body . "\n";
	
    $data = json_decode($message->getBody(), true);

    // Get the data from the message body
    $username = $data['username'];
    $password = $data['password'];
    $confirm = $data['confirm'];
    $email = $data['email'];
    $firstname = $data['firstname'];
    $lastname = $data['lastname'];

    //Sanitize the username and password data
    $sanitizedUsername = filter_var($username, FILTER_SANITIZE_STRING);
    $sanitizedPassword = filter_var($password, FILTER_SANITIZE_STRING);
    $sanitizedConfirm = filter_var($confirm, FILTER_SANITIZE_STRING);
    $sanitizedEmail = filter_var($email, FILTER_SANITIZE_EMAIL);
    $sanitizedFirstname = filter_var($firstname, FILTER_SANITIZE_EMAIL);
    $sanitizedLastname = filter_var($lastname, FILTER_SANITIZE_EMAIL);


    //Compare Passwords (function was giving me issues?)
    if ($sanitizedPassword !== $sanitizedConfirm) 
    {
	echo "[The passwords do not match! Try Again!]\n";
        $matchingPassword = false;
    }
    else /// This else statement
    {
	echo "Received message from Front-end: " . $message->body . "\n";
	echo "[The passwords match! Hooray!]\n";
	$matchingPassword = true;
    
    	// Hash the password using bcrypt with the salt
    	$hashPassword = password_hash($sanitizedPassword, PASSWORD_BCRYPT); 

    	//Funnel everything back into the json. Also add the salt value.
    	$finalMessageBody = json_encode
    	(	
        	[
	       		'username' => $sanitizedUsername,
               		'password' => $hashPassword,
               		'email' => $sanitizedEmail,
               		'firstname' => $sanitizedFirstname,
               		'lastname' => $sanitizedLastname
        	]
    	);

    	// Send a new message to the final.php script 
    	$finalConnection = new AMQPStreamConnection('192.168.191.111', 5672, 'admin', 'admin');
    	$finalChannel = $finalConnection->channel();

    	$finalChannel->queue_declare('BE2DB', false, false, false, false);
        
    	$finalMessage = new AMQPMessage($finalMessageBody);
    	$finalChannel->basic_publish($finalMessage, '', 'BE2DB');

    	$finalChannel->close();
    	$finalConnection->close();
    }
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

