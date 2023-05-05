 <?php
require_once __DIR__ . '/vendor/autoload.php';
use PokePHP\PokeApi;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

// Create a connection to RabbitMQ

//establish a connection with one of the nodes within the rabbitmq cluster
//loop through the IPs until one is able to connect
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


/*
The main function of this file is to process the output given by the database when prompted to check if an entry already exists (locally cached). 
If the information does not exist already, the API must be called and the results will be sent to the Database for storage and to the Frontend for immediate use for users. 
If the information does already exist, then the API does not need to be called and the information is pulled and processed from the Database. After the data is packaged it is sent to the Frontend. 
There are several tracking variables that travel through this file for ease of access in other files.
*/

$channel = $connection->channel();

$channel->queue_declare('pokeAPIBE2DB', false, true, false, false, ['x-ha-policy'=>'all']);
$channel->queue_declare('pokeDB2BE', false, true, false, false, ['x-ha-policy'=>'all']);


$callback = function ($message) use ($channel) {
	$api = new PokeApi;
	$data = json_decode($message->getBody(), true);
	
	//Establish variables used for conditional statements provided by both Database and Frontend
	$choice = $data['choice'];
	$exists = $data['exists'];
	//$user_input = $data['name'];
	
	
		$output = "";
		$output_array = [];
		
		//Condition checks if the API response information is not already stored in the Database. If it is confrimed to not appear in the Database then the API call is made. 
		if ($exists != true) {

			//Check for when user wants to check specific pokemon's typing
			if ($choice == 'pokemon type') {
				$user_input = $data['name'];
				//Call the API to gain the specified information
				$result = $api->pokemon($user_input);
				$decoded_result = json_decode($result, true);
					
				//Output the name of the Pokemon for testing purposes
				/*
				echo "Pokemon name: " . $decoded_result['name'] . "\n";
				echo "Types: \n";
				*/
				
				//Loop through each type result and append the output array that is later pushed to the Frontend/Database
				foreach ($decoded_result['types'] as $type) {
					//echo $type['type']['name'] . "\n";      //Output for testing purposes
					$output .= $type['type']['name'] . "<br>";
				}
				
				//Encode the appropriate information and prepare it for sending
				$pokemonTypesMessageBody = json_encode 
				(
					[
						'choice' => $choice,
						'pokemon_name' => $user_input,
						'types' => $output,
						'exists' => $exists
					]
				);
			}
			
			if ($choice == 'damage type') {
				$user_input = ['name'];
				$result = $api->pokemonType($user_input);
				$decoded_result = json_decode($result, true);
				
				//Loop through the API response for each key and its associated value
				foreach ($decoded_result['damage_relations'] as $key => $value) {
						
					//Append the output array with the current key
					array_push($output_array, $key . ": ");
					
					//Append the output array with the current key's value
			    		foreach ($value as $name) {

						array_push($output_array, $name['name']);
						
			    		}	
				}
				
				//Instantiate empty lists for the API responses to be placed in
				$double_damage_from = [];
				$double_damage_to = [];
				$half_damage_from = [];
				$half_damage_to = [];
				$no_damage_from = [];
				$no_damage_to = [];

				$category = '';
				
				//Sort the long output based on spacing and trim wherever the delimiter is
				foreach ($output_array as $item) {
					if (strpos($item, ':') !== false) {
						$category = trim(str_replace(':', '', $item));
					} else {
						${$category}[] = trim($item);
					}
				}
				
				//Populate the instantiated lists with the sorted information
				$double_damage_from_output = implode(', ', $double_damage_from) . "\n";
				$double_damage_to_output = implode(', ', $double_damage_to) . "\n";
				$half_damage_from_output = implode(', ', $half_damage_from) . "\n";
				$half_damage_to_output = implode(', ', $half_damage_to) . "\n";
				$no_damage_from_output = implode(', ', $no_damage_from) . "\n";
				$no_damage_to_output = implode(', ', $no_damage_to) . "\n";
				
				//Encode and prep the data to be sent to the Database/Frontend
				$pokemonTypesMessageBody = json_encode
				(
					[
						'choice' => $choice,
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
			
				//Ensure that a rabbitmq connection is established
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
				
				//send the API call response to the Database for storage (only executes after confirming it does not already appear in the Database)
				$typeInsertChannel = $pokemonTypesInsertConnection->channel();
				$typeInsertChannel->queue_declare('pokeAPIBE2DB', false, true, false, false, ['x-ha-policy'=>'all']);
		    		$pokemonTypesMessage = new AMQPMessage($pokemonTypesMessageBody);
		    		$typeInsertChannel->basic_publish($pokemonTypesMessage, '', 'pokeAPIBE2DB');

		    		
		    		//send the API call response to the Frontend for display and processing for the user
		    		$typeInsertChannel->queue_declare('pokeBE2FE', false, true, false, false, ['x-ha-policy'=>'all']);
		    		$pokemonTypesMessage = new AMQPMessage($pokemonTypesMessageBody);
		    		$typeInsertChannel->basic_publish($pokemonTypesMessage, '', 'pokeBE2FE');
		    		$typeInsertChannel->close();
		    		$pokemonTypesInsertConnection->close();
		    		
		    		
		    		//Condition now checks if the API response was already stored in the Database	    				
				} elseif ($exists) {
			
					if ($choice == 'pokemon type') {
						$user_input = ['name'];
						//Outputs for testing purposes in Backend
						/*
						echo $data['types'];
						echo $exists;
						*/
						
						//Encode and prep the information to be sent to the Frontend
						$pokemonTypesMessageBody = json_encode
						(
							[
								'choice' => $data['choice'],
								'pokemon_name' => $data['name'],
								'types' => $data['types']
							]
						);
					} elseif ($choice == 'damage type') {
						$user_input = ['name'];
						//used to display test results in backend terminal during testing Database calls
						/*
						$damage_type =$data['name'];
						$double_damage_from = $data['double_from'];
						$double_damage_to = $data['double_to'];
						$half_damage_from = $data['half_from'];
						$half_damage_to = $data['half_to'];
						$no_damage_from = $data['no_from'];
						$no_damage_to = $data['no_to'];
						
						echo "Damage Type: " . $damage_type . "\n";
						echo "Double Damage From: " . $double_damage_from . "\n";
						echo "Double Damage To: " . $double_damage_to . "\n";
						echo "Half Damage From: " . $half_damage_from . "\n";
						echo "Half Damage To: " . $half_damage_to . "\n";
						echo "No Damage From: " . $no_damage_from . "\n";
						echo "No Damage To: " . $no_damage_to . "\n";
						*/
						
						//package up information to send to Frontend machine						
						$pokemonTypesMessageBody = json_encode
						(
							[
								'choice' => $data['choice'],
								'damage_type' => $data['name'],
								'double_from' => $data['double_from'],
								'double_to' => $data['double_to'],
								'half_from' => $data['half_from'],
								'half_to' => $data['half_to'],
								'no_from' => $data['no_from'],
								'no_to' => $data['no_to']
							]
						);
						
						
					}
					
					elseif ($choice == 'team view') {
						$pokemonTypesMessageBody = json_encode 
			   			(
			   				[
			   					'id'=> $data['id'],
			   					'choice' => $data['choice'],
			   					'member_1' => $data['member_1'],
			   					'member_2' => $data['member_2'],
			   					'member_3' => $data['member_3'],
			   					'member_4' => $data['member_4'],
			   					'member_5' => $data['member_5'],
			   					'member_6' => $data['member_6']

			   				
			   				]
			   			);	
	   				} elseif ($choice == 'team build') {
	   					
	   					$pokemonTypesMessageBody = json_encode
	   					(
	   						[
	   							'user_id' => $data['id'],
	   							'choice' => $data['choice'],
			   					'member_1' => $data['member_1'],
			   					'member_2' => $data['member_2'],
			   					'member_3' => $data['member_3'],
			   					'member_4' => $data['member_4'],
			   					'member_5' => $data['member_5'],
			   					'member_6' => $data['member_6']
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
				   		
				   		//Establish the connection channel and send the encoded data to the Frontend
				   		$typeInsertChannel = $pokemonTypesInsertConnection->channel();
				   		$typeInsertChannel->queue_declare('pokeBE2FE', false, true, false, false, ['x-ha-policy'=>'all']);
				    		$pokemonTypesMessage = new AMQPMessage($pokemonTypesMessageBody);
				    		$typeInsertChannel->basic_publish($pokemonTypesMessage, '', 'pokeBE2FE');
				    		$typeInsertChannel->close();
				    		$pokemonTypesInsertConnection->close();
				    		
		   			} 

						
					
					
				//Ensure that a rabbitmq connection is established
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
		   		
		   		//Establish the connection channel and send the encoded data to the Frontend
		   		$typeInsertChannel = $pokemonTypesInsertConnection->channel();
		   		$typeInsertChannel->queue_declare('pokeBE2FE', false, true, false, false, ['x-ha-policy'=>'all']);
		    		$pokemonTypesMessage = new AMQPMessage($pokemonTypesMessageBody);
		    		$typeInsertChannel->basic_publish($pokemonTypesMessage, '', 'pokeBE2FE');
		    		$typeInsertChannel->close();
		    		$pokemonTypesInsertConnection->close();
		    		
				
				
						
	   		

		}
	};	

//Attempt to recconnect to rabbitmq nodes and re-establish queues if connection is lost
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
        $channel->queue_declare('pokeDB2BE', false, true, false, false, ['x-ha-policy'=>'all']);
    }
}
$channel->close();
$connection->close();

?>
