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
	$exists = $data['exists'];
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
						'exists' => $exists
					]
				);
			}
			
			if ($choice = 'damage type') {
				
				$result = $api->pokemonType($user_input);
				$decoded_result = json_decode($result, true);

				foreach ($decoded_result['damage_relations'] as $key => $value) {

					array_push($output_array, $key . ": ");
				//go through each of the relational damages and echo the name of the types	
			    		foreach ($value as $name) {

						array_push($output_array, $name['name']);
						
			    		}	
				}
				$double_damage_from = [];
				$double_damage_to = [];
				$half_damage_from = [];
				$half_damage_to = [];
				$no_damage_from = [];
				$no_damage_to = [];

				$category = '';
				
				foreach ($output_array as $item) {
					if (strpos($item, ':') !== false) {
						$category = trim(str_replace(':', '', $item));
					} else {
						${$category}[] = trim($item);
					}
				}
				$double_damage_from_output = implode(', ', $double_damage_from) . "\n";
				$double_damage_to_output = implode(', ', $double_damage_to) . "\n";
				$half_damage_from_output = implode(', ', $half_damage_from) . "\n";
				$half_damage_to_output = implode(', ', $half_damage_to) . "\n";
				$no_damage_from_output = implode(', ', $no_damage_from) . "\n";
				$no_damage_to_output = implode(', ', $no_damage_to) . "\n";
				
				$pokemonTypesMessageBody = json_encode
				(
					[
						'damage_type' => $user_input,
						'double_from' => $double_damage_from_output,
						'double_to' => $double_damage_to_output,
						'half_from' => $half_damage_from_output,
						'half_to' => $half_damage_to_output,
						'no_from' => $no_damage_from_output,
						'no_to' => $no_damage_to_output,
						'exists' => $exists
					]
				);
			}	
			
			
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
			else {
			
				if ($choice == 'type') {
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
				} elseif ($choice == 'damage type') {
					$double_damage_from = $data['double_from'];
					$double_damage_to = $data['double_to'];
					$half_damage_from = $data['half_from'];
					$half_damage_to = $data['half_to'];
					$no_damage_from = $data['no_from'];
					$no_damage_to = $data['no_to'];
					
					
					$pokemonMessageBody = json_encode
					(
						[
							'damage_type' => $user_input,
							'double_from' => $double_damage_from,
							'double_to' => $double_damage_to,
							'half_from' => $half_damage_from,
							'half_to' => $half_damage_to,
							'no_from' => $no_damage_from,
							'no_to' => $no_damage_to
						]
					);
				}
			
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
