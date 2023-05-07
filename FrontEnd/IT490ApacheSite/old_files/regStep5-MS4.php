<?php

require_once '/home/neil/IT490/IT490/FrontEnd/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

// Create a connection to RabbitMQ
//$connection = new AMQPStreamConnection('192.168.191.111', 5672, 'admin', 'admin');

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

// Declare the queue
//$channel->queue_declare('regBE2FE', false, false, false, false);
$channel->queue_declare('regBE2FE', false, false, false, false, ['x-ha-policy'=>'all']);

echo "-={[FrontEnd Reg5] Waiting for Back-end messages. To exit press CTRL+C}=-\n";

// Define the callback function to process messages from the queue
$callback = function ($message) use ($channel) {
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

                //echo "The value of userAuth is: " . $userExists . "\n";

                if ($userExists == false){
			echo "\nSuccessfully Registered!\n";
			//TODO for Neil: Redirects Page

                } else {
			echo "\nUsername / Email is already taken!\n";

			//TODO for Neil: Redirects Page
                }
        }


	//echo "The value of userExists is: " . $userExists . "\n";
	//if ($userExists == true){
	//	echo "\nThe Username / Email is already taken.\n";
	//} else {
	//	echo "\nSuccessfully Registered Account!\n";
	//}
};

// Consume messages from the queue
//$channel->basic_consume('regBE2FE', '', false, true, false, false, $callback);

// Keep consuming messages until the channel is closed
//while ($channel->is_open()) {
//    $channel->wait();
//}

while (true) {
    try {
        $channel->basic_consume('regBE2FE', '', false, true, false, false, $callback);
        while (count($channel->callbacks)) {
            $channel->wait();
        }
    } catch (ErrorException $e) {
        // Handle Error
        echo "Caught ErrorException: " . $e->getMessage();
    } catch (PhpAmqpLib\Exception\AMQPConnectionClosedException $e) {
        // Handle the AMQPConnectionClosedException error
        echo "Caught AMQPConnectionClosedException: " . $e->getMessage() . "\n\n";
        foreach ($ips as $ip) {
            try {
                $connection = new AMQPStreamConnection($ip, 5672, 'admin', 'admin');
                echo "Connected to RabbitMQ instance at $ip\n";
                break;
            } catch (Exception $e) {
                continue;
            }
        }
        if (!$connection) {
            die("Could not connect to any RabbitMQ instance.");
        }
        $channel = $connection->channel();
        $channel->queue_declare('regBE2FE', false, false, false, false, ['x-ha-policy'=>'all']);
    }
}


// Close the connection
$channel->close();
$connection->close();

