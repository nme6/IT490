<?php
	/*
	require_once __DIR__.'/vendor/autoload.php';
	use PhpAmqpLib\Connection\AMQPStreamConnection;
	use PhpAmqplib\Message\AMQPMessage;
	 */
	#testing a basic php file to run, worked successfully
	$fruits = ["apple", "banana", "orange"];
	for($i=0;$i<count($fruits);$i++){
		echo "Index of ", $i, "= ", $fruits[$i], "\n";
	}
?>
