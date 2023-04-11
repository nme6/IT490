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

// Declare a queue for receiving messages
$channel->queue_declare('logFE2BE', false, false, false, false, ['x-ha-policy'=>'all']);

echo "-={[BackEnd Log2] Waiting for Front-end messages. To exit press CTRL+C}=-\n";

// Define the callback function to process received messages
$callback = function ($message) use ($channel) {
    //echo "Received message from Front-end: " . $message->body . "\n";
	
    $data = json_decode($message->getBody(), true);

    // Get the data from the message body
    $username = $data['username'];
    $password = $data['password'];
    //$email = $data['email'];
    
    //Sanitize the username and password data
    $sanitizedUsername = filter_var($username, FILTER_SANITIZE_STRING);
    $sanitizedPassword = filter_var($password, FILTER_SANITIZE_STRING);
    //echo $sanitizedUsername;
    //$sanitizedEmail = filter_var($email, FILTER_SANITIZE_EMAIL);
    
//##########################[Validation of Variables for Registration]#############################
//Check for valid Email FORMAT (Not database entry)
   // if (preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.(edu|[a-zA-Z]{2,})$/', $sanitizedEmail)) {
	  // $isValidEmail = true; echo "[Valid Email ✓ ]\n";
   // } else {
	  // $isValidEmail = false; echo "[Invalid Email ✗ ]\n";
    // }


//Check for valid Username FORMAT (Not database entry)
    if (preg_match('/^[a-zA-Z0-9_]+$/', $sanitizedUsername)) {
	   $isValidUsername = true; echo "[Valid Username ✓ ]\n";
    } else {
	   $isValidUsername = false; echo "[Invalid Username ✗ ]\n";
    }

//Check if password is using a valid format (At least 1 letter, at least 1 digit and at least 8 chars)
    if (preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $sanitizedPassword)) {
	    $isValidPassword = true; echo "[Valid Password Format ✓ ]\n";
    } else {
    	    $isValidPassword = false; echo "[Invalid Password Format ✗ ]\n";
    }


//#################################[Login Validation]##########################################	    
    if (!($isValidUsername && $isValidPassword)) 
    {
	    echo "\n[Login Unsuccessful ✗ ]\n";
	    $userAuth = false;
	    $isValid = false;
          
	//Send a message in the JSON that will let the front end know there was an error. Include all validated variable true/false values
        $errorMessageBody = json_encode
        (
		[
			'isValidUsername' => $isValidUsername,
			//'isValidEmail' => $isValidEmail,
			'isValidPassword' => $isValidPassword,
			'userAuth' => $userAuth,
			'isValid' => $isValid,

		]
        );

	    // Send a new error message to FrontEnd 
	


	$errorConnection = null;
	$ips = array('192.168.191.111', '192.168.191.67');
	foreach ($ips as $ip) {
    		try {
        		$errorConnection = new AMQPStreamConnection($ip, 5672, 'admin', 'admin');
        		echo "Connected to RabbitMQ instance at $ip\n";

        		break;
    		} catch (Exception $e) {
        		continue;
    			}

	}

	if (!$errorConnection) {
    		die("Could not connect to any RabbitMQ instance.");
		}

        //$errorConnection = new AMQPStreamConnection('192.168.191.111', 5672, 'admin', 'admin');
        $errorChannel = $errorConnection->channel();
        $errorChannel->queue_declare('logBE2FE', false, false, false, false, ['x-ha-policy'=>'all']);
        $errorMessage = new AMQPMessage($errorMessageBody);
        $errorChannel->basic_publish($errorMessage, '', 'logBE2FE');
        $errorChannel->close();
	$errorConnection->close();

        echo "[Sent ERROR data to FrontEnd!: " . $errorMessage->body . "]\n\n";
    }
    else
      {
	echo "\n[Successfully Sent to Database ✓ ]\n";
	
	// Hash the password using bcrypt
    	//$hashPassword = password_hash($sanitizedPassword, PASSWORD_BCRYPT); 

    	//Funnel everything back into the json. Also add the salt value.
    	$successMessageBody = json_encode
    	(	
        	[
	       		
               		'password' => $sanitizedPassword,
              		'username' => $sanitizedUsername,
               		
               		
        	]
    	);

    	// Send a new message to the database
	//$successConnection = new AMQPStreamConnection('192.168.191.111', 5672, 'admin', 'admin');


        $successConnection = null;
        $ips = array('192.168.191.111', '192.168.191.67');
        //global $actual_ip;
        //$actual_ip = null;
        foreach ($ips as $ip) {
                try {
                        $successConnection = new AMQPStreamConnection($ip, 5672, 'admin', 'admin');
                        echo "Connected to RabbitMQ instance at $ip\n";
                        //$actual_ip = $ip;
                        break;
                } catch (Exception $e) {
                        continue;
                        }
                 // return $actual_ip;
                }

        if (!$successConnection) {
                die("Could not connect to any RabbitMQ instance.");
                }



    	$successChannel = $successConnection->channel();
    	$successChannel->queue_declare('logBE2DB', false, false, false, false, ['x-ha-policy'=>'all']);
    	$successMessage = new AMQPMessage($successMessageBody);
    	$successChannel->basic_publish($successMessage, '', 'logBE2DB');
    	$successChannel->close();
    	$successConnection->close();
    }
};

// Start consuming messages from the queue
while (true) {
    try {
        $channel->basic_consume('logFE2BE', '', false, true, false, false, $callback);
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
        $channel->queue_declare('logFE2BE', false, false, false, false, ['x-ha-policy'=>'all']);
    }
}

// Close the channel and the connection
$channel->close();
$connection->close();
?>

