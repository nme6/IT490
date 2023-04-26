<?php
require_once __DIR__ . '/vendor/autoload.php';
use PokePHP\PokeApi;
$api = new PokeApi;
$choice = readline('Please enter what you are looking for: ');

if ($choice == 'evolution chain') {
	$user_input = (int)readline('Enter pokemon number: ');

	$result = $api->evolutionChain($user_input);
	$decoded_result = json_decode($result, true);

	$chain = $decoded_result['chain'];

	echo "Evolution Chain:\n";

	while (!empty($chain)) {
		echo $chain['species']['name'] . "\n";
		if (isset($chain['evolves_to'])) {
			if (isset($chain['evolves_to'][0])) {
				$chain = $chain['evolves_to'][0];
			} else {
				$chain = null;
			}
		
		} else {
			$chain = null;
		}
	}	
}
if ($choice == 'type') {
	
	$user_input = readline('Enter a Pokemon type: ');
	$result = $api->pokemonType($user_input);
	$decoded_result = json_decode($result, true);

	echo "Type name: " . $decoded_result['name'] . "\n";
	echo "Damage relations: \n";
	foreach ($decoded_result['damage_relations'] as $key => $value) {
    		echo $key . ": \n";
    	foreach ($value as $name) {
        	echo $name['name'] . "\n";
    		}
	}
}
//print_r($chain);
//echo $decoded_result['name'] . "\n";

?>
