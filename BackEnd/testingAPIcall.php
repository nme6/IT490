<?php

/*
 *Possible implementation for a check to see if the number associated is already stored in the database: that way we can refrain from constant calls
 *
 */
require __DIR__ . '/testingAPI.php';

$poke_int = 0;

while ($poke_int != -1) {
	#update this in the future to check for other conditions the user might want to see, like evolution lines, typing, movesets, etc
	$poke_int = (int)readline('Enter the pokemon number (-1 to exit): ');

	if ($poke_int != -1) {
		$get_data = callAPI('GET', 'https://pokeapi.co/api/v2/pokemon/' . $poke_int, false);
		$response = json_decode($get_data, true);

		$file = fopen('APIResponses.txt', 'a');

		fwrite($file, ucfirst($poke_int . ": "));

		fwrite($file, ucfirst($response['name'] . "\n"));

		fclose($file);
	

		echo ucfirst($response['name'] . "\n");
	}

}
#$errors = $response['response']['errors'];
#$data = $response['response']['data'][0];
?>
