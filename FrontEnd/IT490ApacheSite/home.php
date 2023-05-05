<?php
session_start(); // Start the session
if (!isset($_SESSION["username"]) && !isset($_SESSION["user_id"])) {
  die(header("Location: login.php")); // Redirect to login page if user is not logged in
}

$_SESSION['choiceRec2'] = ' ';
$_SESSION['teamMember1'] = ' ';
$_SESSION['teamMember2'] = ' ';
$_SESSION['teamMember3'] = ' ';
$_SESSION['teamMember4'] = ' ';
$_SESSION['teamMember5'] = ' ';
$_SESSION['teamMember6'] = ' ';
?>


<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>PokéHub - Team Builder/Viewer</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
  <link href="style.css" rel="stylesheet" type="text/css" />
</head>

<body>
  <nav class="navbar navbar-expand-md bg-dark-subtle justify-content-start">
    <div class="container-fluid">
      <a class="navbar-brand" href="home.php">
        <img src="PokeHub_FinalLogo2.png" alt="Logo" width="75" height="26.25" class="d-inline-block align-text-top">
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
        <div class="navbar-nav ms-auto">
		  <a class="nav-link" href="home2.php">Stats Viewer</a>
          <a class=nav-link>User: <?php echo $_SESSION["username"]; ?></a>
          <a class="nav-link" href="logout.php">Logout</a>
        </div>
        <div style="padding:5px"></div>
        <button class="btn btn-outline-dark" id="btnSwitch">
          <i class="bi bi-sun-fill"></i>
        </button>
      </div>
    </div>
  </nav>
  <div class="container shadow min-vh-100 py-2">
    <img src="All_Starter_Pokemon.png" alt="Starter Pokemon Image" class="mx-auto d-block imageFlipper" width="50%"/>
    <img src="PokeHub_FinalLogo2.png" alt="PokeHub Logo" width="17.5%" class="mx-auto d-block" />
    <br>
	<div class="container">
		<h2>Team Builder</h2>
		<p>Build your team of Pokémon</p>
		<form method="POST">
			<div class="row align-items-center text-center">
				<div class="col border border-black">
					Team Member 1<br>
					<?php 
						$file = fopen("pokemonNames2.txt", "r");
						$names = array();

						while(!feof($file)) {
						$name = fgets($file);
						$name = trim($name);
						if ($name === 'iron-leaves') {
							array_push($names, $name);
							break;
						}
						array_push($names, $name);
						}

						fclose($file);
					?>
					<select name="pokeMember1" class="mb-2 form-select">
						<?php foreach ($names as $name): ?>
							<option value="<?php echo $name; ?>"><?php echo $name; ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="col border border-black">
					Team Member 2<br>
					<?php 
						$file = fopen("pokemonNames2.txt", "r");
						$names = array();

						while(!feof($file)) {
						$name = fgets($file);
						$name = trim($name);
						if ($name === 'iron-leaves') {
							array_push($names, $name);
							break;
						}
						array_push($names, $name);
						}

						fclose($file);
					?>
					<select name="pokeMember2" class="mb-2 form-select">
						<?php foreach ($names as $name): ?>
							<option value="<?php echo $name; ?>"><?php echo $name; ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="col border border-black">
					Team Member 3<br>
					<?php 
						$file = fopen("pokemonNames2.txt", "r");
						$names = array();

						while(!feof($file)) {
						$name = fgets($file);
						$name = trim($name);
						if ($name === 'iron-leaves') {
							array_push($names, $name);
							break;
						}
						array_push($names, $name);
						}

						fclose($file);
					?>
					<select name="pokeMember3" class="mb-2 form-select">
						<?php foreach ($names as $name): ?>
							<option value="<?php echo $name; ?>"><?php echo $name; ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="col border border-black">
					Team Member 4<br>
					<?php 
						$file = fopen("pokemonNames2.txt", "r");
						$names = array();

						while(!feof($file)) {
						$name = fgets($file);
						$name = trim($name);
						if ($name === 'iron-leaves') {
							array_push($names, $name);
							break;
						}
						array_push($names, $name);
						}

						fclose($file);
					?>
					<select name="pokeMember4" class="mb-2 form-select">
						<?php foreach ($names as $name): ?>
							<option value="<?php echo $name; ?>"><?php echo $name; ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="col border border-black">
					Team Member 5<br>
					<?php 
						$file = fopen("pokemonNames2.txt", "r");
						$names = array();

						while(!feof($file)) {
						$name = fgets($file);
						$name = trim($name);
						if ($name === 'iron-leaves') {
							array_push($names, $name);
							break;
						}
						array_push($names, $name);
						}

						fclose($file);
					?>
					<select name="pokeMember5" class="mb-2 form-select">
						<?php foreach ($names as $name): ?>
							<option value="<?php echo $name; ?>"><?php echo $name; ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="col border border-black">
					Team Member 6<br>
					<?php 
						$file = fopen("pokemonNames2.txt", "r");
						$names = array();

						while(!feof($file)) {
						$name = fgets($file);
						$name = trim($name);
						if ($name === 'iron-leaves') {
							array_push($names, $name);
							break;
						}
						array_push($names, $name);
						}

						fclose($file);
					?>
					<select name="pokeMember6" class="mb-2 form-select">
						<?php foreach ($names as $name): ?>
							<option value="<?php echo $name; ?>"><?php echo $name; ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>
			<input type="submit" name="submitPokeTeam" style="margin-left: 39.25%;" class="mt-2 btn btn-primary" value="Submit & Save" />
			<input type="submit" name="viewPokeTeam" style="margin-left: 1%;" class="mt-2 btn btn-primary" value="View Saved Team" />
		</form>
		<!-- PHP starts here -->
		<?php
			require_once '/home/neil/IT490/IT490/FrontEnd/vendor/autoload.php';
			
			use PhpAmqpLib\Connection\AMQPStreamConnection;
			use PhpAmqpLib\Message\AMQPMessage;
			
			if ($_SERVER['REQUEST_METHOD'] === 'POST') {
				if(isset($_POST['submitPokeTeam'])) {
					$choice = "team build";
					$userID = $_SESSION['user_id'];
					$member1 = $_POST['pokeMember1'];
					$member2 = $_POST['pokeMember2'];
					$member3 = $_POST['pokeMember3'];
					$member4 = $_POST['pokeMember4'];
					$member5 = $_POST['pokeMember5'];
					$member6 = $_POST['pokeMember6'];
					
					// Create a connection to RabbitMQ
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
					$channel->queue_declare('pokeFE2BE', false, true, false, false, ['x-ha-policy'=>'all']);
					
					// Publish the message to the queue
					$messageBody = json_encode([
					'user_id' => $userID,
					'choice' => $choice,
					'member_1' => $member1,
					'member_2' => $member2,
					'member_3' => $member3,
					'member_4' => $member4,
					'member_5' => $member5,
					'member_6' => $member6,
					]);

					// Define the message to send
					$message = new AMQPMessage($messageBody);

					// Publish the message to the queue
					$channel->basic_publish($message, '', 'pokeFE2BE');
					
					// Close the channel and the connection
					$channel->close();
					$connection->close();
				}
				if(isset($_POST['viewPokeTeam'])) {
					$choice = "team view";
					$userID = $_SESSION['user_id'];
					
					// Create a connection to RabbitMQ
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
					$channel->queue_declare('pokeFE2BE', false, true, false, false, ['x-ha-policy'=>'all']);
					
					// Publish the message to the queue
					$messageBody = json_encode([
					'user_id' => $userID,
					'choice' => $choice,
					]);

					// Define the message to send
					$message = new AMQPMessage($messageBody);

					// Publish the message to the queue
					$channel->basic_publish($message, '', 'pokeFE2BE');
					
					// Close the channel and the connection
					$channel->close();
					$connection->close();
				}

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
				$channelReceive->queue_declare('pokeBE2FE', false, true, false, false, ['x-ha-policy'=>'all']);
				
				// Define the callback function to process messages from the queue
				$callbackReceive = function ($messageReceive) {
					$data = json_decode($messageReceive->getBody(),true);
					$_SESSION['choiceRec2'] = $data['choice'];
					$_SESSION['teamMember1'] = $data['member_1'];
					$_SESSION['teamMember2'] = $data['member_2'];
					$_SESSION['teamMember3'] = $data['member_3'];
					$_SESSION['teamMember4'] = $data['member_4'];
					$_SESSION['teamMember5'] = $data['member_5'];
					$_SESSION['teamMember6'] = $data['member_6'];
				};

				// Consume messages from the queue
				$channelReceive->basic_consume('pokeBE2FE', '', false, true, false, false, $callbackReceive);

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
		<!-- PHP ends here -->
		</div>
		<br>
		<div class="row align-items-center text-center">
			<?php
				if ($_SESSION['choiceRec2'] == 'team build') {
			?>
				<h3 style = "text-align: left;">Your Team Build</h3>
				<div class="col border border-black py-2" style="text-transform: uppercase;">
					<?php
						if ($_SESSION['teamMember1'] == 'none') {
							echo 'No Pokémon selected';
						} else {
							//Define variable
							$teamMember1 = $_SESSION['teamMember1'];
							echo $teamMember1;
						}
					?>
				</div>
				<div class="col border border-black py-2" style="text-transform: uppercase;">
					<?php
						if ($_SESSION['teamMember2'] == 'none') {
							echo 'No Pokémon selected';
						} else {
							//Define variable
							$teamMember2 = $_SESSION['teamMember2'];
							echo $teamMember2;
						}
					?>
				</div>
				<div class="col border border-black py-2" style="text-transform: uppercase;">
					<?php
						if ($_SESSION['teamMember3'] == 'none') {
							echo 'No Pokémon selected';
						} else {
							//Define variable
							$teamMember3 = $_SESSION['teamMember3'];
							echo $teamMember3;
						}
					?>
				</div>
				<div class="col border border-black py-2" style="text-transform: uppercase;">
					<?php
						if ($_SESSION['teamMember4'] == 'none') {
							echo 'No Pokémon selected';
						} else {
							//Define variable
							$teamMember4 = $_SESSION['teamMember4'];
							echo $teamMember4;
						}
					?>
				</div>
				<div class="col border border-black py-2" style="text-transform: uppercase;">
					<?php
						if ($_SESSION['teamMember5'] == 'none') {
							echo 'No Pokémon selected';
						} else {
							//Define variable
							$teamMember5 = $_SESSION['teamMember5'];
							echo $teamMember5;
						}
					?>
				</div>
				<div class="col border border-black py-2" style="text-transform: uppercase;">
					<?php
						if ($_SESSION['teamMember6'] == 'none') {
							echo 'No Pokémon selected';
						} else {
							//Define variable
							$teamMember6 = $_SESSION['teamMember6'];
							echo $teamMember6;
						}
					?>
				</div>
			</div>
			<br>
			<input type="button" style="margin-left: 45.5%;" class="mt-2 btn btn-primary" value="Download" onclick="downloadFile()">
			<?php
				}
			?>
		</div>

		<div class="row align-items-center text-center">
			<?php
				if ($_SESSION['choiceRec2'] == 'team view') {
			?>
				<h3 style = "text-align: left;">Your Team Build</h3>
				<div class="col border border-black py-2" style="text-transform: uppercase;">
					<?php
						if ($_SESSION['teamMember1'] == 'none') {
							echo 'No Pokémon selected';
						} else {
							//Define variable
							$teamMember1 = $_SESSION['teamMember1'];
							echo $teamMember1;
						}
					?>
				</div>
				<div class="col border border-black py-2" style="text-transform: uppercase;">
					<?php
						if ($_SESSION['teamMember2'] == 'none') {
							echo 'No Pokémon selected';
						} else {
							//Define variable
							$teamMember2 = $_SESSION['teamMember2'];
							echo $teamMember2;
						}
					?>
				</div>
				<div class="col border border-black py-2" style="text-transform: uppercase;">
					<?php
						if ($_SESSION['teamMember3'] == 'none') {
							echo 'No Pokémon selected';
						} else {
							//Define variable
							$teamMember3 = $_SESSION['teamMember3'];
							echo $teamMember3;
						}
					?>
				</div>
				<div class="col border border-black py-2" style="text-transform: uppercase;">
					<?php
						if ($_SESSION['teamMember4'] == 'none') {
							echo 'No Pokémon selected';
						} else {
							//Define variable
							$teamMember4 = $_SESSION['teamMember4'];
							echo $teamMember4;
						}
					?>
				</div>
				<div class="col border border-black py-2" style="text-transform: uppercase;">
					<?php
						if ($_SESSION['teamMember5'] == 'none') {
							echo 'No Pokémon selected';
						} else {
							//Define variable
							$teamMember5 = $_SESSION['teamMember5'];
							echo $teamMember5;
						}
					?>
				</div>
				<div class="col border border-black py-2" style="text-transform: uppercase;">
					<?php
						if ($_SESSION['teamMember6'] == 'none') {
							echo 'No Pokémon selected';
						} else {
							//Define variable
							$teamMember6 = $_SESSION['teamMember6'];
							echo $teamMember6;
						}
					?>
				</div>
			</div>
			<br>
			<input type="button" style="margin-left: 45.5%;" class="mt-2 btn btn-primary" value="Download" onclick="downloadFile()">
			<?php
				}
			?>
		</div>

	</div>
  </div>
  <script>
	function downloadFile() {
	// Get the current date and time
	var now = new Date();
	var month = now.getMonth() + 1; // add 1 to get month starting from 1 instead of 0
	var day = now.getDate();
	var year = now.getFullYear();
	var hour = now.getHours();
	var minute = now.getMinutes();
	var ampm = hour >= 12 ? 'pm' : 'am';

	// Format the date and time string
	var dateTime = month.toString().padStart(2, '0') + '-' + day.toString().padStart(2, '0') + '-' + year + '-' + hour.toString().padStart(2, '0') + '_' + minute.toString().padStart(2, '0') + ampm;

	// JavaScript code to output and download the file
	var userInfo = "<?php echo $_SESSION['username'];?>";
	var teamMember1 = "<?php echo $_SESSION['teamMember1']; ?>";
	var teamMember2 = "<?php echo $_SESSION['teamMember2']; ?>";
	var teamMember3 = "<?php echo $_SESSION['teamMember3']; ?>";
	var teamMember4 = "<?php echo $_SESSION['teamMember4']; ?>";
	var teamMember5 = "<?php echo $_SESSION['teamMember5']; ?>";
	var teamMember6 = "<?php echo $_SESSION['teamMember6']; ?>";
	
	var content = "Username: " + userInfo + " " + "\n" +
					"Date & Time: " + dateTime + "\n" +
					"Team Member 1: " + teamMember1 + "\n" +
					"Team Member 2: " + teamMember2 + "\n" +
					"Team Member 3: " + teamMember3 + "\n" +
					"Team Member 4: " + teamMember4 + "\n" +
					"Team Member 5: " + teamMember5 + "\n" +
					"Team Member 6: " + teamMember6 + "\n";
	
	// Create a Blob object from the text content
	var blob = new Blob([content], { type: 'text/plain' });
	
	// Create a link element to trigger the download
	var link = document.createElement('a');
	link.download = userInfo + "_PokemonTeam_" + dateTime + '.txt';
	link.href = window.URL.createObjectURL(blob);
	link.click();
	}
  </script>
  <script src="script.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
</body>
</html>