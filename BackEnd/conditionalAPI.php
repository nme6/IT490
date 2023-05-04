<?php
require_once __DIR__ . '/vendor/autoload.php';
use PokePHP\PokeApi;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

// Create a connection to RabbitMQ
//$connection = new AMQPStreamConnection('192.168.191.111', 5672, 'admin', 'admin');

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





$choice = null;



while ($choice != 'exit') {
		//change this to a json decode receiving info from the frontend
		$choice = readline('Please enter what you are looking for: ');
		
		if ($choice == 'pokemon type') {
			//change to a json decode for user input from the frontend
			$user_input = readline('Enter a Pokemon name: ');
			

			
			$pokemonTypesMessageBody = json_encode 
			(
				[
					'choice' => $choice,
					'pokemon_name' => $user_input
				]
			);
			
		}
	   		
	   	if ($choice == 'damage type') {
	   		$user_input = readline('Enter a damage type: ');
	   		
	   		$pokemonTypesMessageBody = json_encode
	   		(
	   			[
	   				'choice' => $choice,
	   				'damage_type' => $user_input
	   			]
	   		);
	   		
	   	}
	   	
			$pokemonTypesConnection = null;
			$ips = array('192.168.191.111', '192.168.191.67', '192.168.191.215');
			foreach ($ips as $ip) {
	    			try {
					$pokemonTypesConnection = new AMQPStreamConnection($ip, 5672, 'admin', 'admin');
					echo "Connected to RabbitMQ instance at $ip\n";
			    		break;
	    			} catch (Exception $e) {
					continue;
	    			}
	   		}
	   		
	   		if (!$pokemonTypesConnection) {
	   			die("could not connect to any RabbitMQ instance");
	   		}
			$typeChannel = $pokemonTypesConnection->channel();
			$typeChannel->queue_declare('pokeBE2DB', false, true, false, false, ['x-ha-policy'=>'all']);
	    		$pokemonTypesMessage = new AMQPMessage($pokemonTypesMessageBody);
	    		$typeChannel->basic_publish($pokemonTypesMessage, '', 'pokeBE2DB');
	    		$typeChannel->close();
	    		$pokemonTypesConnection->close();
			
			
			
			

		}

$channel->close();
$connection->close();

?>
