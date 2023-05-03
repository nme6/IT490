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


$channel = $connection->channel();

$channel->queue_declare('pokeAPIBE2DB', false, false, false, false, ['x-ha-policy'=>'all']);

//$choice = null;

$callback = function ($message) use ($channel) {
	$api = new PokeApi;
	$data = json_decode($message->getBody(), true);
	
	$choice = $data['choice'];
	$exists = $data['pokemon_exists'];
	$user_input = $data['name'];
	
	//while ($choice != 'exit') {
		$output = "";
		$output_array = [];

		if ($exists != true) {

			//Check for when user wants to check specific pokemon's typing
			if ($choice == 'pokemon type') {
				//$user_input = readline('Enter a Pokemon name: ');
				$result = $api->pokemon($user_input);
				$decoded_result = json_decode($result, true);
					
				//ouptut the pokemon's name 
				echo "Pokemon name: " . $decoded_result['name'] . "\n";
				echo "Types: \n";

				foreach ($decoded_result['types'] as $type) {
					echo $type['type']['name'] . "\n";
					$output .= $type['type']['name'] . " ";
				}
				
				$pokemonTypesMessageBody = json_encode 
				(
					[
						'pokemon_name' => $user_input,
						'types' => $output,
						'pokemon_exists' => $exists
					]
				);
				
				$pokemonTypesInsertConnection = null;
				$ips = array('192.168.191.111', '192.168.191.67', '192.168.191.215');
				foreach ($ips as $ip) {
		    			try {
						$pokemonTypesInsertConnection = new AMQPStreamConnection($ip, 5672, 'admin', 'admin');
						echo "Connected to RabbitMQ instance at $ip\n";
				    		break;
		    			} catch (Exception $e) {
						continue;
		    			}
		   		}
		   		
		   		if (!$pokemonTypesInsertConnection) {
		   			die("could not connect to any RabbitMQ instance");
		   		};
				
				$typeInsertChannel = $pokemonTypesInsertConnection->channel();
				$typeInsertChannel->queue_declare('pokeAPIBE2DB', false, false, false, false, ['x-ha-policy'=>'all']);
		    		$pokemonTypesMessage = new AMQPMessage($pokemonTypesMessageBody);
		    		$typeInsertChannel->basic_publish($pokemonTypesMessage, '', 'pokeAPIBE2DB');
		    		$typeInsertChannel->close();
		    		$pokemonTypesInsertConnection->close();
		    		
		    		//send to frontend here as well
				
				

				}
			} else {
			
			
			$pokemon_types = $data['types'];
			echo $pokemon_types;
			echo $exists;
			
			$pokemonMessageBody = json_encode
			(
				[
					'pokemon_name' => $user_input,
					'types' => $pokemon_types
				]
			);
			
			//now send to the frontend
			
		}
	};	
//};
while (true) {
    try {
        $channel->basic_consume('pokeDB2BE', '', false, true, false, false, $callback);
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
        $channel->queue_declare('pokeDB2BE', false, false, false, false, ['x-ha-policy'=>'all']);
    }
}
$channel->close();
$connection->close();

?>
