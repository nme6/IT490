<?php

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection('192.168.191.111', 5672, 'admin', 'admin');
$channel = $connection->channel();

//Used to set durable Queues (Third arg set to true)
//Must be set true on both consumers and producers to wrork
$channel->queue_declare('testQueue', false, true, false, false);


echo " [*] Waiting for messages from the New Task. To exit press CTRL+C\n";

$callback = function ($msg) {
	
	echo ' [x] Received ', $msg->body, "\n";
  
 	sleep(substr_count($msg->body, '.'));
  
	echo " [x] Done\n";

	//Used to acknowledge Messages that don't get consumed.
	//Essentially, if a work dies to will be passed to another one.
	//You can test with one consumer by continously CTRL+C
	
	$msg->ack();
};
//Evenly Distributes traffic across multiple workers depending on WORKLOAD
$channel->basic_qos(null, 1, null);

//Fourth argument is false, because true means "no-ack"
$channel->basic_consume('testQueue', '', false, false, false, false, $callback);

while (count($channel->callbacks)) {

	$channel->wait();
}

$channel->close();
$connection->close();
?>
