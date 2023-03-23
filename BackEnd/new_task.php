<?php

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

#Establishing a new connection to the RabbitMQ hosting machine
$connection = new AMQPStreamConnection('192.168.191.111', 5672, 'admin', 'admin');
$channel = $connection->channel();

#Declaring the queue with the associated server

//The third parameter is set to true, this means that the queue is marked as durable. This lets it stay alive during a restart. VERY USEFUL !!!!!!
$channel->queue_declare('testQueue', false, true, false, false);


$data = implode(' ', array_slice($argv, 1));

if (empty($data)) {
	$data = "Hello World!";
}
//the delivery mode marks the messages as persistent, this means after a RabbitMQ restart the messages are still available to be consumed by the other machine. VERY USEFUL!!!!
$msg = new AMQPMessage(
	$data,
	array('delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT)
);

$channel->basic_publish($msg, '', 'testQueue');

echo ' [x] Sent ', $data, "\n";


#Closing the channel and the connection
$channel->close();
$connection->close();
?>
