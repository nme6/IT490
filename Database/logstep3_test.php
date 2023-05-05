<?php

require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;


// Create a connection to RabbitMQ
//$connection = new AMQPStreamConnection('192.168.191.111', 5672, 'admin', 'admin');
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

$channel = $connection->channel();

// Declare the queue
$channel->queue_declare('logBE2DB', false, false, false, false, ['x-ha-policy'=>'all']);

echo "-={[Database] Waiting for Back-end messages. To exit press CTRL+C}=-\n";

// Define the callback function to process messages from the queue
//$callback = function ($message) use ($servername, $username, $password, $dbname) {
$callback = function ($message) use ($channel){
    // Assign $data from JSON   
    $data = json_decode($message->getBody(), true);

    echo "Received message from Back-end: " . $message->body . "\n";
    // Get the data from the message body
    $username = $data['username'];
    $password = $data['password'];

	// Connect to the database
	$servername = "localhost";
	$username_db = "test";
	$password_db = "test";
	$dbname = "test";


	$conn = mysqli_connect($servername, $username_db, $password_db, $dbname);

	// Check if the connection is successful
	if (!$conn) {
    	die("Connection failed: " . mysqli_connect_error());
	}

	// Check if the user exists in the database
    	$sql_check = "SELECT * FROM users WHERE BINARY username = '$username'";
    	$result = mysqli_query($conn, $sql_check);

	if (mysqli_num_rows($result) > 0) {
    		// User exists, retrieve the password
    		$row = mysqli_fetch_assoc($result);
    		$hash = $row['password'];
    		$userFound = true;
    		$id = $row['id'];
	} else {
    		// User does not exist
    		$userFound = false;
    		$hash = null;
	}

	// Close the database connection
	mysqli_close($conn);

	// Send a message to the backend with the authentication information
	$dbmessageBody = json_encode([
    		'userFound' => $userFound,
		'hash' => $hash,
		'password' => $password,
		'username' => $username,
		'id' => $id
	]);

	//echo "Received message from Back-end: " . $dbmessageBody->body . "\n";
        // Send a new message to the database
        //$dbconnection = new AMQPStreamConnection('192.168.191.111', 5672, 'admin', 'admin');
        
	$dbconnection = null;
	$ips = array('192.168.191.111', '192.168.191.67', '192.168.191.215');

	foreach ($ips as $ip) {
	    try {
		$dbconnection = new AMQPStreamConnection($ip, 5672, 'admin', 'admin');
		echo "Connected to RabbitMQ instance at $ip\n";
		break;
	    } catch (Exception $e) {
		continue;
	    }
	}

	if (!$dbconnection) {
	    die("Could not connect to any RabbitMQ instance.");
	} 
        
        $dbchannel = $dbconnection->channel();
        $dbchannel->queue_declare('logDB2BE', false, false, false, false, ['x-ha-policy'=>'all']);
        $dbmessage = new AMQPMessage($dbmessageBody);
        $dbchannel->basic_publish($dbmessage, '', 'logDB2BE');
        $dbchannel->close();
        $dbconnection->close();

	};


// Consume messages from the queue with the defined callback function
//$channel->basic_consume('logBE2DB', '', false, true, false, false, $callback);

// Keep the script running to receive messages
//while (count($channel->callbacks)) {
//    $channel->wait();
//}

while (true) {
  try {
      $channel->basic_consume('logBE2DB', '', false, true, false, false, $callback);
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
      $channel->queue_declare('logBE2DB', false, false, false, false, ['x-ha-policy'=>'all']);
  }
}


// Close the connection
$channel->close();
$connection->close();
