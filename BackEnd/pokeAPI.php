<?php
require_once __DIR__ . '/vendor/autoload.php';
use PokePHP\PokeApi;
$api = new PokeApi;
$choice = null;
while ($choice != 'exit') {
	$choice = readline('Please enter what you are looking for: ');
	//Check for when the user wants to check the evolution chains
	//NOTE: As of 4/25/23 the number associated is the evolution chain grouping number
	
	//This block checks the user input to give the user either the pokedex ID number for the pokemon they enter or the associated name with the ID number they enter
	
	if ($choice == 'pokedex entry') {
		$user_input = readline('Enter the pokemon number or name: ');

		if (is_numeric($user_input)) {
			$result = $api->pokemon($user_input);
			$decoded_result = json_decode($result, true);
			echo "Name: " . $decoded_result['name'] . "\n";
			}
		else {
			$result = $api->pokemon($user_input);
			$decoded_result = json_decode($result, true);
			echo "Number: " . $decoded_result['id'] . "\n";
		}
					
		
	}


	if ($choice == 'evolution chain') {
		$user_input = (int)readline('Enter pokemon number: ');

		$result = $api->evolutionChain($user_input);
		$decoded_result = json_decode($result, true);
		
		//store the decoded api response array
		$chain = $decoded_result['chain'];

		echo "Evolution Chain:\n";
		
		//while there are still items in the chain array continue the loop
		while (!empty($chain)) {
			echo $chain['species']['name'] . "\n";

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
	}

	//Checks for when the user wants to know information about a specific type
	if ($choice == 'type') {
		
		$user_input = readline('Enter a Pokemon type: ');
		$result = $api->pokemonType($user_input);
		$decoded_result = json_decode($result, true);

		echo "Type name: " . $decoded_result['name'] . "\n";
		echo "Damage relations: \n";

		//go through each key value pair for damage relations and echo the relation
		foreach ($decoded_result['damage_relations'] as $key => $value) {
			echo $key . ": \n";
		//go through each of the relational damages and echo the name of the types	
	    	foreach ($value as $name) {
			echo $name['name'] . "\n";
	    		}
		}
	}

	//Check for when user wants to check specific pokemon's typing
	if ($choice == 'pokemon type') {
		$user_input = readline('Enter a Pokemon name: ');
		$result = $api->pokemon($user_input);
		$decoded_result = json_decode($result, true);
		
		//ouptut the pokemon's name 
		echo "Pokemon name: " . $decoded_result['name'] . "\n";
		echo "Types: \n";

		//loop through each type in the array and print to the screen
		foreach ($decoded_result['types'] as $type) {
			echo $type['type']['name'] . "\n";
		}
	}
}

?>
