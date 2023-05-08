<?php
session_start();

// Checks if the user is logged in. If they are, redirect them to the home page as login.php should not be accessable to logged in users.
if (isset($_SESSION['username']) && isset($_SESSION["user_id"])) {
  header("Location: home.php");
  exit();
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>PokéHub - Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
  <link href="style.css" rel="stylesheet" type="text/css" />
</head>
<body>
  <nav class="navbar navbar-expand-md bg-dark-subtle justify-content-start">
    <div class="container-fluid">
      <a class="navbar-brand" href="index.php">
        <img src="PokeHub_FinalLogo2.png" alt="Logo" width="75" height="26.25" class="d-inline-block align-text-top">
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
        <div class="navbar-nav ms-auto">
          <a class="nav-link" href="register.php">Register</a>
          <a class="nav-link" href="login.php">Login</a>
        </div>
        <div style="padding:5px"></div>
        <button class="btn btn-outline-dark" id="btnSwitch">
          <i class="bi bi-sun-fill"></i>
        </button>
      </div>
    </div>
  </nav>
  <div class="container-fluid col-lg-4 offset-lg-4">
    <h1 style="padding-top:10px;">Login</h1>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label" for="username">Username</label>
            <input class="form-control" type="text" id="username" name="username" required />
        </div>
        <div class="mb-3">
            <label class="form-label" for="pw">Password</label>
            <input class="form-control" type="password" id="pw" name="password" required minlength="8" />
        </div>
        <input type="submit" class="mt-3 btn btn-primary" value="Login" />
    </form>
</div>

<?php
      session_start(); // Start the session

      require_once '/home/neil/IT490/IT490/FrontEnd/vendor/autoload.php';

      use PhpAmqpLib\Connection\AMQPStreamConnection;
      use PhpAmqpLib\Message\AMQPMessage;

      if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Create a connection to RabbitMQ
        //$connection = new AMQPStreamConnection('192.168.191.111', 5672, 'admin', 'admin');
        $connection = null;
	$ips = array('192.168.191.111', '192.168.191.67', '192.168.191.215');

	foreach ($ips as $ip) {
	    try {
		$connection = new AMQPStreamConnection($ip, 5672, 'admin', 'admin');
		//echo "Connected to RabbitMQ instance at $ip\n";
		break;
	    } catch (Exception $e) {
		continue;
	    }
	}

	if (!$connection) {
	    die("Could not connect to any RabbitMQ instance.");
	}
        
        $channel = $connection->channel();

        // Declare a queue for sending messages
        //$channel->queue_declare('logFE2BE', false, false, false, false);
        $channel->queue_declare('logFE2BE', false, false, false, false, ['x-ha-policy'=>'all']);

        // Publish the message to the queue
        $messageBody = json_encode([
          'username' => $username,
          'password' => $password,
        ]);

        // Define the message to send
        $message = new AMQPMessage($messageBody);

        // Publish the message to the queue
        $channel->basic_publish($message, '', 'logFE2BE');

        //Echo Msg to console
        //echo "-={[Front-end] Sent message to the Back-end!}=-\n$messageBody\n";

        // Close the channel and the connection
        $channel->close();
        $connection->close();
        
        
      			//Outside of Website Post if(statement)//
      	// Create a connection to RabbitMQ
	//$connectionReceive = new AMQPStreamConnection('192.168.191.111', 5672, 'admin', 'admin');
	$connectionReceive = null;
	$ips = array('192.168.191.111', '192.168.191.67', '192.168.191.215');

	foreach ($ips as $ip) {
	    try {
		$connectionReceive = new AMQPStreamConnection($ip, 5672, 'admin', 'admin');
		//echo "Connected to RabbitMQ instance at: $ip\n";
		break;
	    } catch (Exception $e) {
		continue;
	    }
	}

	if (!$connectionReceive) {
	    die("Could not connect to any RabbitMQ instance.");
	}
	
	$channelReceive = $connectionReceive->channel();

	// Declare the queue
	//$channelReceive->queue_declare('logBE2FE', false, false, false, false);
	$channelReceive->queue_declare('logBE2FE', false, false, false, false, ['x-ha-policy'=>'all']);

	//echo "-={[FrontEnd Log5] Waiting for Back-end messages. To exit press CTRL+C}=-\n";

	// Define the callback function to process messages from the queue
	$callbackReceive = function ($messageReceive) {

		$data = json_decode($messageReceive->getBody(),true);
		$isValid = $data['isValid'];


		if ($isValid == false)
		{
			//echo "\n[Incorrect format for Username/Password ]\n";

			//TODO for Neil: Redirects Page
			echo "<script>alert('Oopsie, you made a FORMAT mistake!');</script>";
			echo "<script>location.href='login.php';</script>";
		}


		if ($isValid == true)
		{
		        $userAuth = $data['userAuth'];
		        $username = $data['username'];
		        $user_id = $data['id'];

			//echo "The value of userAuth is: " . $userAuth . "\n";

		        if ($userAuth == false){
				//echo "\nInvalid Username or Password.\n";

				//TODO for Neil: Redirects Page
				echo "<script>alert('Oopsie, you made a INVALID mistake!');</script>";
				echo "<script>location.href='login.php';</script>";

		        } else {
		        	$_SESSION['username'] = $username;
		        	$_SESSION['user_id'] = $user_id;
				echo "\nSuccessfully Logged in!\n";

				//TODO for Neil: Redirects Page
				die(header("Location:home.php"));
			
		        }
		}

	};

	// Consume messages from the queue
	$channelReceive->basic_consume('logBE2FE', '', false, true, false, false, $callbackReceive);

	// Keep consuming messages until the channel is closed
	while ($channelReceive->is_open()) {
	    $channelReceive->wait();
	    break;
	}

	// Close the connection
	$channelReceive->close();
	$connectionReceive->close();
      }
?>

<script src="script.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
</body>
</html>
