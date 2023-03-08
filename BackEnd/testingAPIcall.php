<?php
require __DIR__ . '/testingAPI.php';
$poke_int = (int)readline('Enter the pokemon number: ');
$get_data = callAPI('GET', 'https://pokeapi.co/api/v2/pokemon/' . $poke_int, false);
$response = json_decode($get_data, true);
echo $response['name'] . "\n";
#$errors = $response['response']['errors'];
#$data = $response['response']['data'][0];
?>
