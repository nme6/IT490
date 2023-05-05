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
$channel->queue_declare('pokeFE2BE', false, true, false, false, ['x-ha-policy' => 'all']);

//Define values


$choice = 'team build';
$userID = '10';
$member1 = 'none';
$member2 = 'pikachu';
$member3 = 'starie';
$member4 = 'pikachu';
$member5 = 'bulbasaur';
$member6 = 'pikachu';

	
// Publish the message to the queue
$messageBody = json_encode
(
	[
   		'choice' => $choice,
   		'user_id' => $userID,
		'member_1' => $member1,
		'member_2' => $member2,
		'member_3' => $member3,
		'member_4' => $member4,
		'member_5' => $member5,
		'member_6' => $member6,
	]
);

// Define the message to send
$message = new AMQPMessage($messageBody);
//$message = new AMQPMessage("Hello World from mock step4");


// Publish the message to the queue
$channel->basic_publish($message, '', 'pokeFE2BE');

//Echo Msg to console
echo "-={[Front-end] Sent message to the Back-end!}=-\n$messageBody\n";

//echo "\nSent Hello World!\n";

// Close the channel and the connection
$channel->close();
$connection->close();
?>
