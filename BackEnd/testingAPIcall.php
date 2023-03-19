<?php
require __DIR__ . '/testingAPI.php';
#update this in the future to check for other conditions the user might want to see, like evolution lines, typing, movesets, etc
$poke_int = (int)readline('Enter the pokemon number: ');
$get_data = callAPI('GET', 'https://pokeapi.co/api/v2/pokemon/' . $poke_int, false);
$response = json_decode($get_data, true);

echo ucfirst($response['name'] . "\n");
#$errors = $response['response']['errors'];
#$data = $response['response']['data'][0];
?>
