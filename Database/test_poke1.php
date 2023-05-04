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
$channel->queue_declare('pokeBE2DB', false, true, false, false);

$callback = function ($message) use ($channel){
    // Assign $data from JSON   
    $data = json_decode($message->getBody(), true);
     echo "Received message from Back-end: " . $message->body . "\n";
    // Get the data from the message body


    //$types =$data['types'];
    $choice = $data['choice'];
	if ($choice == 'pokemon type') {
		$pokemon_name =$data['pokemon_name'];
		// Connect to the database
		$servername = "localhost";
		$username_db = "test";
		$password_db = "test";
		$dbname = "test";
		
		$conn = mysqli_connect($servername, $username_db, $password_db, $dbname);
		
		$sql = "SELECT * FROM `pokemon types` WHERE `pokemon_name` = '$pokemon_name'";
		$result = mysqli_query($conn, $sql);
		$row = mysqli_fetch_assoc($result);
		$pokemon_exists = ($row !== null);
		//send doesnt exist
		

	 	if ($pokemon_exists != true) {
			// Send back the pokemon exists status and choice
		    	$pokemonMessage = json_encode
		    	(
		    		[
		    			'exists' => $pokemon_exists,
		    			'choice' => $choice,
		    			'name' => $pokemon_name
		    		]
		    	);
	    

		// Insert the pokemon into the database
		//$sql_insert = "INSERT INTO `pokemon types` (`pokemon_name`, `types`) VALUES ('$pokemon_name', '$types')";
		//mysqli_query($conn, $sql_insert);
	    
	} elseif ($pokemon_exists) {
            $sql = "SELECT types FROM `pokemon types` WHERE `pokemon_name` = '$pokemon_name'";
	    $result = mysqli_query($conn, $sql);
	    $row = mysqli_fetch_assoc($result);
	    $types = $row['types'];
        	$pokemonMessage = json_encode(
    		[
    			'exists' => $pokemon_exists,
    			'choice' => $choice,
    			'name' => $pokemon_name,
    			'types' => $types
    			
    		]
    	
    	);  
	};
	}
	if ($choice == 'damage type') {
		$damage_type = $data['damage_type'];
		$sql = "SELECT * FROM `damage` WHERE `damage_type` = '$damage_type'";
		$result = mysqli_query($conn, $sql);
		$row = mysqli_fetch_assoc($result);
		$damage_exists = ($row !== null);
		if (!$damage_exists) {
			// Send back the pokemon exists status and choice
		    	$pokemonMessage = json_encode
		    	(
		    		[
		    			'exists' => $damage_exists,
		    			'choice' => $choice,
		    			'damage_type' => $damage_type
		    		]
		    	);
		
}
}
    	$pokeconnection = null;
	$ips = array('192.168.191.111', '192.168.191.67', '192.168.191.215');
	foreach ($ips as $ip) {
	    try {
		$pokeconnection = new AMQPStreamConnection($ip, 5672, 'admin', 'admin');
		echo "Connected to RabbitMQ instance at $ip\n";
		break;
	    } catch (Exception $e) {
		continue;
	    }
	   
	}

	if (!$pokeconnection) {
	    die("Could not connect to any RabbitMQ instance.");
	}
	
	$pokechannel = $pokeconnection->channel();
        $pokechannel->queue_declare('pokeDB2BE', false, false, false, false, ['x-ha-policy'=>'all']);
        $dbPmessage = new AMQPMessage($pokemonMessage);
        $pokechannel->basic_publish($dbPmessage, '', 'pokeDB2BE');
        $pokechannel->close();
        $pokeconnection->close();



};
while (true) {
    try {
        $channel->basic_consume('pokeBE2DB', '', false, true, false, false, $callback);
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
        $channel->queue_declare('pokeBE2DB', false, false, false, false, ['x-ha-policy'=>'all']);
    }
}
// Close the connection
$channel->close();
$connection->close();
