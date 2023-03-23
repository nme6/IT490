<?php

require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;


// Create a connection to RabbitMQ
$connection = new AMQPStreamConnection('192.168.0.11', 5672, 'guest', 'guest');
$channel = $connection->channel();

// Declare the queue
$channel->queue_declare('BE2DB', false, false, false, false);

echo "-={[Database] Waiting for Back-end messages. To exit press CTRL+C}=-\n";

// Define the callback function to process messages from the queue
$callback = function ($message) use ($servername, $username, $password, $dbname) {

    // Assign $data from JSON   
    $data = json_decode($message->getBody(), true);

    // Get the data from the message body
    $vusername = $data['username'];
    $vpassword = $data['password'];
    $vemail = $data['email'];
    $vfirstname = $data['firstname'];
    $vlastname = $data['lastname'];

    // MySQL server credentials
    $servername = "192.168.191.240"; // replace with the IP address of your MySQL server
    $username = "test"; // replace with the username of your MySQL user
    $password = "test"; // replace with the password of your MySQL user
    $dbname = "test"; // replace with the name of your MySQL database
    
    // Create a connection to the MySQL server
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare the SQL statement to insert the data into the "registration" table
    $stmt = $conn->prepare("INSERT INTO registration (username, password, email, firstname, lastname) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $vusername, $vpassword, $vemail, $vfirstname, $vlastname);

    // Execute the SQL statement
    if ($stmt->execute() === TRUE) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement and the connection
    $stmt->close();
    $conn->close();

    echo "Received message from Back-end: " . $message->body . "\n";
};

// Consume messages from the queue with the defined callback function
$channel->basic_consume('BE2DB', '', false, true, false, false, $callback);

// Keep the script running to receive messages
while (count($channel->callbacks)) {
    $channel->wait();
}

// Close the connection
$channel->close();
$connection->close();
