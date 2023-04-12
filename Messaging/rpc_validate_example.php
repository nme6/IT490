<?php

// Load the required dependencies
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

// Create a connection to the RabbitMQ server
$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');

// Create a channel through which to communicate with RabbitMQ
$channel = $connection->channel();

// Declare the queue we will be using for the RPC requests
$channel->queue_declare('rpc_queue', false, false, false, false);

// Define the Fibonacci function that will be used to process the requests
function fib($n)
{
    if ($n == 0) {
        return 0;
    }
    if ($n == 1) {
        return 1;
    }
    return fib($n-1) + fib($n-2);
}

// Print a message to indicate that the server is ready to receive requests
echo " [x] Awaiting RPC requests\n";

// Define the callback function that will be called when a request is received
$callback = function ($req) {

    // Extract the integer value from the request message
    $n = intval($req->body);

    // Print a message indicating which Fibonacci number is being calculated
    echo ' [.] fib(', $n, ")\n";

    // Calculate the Fibonacci number
    $result = fib($n);

    // Create a new message containing the result
    $msg = new AMQPMessage(
        (string) $result,
        array('correlation_id' => $req->get('correlation_id'))
    );

    // Send the result message back to the client
    $req->delivery_info['channel']->basic_publish(
        $msg,
        '',
        $req->get('reply_to')
    );

    // Acknowledge that the request message has been received and processed
    $req->ack();
};

// Configure the channel to only handle one request at a time
$channel->basic_qos(null, 1, null);

// Set up the channel to consume messages from the RPC queue and call the callback function when a message is received
$channel->basic_consume('rpc_queue', '', false, false, false, false, $callback);

// Wait for incoming messages and process them as they arrive
while ($channel->is_open()) {
    $channel->wait();
}

// Close the channel and the connection
$channel->close();
$connection->close();
?>

