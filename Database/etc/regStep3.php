<?php

require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;


// Create a connection to RabbitMQ
$connection = new AMQPStreamConnection('192.168.191.111', 5672, 'admin', 'admin');
$channel = $connection->channel();

// Declare the queue
$channel->queue_declare('regBE2DB', false, false, false, false);

echo "-={[Database] Waiting for Back-end messages. To exit press CTRL+C}=-\n";

// Define the callback function to process messages from the queue
//$callback = function ($message) use ($servername, $username, $password, $dbname) {
$callback = function ($message) use ($channel){
    // Assign $data from JSON   
    $data = json_decode($message->getBody(), true);

    echo "Received message from Back-end: " . $message->body . "\n";
    // Get the data from the message body
    $username = $data['username'];
    $password = $data['password'];
    $email = $data['email'];
    $firstname = $data['firstname'];
    $lastname = $data['lastname'];

	// Connect to the database
	$servername = "localhost";
	$username_db = "test";
	$password_db = "test";
	$dbname = "test";

	$conn = mysqli_connect($servername, $username_db, $password_db, $dbname);

	// Check if the connection is successful
	if (!$conn) {
    	die("Connection failed: " . mysqli_connect_error());
	}
	
	// Check if the user already exists in the database
    	$sql_check = "SELECT * FROM users WHERE username = '$username' OR email = '$email'";
    	$result = mysqli_query($conn, $sql_check);

    	if (mysqli_num_rows($result) > 0) {
        // User already exists
        	echo "User already exists in the database.\n";
		$userExists = true;
    	} else {
        // User does not exist
	// Insert the user data into the database
	$sql = "INSERT INTO users (username, password, email, firstname, lastname) VALUES ('$username', '$password', '$email', '$firstname', '$lastname')";
	
	if (mysqli_query($conn, $sql)) {
    	
	echo "New record created successfully";
	$userExists = false;
	} else {
    	echo "Error: " . $sql . "<br>" . mysqli_error($conn);
	
	}
}
	// Close the database connection
	mysqli_close($conn);
	
    	
	        $dbmessageBody = json_encode(
                [
                        'userExists' => $userExists
                ]	
	);

	//echo "Received message from Back-end: " . $dbmessageBody->body . "\n";
        // Send a new message to the database
        $dbconnection = new AMQPStreamConnection('192.168.191.111', 5672, 'admin', 'admin');
        $dbchannel = $dbconnection->channel();
        $dbchannel->queue_declare('regDB2BE', false, false, false, false);
        $dbmessage = new AMQPMessage($dbmessageBody);
        $dbchannel->basic_publish($dbmessage, '', 'regDB2BE');
        $dbchannel->close();
        $dbconnection->close();

	};


// Consume messages from the queue with the defined callback function
$channel->basic_consume('regBE2DB', '', false, true, false, false, $callback);

// Keep the script running to receive messages
while (count($channel->callbacks)) {
    $channel->wait();
}

// Close the connection
$channel->close();
$connection->close();
