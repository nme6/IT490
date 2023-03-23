<?php
require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

//$payload = 'Hello World!';
//$username = $_POST['username'];
//$password = $_POST['password'];
//$email = $_POST['email'];

$isLogin = true;

$username = 'OurMom';
$password = 'ILovet4091!';
$confirm = 'ILovet4091!';
$email = 'Yellisisawesome@gmail.edu';
$firstname = 'Boximilian';
$lastname = 'Aacobs';

if($isLogin == false)
{
	// Create a connection to RabbitMQ
	$connection = new AMQPStreamConnection('192.168.191.111', 5672, 'admin', 'admin');
	$channel = $connection->channel();

	// Declare a queue for sending messages
	$channel->queue_declare('FE2BE', false, false, false, false);

	// Publish the message to the queue
	$messageBody = json_encode
	(
		[
    			'username' => $username,
			'password' => $password,
			'confirm' => $confirm,
			'email' => $email,
			'firstname' => $firstname,
			'lastname' => $lastname,

			'isLogin' => $isLogin
		]
	);

	// Define the message to send
	$message = new AMQPMessage($messageBody);

	// Publish the message to the queue
	$channel->basic_publish($message, '', 'FE2BE');

	//Echo Msg to console
	echo "-={[Front-end | Register] Sent message to the Back-end!}=-\n$messageBody\n";

	// Close the channel and the connection
	$channel->close();
	$connection->close();
}


if($isLogin == true)
{
        // Create a connection to RabbitMQ
        $connection = new AMQPStreamConnection('192.168.191.111', 5672, 'admin', 'admin');
        $channel = $connection->channel();

        // Declare a queue for sending messages
        $channel->queue_declare('FE2BE', false, false, false, false);

        // Publish the message to the queue
        $messageBody = json_encode
        (
                [
                        'username' => $username,
                        'password' => $password,
			'email' => $email,

	  		'isLogin' => $isLogin		
                ]
        );

        // Define the message to send
        $message = new AMQPMessage($messageBody);

        // Publish the message to the queue
        $channel->basic_publish($message, '', 'FE2BE');

        //Echo Msg to console
        echo "-={[Front-end | Login] Sent message to the Back-end!}=-\n$messageBody\n";

        // Close the channel and the connection
        $channel->close();
        $connection->close();
}
?>

