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
//$channel->queue_declare('logDB2BE', false, false, false, false);
$channel->queue_declare('logDB2BE', false, false, false, false, ['x-ha-policy'=>'all']);

echo "-={[BackEnd Log4] Waiting for Database confirmation. To exit press CTRL+C}=-\n";

// Define the callback function to process messages from the queue
$callback = function ($message) use ($channel) {

    //data= $message->body;
	
    		
    $data  = json_decode($message->getBody(), true);

    $password = $data['password'];
    $username = $data['username'];
    $id = $data['id'];
    $hash = $data['hash'];
    $userFound = $data['userFound'];


    if(password_verify($password, $hash) && $userFound == true) {
	    echo "Username and Password are valid" . "\n";
	    $userAuth = true;
	    $isValid = true;
    } else {
	    echo "Username or Password is invalid" . "\n";
	    $userAuth = false;
	    $isValid = true;
    }

    $authBody = json_encode (
	    [
		    'userAuth' => $userAuth,
		    'username' => $username,
		    'isValid' => $isValid,
		    'id' => $id
	    ]
    );



   // $userExists = $data['userExists'];    
    //echo "Received user status: " . $message->body . "\n";



    //$userStatusConnection = new AMQPStreamConnection('192.168.191.111', 5672, 'admin', 'admin');
    $userStatusConnection = null;
    $ips = array('192.168.191.111', '192.168.191.67', '192.168.191.215');

    foreach ($ips as $ip) {
        try {
            $userStatusConnection = new AMQPStreamConnection($ip, 5672, 'admin', 'admin');
            echo "Connected to RabbitMQ instance at $ip\n";
            break;
        } catch (Exception $e) {
            continue;
        }
    }

    if (!$userStatusConnection) {
        die("Could not connect to any RabbitMQ instance.");
    }

    $userStatusChannel = $userStatusConnection->channel();
    //$userStatusChannel->queue_declare('logBE2FE', false, false, false, false);
    $userStatusChannel->queue_declare('logBE2FE', false, false, false, false, ['x-ha-policy'=>'all']);
    $userStatusMessage = new AMQPMessage($authBody);
    $userStatusChannel->basic_publish($userStatusMessage, '', 'logBE2FE');
    $userStatusChannel->close();
    $userStatusConnection->close();
};

//TODO while (true)

while (true) {
  try {
      $channel->basic_consume('logDB2BE', '', false, true, false, false, $callback);
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
      $channel->queue_declare('logDB2BE', false, false, false, false, ['x-ha-policy'=>'all']);
  }
}
// Close the connection
$channel->close();
$connection->close();

?>
