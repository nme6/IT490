<?php
session_start(); // Start the session
if (!isset($_SESSION["username"]) && !isset($_SESSION["user_id"])) {
  die(header("Location: login5.php")); // Redirect to login page if user is not logged in
}

$_SESSION['choiceRec'] = ' ';
?>


<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>PokéHub - Stats Viewer</title>
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
		  <a class="nav-link" href="home.php">Team Builder/Viewer</a>
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
    	<div class="row align-items-center">
			<div class="col">
				<h2>Stats Viewer</h2>
				<p>View Pokémon and Pokémon Type stats</p>
				<form method="POST">
					<input type="radio" name="choice" value="dropdown" onclick="showDropdown()"> Pokémon
					<input type="radio" name="choice" value="input2" onclick="showInput2()"> Pokémon Type
					<br>
					<div id="dropdown" style="display: none; margin-top: 2%;">
						<?php 
						$file = fopen("pokemonNames.txt", "r");
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
						<select class="form-select" name="selectedPokeName">
						<?php foreach ($names as $name): ?>
							<option value="<?php echo $name; ?>"><?php echo $name; ?></option>
						<?php endforeach; ?>
						</select>
						<input type="submit" name="submitPokeName" class="btn btn-primary" value="Submit" />
					</div>
					<div id="input2" style="display: none; margin-top: 2%;">
						<?php 
						$file = fopen("pokemonTypes.txt", "r");
						$types = array();

						while(!feof($file)) {
						$type = fgets($file);
						$type = trim($type);
						if ($type === 'fairy') {
							array_push($types, $type);
							break;
						}
						array_push($types, $type);
						}

						fclose($file);
						?>
						<select class="form-select" style="width: 20%;" name="selectedPokeType">
						<?php foreach ($types as $type): ?>
							<option value="<?php echo $type; ?>"><?php echo $type; ?></option>
						<?php endforeach; ?>
						</select>
						<input type="submit" name="submitPokeType" class="btn btn-primary" value="Submit" />
					</div>
				</form>
			</div>
			<?php
				require_once '/home/neil/IT490/IT490/FrontEnd/vendor/autoload.php';
				
				use PhpAmqpLib\Connection\AMQPStreamConnection;
				use PhpAmqpLib\Message\AMQPMessage;
				
				if ($_SERVER['REQUEST_METHOD'] === 'POST') {
					if(isset($_POST['submitPokeName'])) {
						$choice = "pokemon type";
						$name_input = $_POST['selectedPokeName'];
						
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
						$channel->queue_declare('pokeFE2BE', false, true, false, false, ['x-ha-policy'=>'all']);
						
						// Publish the message to the queue
						$messageBody = json_encode([
						'choice' => $choice,
						'user_input' => $name_input,
						]);
						
						// Define the message to send
						$message = new AMQPMessage($messageBody);

						// Publish the message to the queue
						$channel->basic_publish($message, '', 'pokeFE2BE');
						
						// Close the channel and the connection
						$channel->close();
						$connection->close();
					}
					if(isset($_POST['submitPokeType'])) {
						$choice = "damage type";
						$type_input = $_POST['selectedPokeType'];
						
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
						$channel->queue_declare('pokeFE2BE', false, true, false, false, ['x-ha-policy'=>'all']);
						
						// Publish the message to the queue
						$messageBody = json_encode([
						'choice' => $choice,
						'user_input' => $type_input,
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
						$_SESSION['choiceRec'] = $data['choice'];
						//echo $_SESSION['choiceRec'];
						//echo $data['damage_type'] . "NAME <br>";
						//echo $data['double_from'] . "DF <br>";
						//echo $data['double_to'] . "DT <br>";
						//echo $data['half_from'] . "HF <br>";
						//echo $data['half_to'] . "HT <br>";
						//echo $data['no_from'] . "NF <br>";
						//echo $data['no_to'] . "NT";
						
						if ($data['choice'] == 'pokemon type') {
							$_SESSION['pokeName'] = $data['pokemon_name'];
							$_SESSION['pokeType'] = $data['types'];
						} else if ($data['choice'] == 'damage type') {
							$_SESSION['typeName'] = $data['damage_type'];
							$_SESSION['doubleDamFrom'] = $data['double_from'];
							$_SESSION['doubleDamTo'] = $data['double_to'];
							$_SESSION['halfDamFrom'] = $data['half_from'];
							$_SESSION['halfDamTo'] = $data['half_to'];
							$_SESSION['noDamFrom'] = $data['no_from'];
							$_SESSION['noDamTo'] = $data['no_to'];
						}
						
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
			<div class="col">
				<!-- Pokemon Name Table -->
				<?php
				if ($_SESSION['choiceRec'] == "pokemon type") {
				    ?>
				    <table class="table table-striped">
					<thead>
					    <tr>
						<th>RESULTS</th>
					    </tr>
					</thead>
					<tbody>
					    <?php
					    // Define the variables
					    $pokeName = $_SESSION['pokeName'];
					    $pokeType = $_SESSION['pokeType'];
					    ?>
					    <tr>
						<td style="text-transform: uppercase;"><?php echo $pokeName; ?></td>
						<td><?php echo $pokeType; ?></td>
					    </tr>
					</tbody>
				    </table>
				    <?php
				}
				?>
				<!-- Damage Type Table -->
				<?php
				if ($_SESSION['choiceRec'] == "damage type") {
				    ?>
				    <table class="table table-striped">
					<thead>
					    <tr>
						<th>RESULTS</th>
					    </tr>
					</thead>
					<tbody>
					    <?php
					    // Define the variables
					    $typeName = $_SESSION['typeName'];
					    $doubleDamFrom = $_SESSION['doubleDamFrom'];
					    $doubleDamTo = $_SESSION['doubleDamTo'];
					    $halfDamFrom = $_SESSION['halfDamFrom'];
					    $halfDamTo = $_SESSION['halfDamTo'];
					    $noDamFrom = $_SESSION['noDamFrom'];
					    $noDamTo = $_SESSION['noDamTo'];
					    
					    // Check if any damage relations are returned before creating the table row
					    if ($doubleDamFrom || $doubleDamTo || $halfDamFrom || $halfDamTo || $noDamFrom || $noDamTo) {
						// Start the table row
						echo "<tr>";

						// Add the type name to the row
						echo "<td style=\"text-transform: uppercase;\">$typeName</td>";

						// Add the damage relations to the row
						echo "<td>";
						if ($doubleDamFrom) {
						    echo "Double damage from: " . $doubleDamFrom . "<br>";
						}
						if ($doubleDamTo) {
						    echo "Double damage to: " . $doubleDamTo . "<br>";
						}
						if ($halfDamFrom) {
						    echo "Half damage from: " . $halfDamFrom . "<br>";
						}
						if ($halfDamTo) {
						    echo "Half damage to: " . $halfDamTo . "<br>";
						}
						if ($noDamFrom) {
						    echo "No damage from: " . $noDamFrom . "<br>";
						}
						if ($noDamTo) {
						    echo "No damage to: " . $noDamTo . "<br>";
						}
						echo "</td>";

						// End the table row
						echo "</tr>";
					    }
					    ?>
					</tbody>
				    </table>
				    <?php
				}
				?>
			</div>
		</div>
    </div>
	<br>
	
	</div>
  <script>
	function showInput2() {
		document.getElementById("input2").style.display = "block";
		document.getElementById("dropdown").style.display = "none";
		localStorage.setItem("choice", "input2");
	}
	
	function showDropdown() {
		document.getElementById("input2").style.display = "none";
		document.getElementById("dropdown").style.display = "block";
		localStorage.setItem("choice", "dropdown");
	}

	// Retrieve the stored radio button value and set the initial state
	window.onload = function() {
		var choice = localStorage.getItem("choice");
		if (choice === "input2") {
			document.getElementsByName("choice")[1].checked = true;
			showInput2();
		} else if (choice === "dropdown") {
			document.getElementsByName("choice")[0].checked = true;
			showDropdown();
		}
	}
  </script>
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