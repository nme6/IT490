<?php

require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

// Create a connection to RabbitMQ
//$connection = new AMQPStreamConnection('192.168.191.111', 5672, 'admin', 'admin');

$connection = null;
$ips = array('192.168.191.111', '192.168.191.67');
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

// Declare the queue
$channel->queue_declare('regDB2BE', false, false, false, false, ['x-ha-policy'=>'all']);

echo "-={[BackEnd Reg4] Waiting for Database confirmation. To exit press CTRL+C}=-\n";

// Define the callback function to process messages from the queue
$callback = function ($message) use ($channel) {

    $status = $message->body;
    $data = json_decode($message->getBody(), true);
    $isValid = true; 

    $userExists= $data['userExists'];
    
    $data = json_encode
    (
	    [
		    'isValid' => $isValid,
		    'userExists' => $userExists,
	    ]
    );

    echo "Received user status: " . $message->body . "\n";
        $userStatusConnection = null;
        $ips = array('192.168.191.111', '192.168.191.67');
        //global $actual_ip;
        //$actual_ip = null;
    	foreach ($ips as $ip) {
                try {
                        $userStatusConnection = new AMQPStreamConnection($ip, 5672, 'admin', 'admin');
                        echo "Connected to RabbitMQ instance at $ip\n";
                        //$actual_ip = $ip;
                        break;
                } catch (Exception $e) {
                        continue;
                        }
                 // return $actual_ip;
                }

    	if (!$userStatusConnection) {
                die("Could not connect to any RabbitMQ instance.");
                }

    //$userStatusConnection = new AMQPStreamConnection('192.168.191.111', 5672, 'admin', 'admin');
    $userStatusChannel = $userStatusConnection->channel();
    $userStatusChannel->queue_declare('regBE2FE', false, false, false, false);
    $userStatusMessage = new AMQPMessage($data);
    $userStatusChannel->basic_publish($userStatusMessage, '', 'regBE2FE');
    $userStatusChannel->close();
    $userStatusConnection->close();
};

// Consume messages from the queue
while (true) {
    try {
        $channel->basic_consume('regDB2BE', '', false, true, false, false, $callback);
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
        $channel->queue_declare('regDB2BE', false, false, false, false, ['x-ha-policy'=>'all']);
    }
}

