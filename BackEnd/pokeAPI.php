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


//$channel = $connection->channel();


$api = new PokeApi;
$choice = null;

//$callback = function ($message) use ($channel) {
	while ($choice != 'exit') {
		$output = "";
		$output_array = [];
		$choice = readline('Please enter what you are looking for: ');
		//Check for when the user wants to check the evolution chains
		//NOTE: As of 4/25/23 the number associated is the evolution chain grouping number
		
		//This block checks the user input to give the user either the pokedex ID number for the pokemon they enter or the associated name with the ID number they enter
		
		if ($choice == 'pokedex entry') {
			$user_input = readline('Enter the pokemon number or name: ');
			$pokedex_entry_file = fopen('Pokedex_Entry.txt', 'a');
			if (is_numeric($user_input)) {
				$result = $api->pokemon($user_input);
				$decoded_result = json_decode($result, true);
				//echo "Name: " . $decoded_result['name'] . "\n";
				$output = $user_input . " is " . $decoded_result['name'] . " in the pokedex \n";
				echo $output;
				
				
				}
			else {
				$result = $api->pokemon($user_input);
				$decoded_result = json_decode($result, true);
				//echo "Number: " . $decoded_result['id'] . "\n";
				$output = $user_input . " is number " . $decoded_result['id'] . " in the pokedex \n";
				echo $output;
			}
			fwrite($pokedex_entry_file, "-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- \n");
			fwrite($pokedex_entry_file, $output);
			fclose($pokedex_entry_file);
						
			
		}

		#IMPORTANT: need to find a way to store these all in one variable
		if ($choice == 'evolution chain') {
			$user_input = (int)readline('Enter pokemon number: ');

			$result = $api->evolutionChain($user_input);
			$decoded_result = json_decode($result, true);
			
			//store the decoded api response array
			$chain = $decoded_result['chain'];

			echo "Evolution Chain:\n";
			$output = "Evolution Chain:\n";

			$evolution_file = fopen('Evolution_Chains.txt', 'a');

			//while there are still items in the chain array continue the loop
			while (!empty($chain)) {
				echo $chain['species']['name'] . "\n";
				$output .= $chain['species']['name'] . "\n";
				//check if there is a variable in the chain with value 'evolves to'
				if (isset($chain['evolves_to'])) {
					//Check if there is a value in the array to print (next pokemon in the evolition chain)
					if (isset($chain['evolves_to'][0])) {
						$chain = $chain['evolves_to'][0];
					} else {
						//stops the loop and indicates the end of the evoliton chain
						$chain = null;
					}
				
				} else {
					$chain = null;
				}
			}
			
			fwrite($evolution_file, "-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- \n");
			fwrite($evolution_file, $output);
			fclose($evolution_file);	
		}

		//Checks for when the user wants to know information about a specific type
		if ($choice == 'type') {
			
			$user_input = readline('Enter a Pokemon type: ');
			//this is where we need to check the database
			
			$result = $api->pokemonType($user_input);
			$decoded_result = json_decode($result, true);

			echo "Type name: " . $decoded_result['name'] . "\n";
			//echo "Damage relations: \n";
			$output = "Type name: " . $decoded_result['name'] . "\n";
			$output .= "Damage relations: \n";
			$damage_file = fopen('Damage_Relations.txt', 'a');
			//go through each key value pair for damage relations and echo the relation
			foreach ($decoded_result['damage_relations'] as $key => $value) {
				//echo $key . ": \n";
				$output .= $key . ": \n";
				//might need to save key to another variable
				array_push($output_array, $key . ": ");
			//go through each of the relational damages and echo the name of the types	
		    		foreach ($value as $name) {
					//echo $name['name'] . "\n";
					$output .= $name['name'] . "\n";
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
			$double_damage_from_output = "double_damage_from: " . implode(', ', $double_damage_from) . "\n";
			$double_damage_to_output = "double_damage_to: " . implode(', ', $double_damage_to) . "\n";
			$half_damage_from_output = "half_damage_from: " . implode(', ', $half_damage_from) . "\n";
			$half_damage_to_output = "half_damage_to: " . implode(', ', $half_damage_to) . "\n";
			$no_damage_from_output = "no_damage_from: " . implode(', ', $no_damage_from) . "\n";
			$no_damage_to_output = "no_damage_to: " . implode(', ', $no_damage_to) . "\n";
			fwrite($damage_file, $double_damage_from_output);
			fwrite($damage_file, $double_damage_to_output);
			fwrite($damage_file, $half_damage_from_output);
			fwrite($damage_file, $half_damage_to_output);
			fwrite($damage_file, $no_damage_from_output);
			fwrite($damage_file, $no_damage_to_output);
			//echo "double_damage_from: " . implode(', ', $double_damage_from) . "\n";
			//echo "double_damage_to: " . implode(', ', $double_damage_to) . "\n";
			//echo "half_damage_from: " . implode(', ', $half_damage_from) . "\n";
			//echo "half_damage_to: " . implode(', ', $half_damage_to) . "\n";
			//echo "no_damage_from: " . implode(', ', $no_damage_from) . "\n";
			//echo "no_damage_to: " . implode(', ', $no_damage_to) . "\n";
			
			//print_r($output_array); 
			
			//print_r($output);
			//print(gettype($output));

			//fwrite($damage_file, $output);
			fwrite($damage_file, "-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- \n");
			fclose($damage_file);
		}

		//Check for when user wants to check specific pokemon's typing
		if ($choice == 'pokemon type') {
			$user_input = readline('Enter a Pokemon name: ');
			$result = $api->pokemon($user_input);
			$decoded_result = json_decode($result, true);
				
			//ouptut the pokemon's name 
			echo "Pokemon name: " . $decoded_result['name'] . "\n";
			echo "Types: \n";
			//$output = "Pokemon name: " . $decoded_result['name'] . "\n";
			//$output .= "Types: \n";
			$types_file = fopen('Pokemon_Types.txt', 'a');
			//loop through each type in the array and print to the screen
			foreach ($decoded_result['types'] as $type) {
				echo $type['type']['name'] . "\n";
				$output .= $type['type']['name'] . " ";
			}
			
			$pokemonTypesMessageBody = json_encode 
			(
				[
					'pokemon_name' => $user_input,
					'types' => $output
				]
			);
			
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
			$typeChannel->queue_declare('pokeBE2DB', false, false, false, false, ['x-ha-policy'=>'all']);
	    		$pokemonTypesMessage = new AMQPMessage($pokemonTypesMessageBody);
	    		$typeChannel->basic_publish($pokemonTypesMessage, '', 'pokeBE2DB');
	    		$typeChannel->close();
	    		$pokemonTypesConnection->close();
			
			
			
			
			//fwrite($types_file, "-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- \n");
			fwrite($types_file, $output);
			fclose($types_file);
		}
		//$file = fopen('APIResponses.txt', 'a');
		//fwrite($file, $output);
		//fclose($file);
		
	}
//}
$channel->close();
$connection->close();

?>
