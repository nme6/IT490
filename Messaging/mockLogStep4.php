<?php

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

//$userExists = true;
//$isValid = true;

// Create a connection to RabbitMQ
$connection = null;
$ips = array('192.168.191.111', '192.168.191.67', '192.168.191.215');

foreach ($ips as $ip) {
    try {
        $connection = new AMQPStreamConnection($ip, 5672, 'admin', 'admin');
        echo "Connected to RabbitMQ instance at: $ip\n";
        break;
    } catch (Exception $e) {
        continue;
    }
}

if (!$connection) {
    die("Could not connect to any RabbitMQ instance.");
}

$channel = $connection->channel();

// Declare a queue for sending messages
$channel->queue_declare('logBE2FE', false, false, false, false, ['x-ha-policy' => 'all']);

//Define values
$userAuth = true;
$isValid = true;
$username = "yupol";

// Publish the message to the queue
$messageBody = json_encode
(
	[
   		'userAuth' => $userAuth,
		'isValid' => $isValid,
		'username' => $username
	]
);

// Define the message to send
$message = new AMQPMessage($messageBody);
//$message = new AMQPMessage("Hello World from mock step4");



// Publish the message to the queue
$channel->basic_publish($message, '', 'logBE2FE');

//Echo Msg to console
echo "-={[Front-end] Sent message to the Back-end!}=-\n$messageBody\n";

//echo "\nSent Hello World!\n";

// Close the channel and the connection
$channel->close();
$connection->close();
?>
