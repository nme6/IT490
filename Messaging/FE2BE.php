<?php
require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

//$username = $_POST['username'];
//$password = $_POST['password'];

$username = 'testuser';
$password = 'testpassword';

// Create a connection to RabbitMQ
$connection = new AMQPStreamConnection('192.168.191.111', 5672, 'admin', 'admin');
$channel = $connection->channel();

// Define the exchange and queue names
$exchangeName = 'user_info';
$queueName = 'FE2BE';

// Declare the exchange and queue
$channel->exchange_declare($exchangeName, 'fanout', false, false, false);
$channel->queue_declare($queueName, false, true, false, false);
$channel->queue_bind($queueName, $exchangeName);

// Publish the message to the exchange
$messageBody = json_encode([
    'username' => $username,
    'password' => $password
]);
$message = new AMQPMessage($messageBody);
$channel->basic_publish($message, $exchangeName);

echo "Message sent: $messageBody\n";

// Close the connection
$channel->close();
$connection->close();
?>
