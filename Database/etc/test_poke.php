<?php

require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = null;
$ips = array('192.168.191.111', '192.168.191.67', '192.168.191.215');
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
$channel->queue_declare('pokeAPIBE2DB', false, true, false, false, ['x-ha-policy'=>'all']);


$callback = function ($message) use ($channel){
    // Assign $data from JSON   
    $data = json_decode($message->getBody(), true); 
    
     echo "Received message from Back-end: " . $message->body . "\n";
    // Get the data from the message body
    $pokemon_exists=$data['exists'];
    $choice = $data['choice'];
    //$pokemon_name =$data['pokemon_name'];
    //$types =$data['types'];mysqli_query($conn, $sql_insert);
    //$choice = $data['choice'];
    
       if ($pokemon_exists != true){
		if ($choice == 'pokemon type') {
	   $pokemon_name = $data['pokemon_name'];
       	   $types = $data['types'];
    
    
	// Connect to the database
	$servername = "localhost";
	$username_db = "test";
	$password_db = "test";
	$dbname = "test";
	
	$conn = mysqli_connect($servername, $username_db, $password_db, $dbname);
	
	 // Insert the pokemon into the database
        $sql_insert = "INSERT INTO `pokemon types` (`pokemon_name`, `types`) VALUES ('$pokemon_name', '$types')";
        mysqli_query($conn, $sql_insert);
        
	} elseif ($choice == 'damage type') {
		$damage_type = $data['damage_type'];
		$double_from = $data['double_from'];
		$double_to = $data['double_to'];
		$half_from = $data['half_from'];
		$half_to = $data['half_to'];
		$no_to = $data['no_to'];
		$no_from = $data['no_from'];
		// Connect to the database
	$servername = "localhost";
	$username_db = "test";
	$password_db = "test";
	$dbname = "test";
	
	$conn = mysqli_connect($servername, $username_db, $password_db, $dbname);

	$sql_insert = "INSERT INTO damage (damage_type, double_from, double_to, half_from, half_to, no_to, no_from)
VALUES ('$damage_type', '$double_from', '$double_to', '$half_from', '$half_to', '$no_to', '$no_from')";
	mysqli_query($conn, $sql_insert);

		
	}
};
};
	
while (true) {
    try {
        $channel->basic_consume('pokeAPIBE2DB', '', false, true, false, false, $callback);
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
        $channel->queue_declare('pokeAPIBE2DB', false, true, false, false, ['x-ha-policy'=>'all']);
    }
}
// Close the connection
$channel->close();
$connection->close();
