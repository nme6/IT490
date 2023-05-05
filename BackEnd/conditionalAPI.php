<?php
require_once __DIR__ . '/vendor/autoload.php';
use PokePHP\PokeApi;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

//Ensure that a rabbitmq connection is established
$connection = null;
$ips = array('192.168.191.111', '192.168.191.67', '192.168.191.215');


foreach ($ips as $ip) {
    try {
        $connection = new AMQPStreamConnection($ip, 5672, 'admin', 'admin');
	echo "[conditionalAPI] Connected to RabbitMQ instance at $ip\n";
	
        break;
    } catch (Exception $e) {
        continue;
    }
   
}

if (!$connection) {
    die("Could not connect to any RabbitMQ instance.");
}


/*
The main function of this file is to receive the requestt from the Frontend and prepare the variables to be processed in the Database. 
Based on the request made (pokemon type, damage type, team build) the information will be packaged differently and sent to the Database for reference checks.
*/


$choice = null;
$channel = $connection->channel();
$channel->queue_declare('pokeFE2BE', false, true, false, false, ['x-ha-policy'=>'all']);

$callback = function ($message) use ($channel) {
		
		
		$data = json_decode($message->getBody(), true);
		$choice = $data['choice'];
		
		//Each If statement checks for the different arguments made by the user
		//Checks if the user is looking for Pokemon Type information
		if ($choice == 'pokemon type') {		

			$pokemonTypesMessageBody = json_encode 
			(
				[
					'choice' => $data['choice'],
					'pokemon_name' => $data['user_input']
				]
			);
			
		}
	   	
	   	//Checks if the user is looking for the Type Damage information
	   	if ($choice == 'damage type') {		
	   		
	   		$pokemonTypesMessageBody = json_encode
	   		(
	   			[
	   				'choice' => $data['choice'],
	   				'damage_type' => $data['user_input']
	   			]
	   		);
	   		
	   	}
	   	
	   	//Checks if the user is looking to build a team that will be saved in the Database for export later
	   	if ($choice == 'team build') {
	   		//take 8 parameters: User ID, Choice, Member1, Member2, Member3, Member4, Member5, Member6	   		
	   		//add json encoding info here for team builder
	   		$pokemonTypesMessageBody = json_encode 
	   		(
	   			[
	   				'id'=> $data['user_id'],
	   				'choice' => $data['choice'],
	   				'member_1' => $data['member_1'],
	   				'member_2' => $data['member_2'],
	   				'member_3' => $data['member_3'],
	   				'member_4' => $data['member_4'],
	   				'member_5' => $data['member_5'],
	   				'member_6' => $data['member_6']

	   				
	   			]
	   		);
	   		}
	   	if ($choice == 'team view') {
	   		$pokemonTypesMessageBody = json_encode
	   		(
	   			[
	   				'id' => $data['user_id'],
	   				'choice' => $data['choice']
	   			]
	   		);
	   		}
	   		
	   		//Ensure that a rabbitmq connection is established
			$pokemonTypesConnection = null;
			$ips = array('192.168.191.111', '192.168.191.67', '192.168.191.215');
			foreach ($ips as $ip) {
	    			try {
					$pokemonTypesConnection = new AMQPStreamConnection($ip, 5672, 'admin', 'admin');
					echo "[conditionalAPI] Connected to RabbitMQ instance at $ip\n";
			    		break;
	    			} catch (Exception $e) {
					continue;
	    			}
	   		}
	   		
	   		if (!$pokemonTypesConnection) {
	   			die("could not connect to any RabbitMQ instance");
	   		}
	   		
	   		//Establish the connection channel and send the encoded data to the Database
			$typeChannel = $pokemonTypesConnection->channel();
			$typeChannel->queue_declare('pokeBE2DB', false, true, false, false, ['x-ha-policy'=>'all']);
	    		$pokemonTypesMessage = new AMQPMessage($pokemonTypesMessageBody);
	    		$typeChannel->basic_publish($pokemonTypesMessage, '', 'pokeBE2DB');
	    		$typeChannel->close();
	    		$pokemonTypesConnection->close();

		};
		
//Attempt to recconnect to rabbitmq nodes and re-establish queues if connection is lost
while (true) {
    try {
        $channel->basic_consume('pokeFE2BE', '', false, true, false, false, $callback);
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
                echo "[conditionalAPI] Connected to RabbitMQ instance at $ip\n";
                break;
            } catch (Exception $e) {
                continue;
            }
        }
        if (!$connection) {
            die("Could not connect to any RabbitMQ instance.");
        }
        $channel = $connection->channel();
        $channel->queue_declare('pokeFE2BE', false, true, false, false, ['x-ha-policy'=>'all']);
    }
}

$channel->close();
$connection->close();

?>
