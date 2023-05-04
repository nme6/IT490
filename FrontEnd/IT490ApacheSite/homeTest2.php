<?php
session_start(); // Start the session
if (!isset($_SESSION["username"])) {
  die(header("Location: login5.php")); // Redirect to login page if user is not logged in
}
?>


<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>PokéHub - Home</title>
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
    <h1 class="text-center">Welcome back, <?php echo $_SESSION["username"]; ?>!</h1>
    <!--<p class="text-center">Note for Neil: Last time logged in will go here. Debating between links to team building page and state page (pokedex basically). Also debating the virtual fight viewer thingy from proposal (Ellis and Max suggested just comparing team health because lets be honest, he's not gonna check that deep in the code). </p> -->
    <p class="text-center">You've succesfully logged in! More to come soon!</p>
    <br>
    <div class="container">
    	<div class="row align-items-center">
			<div class="col">
				<h2>Stats Viewer</h2>
				<p>View Pokémon and Pokémon Type stats</p>
				<form method="POST">
					<input type="radio" name="choice" value="input" onclick="showInput()"> Pokémon
					<input type="radio" name="choice" value="input2" onclick="showInput2()"> Pokémon Type
					<input type="radio" name="choice" value="dropdown" onclick="showDropdown()"> Name Dropdown
					<br>
					<div id="input" style="display: none; margin-top: 2%;">
						<input type="text" name="pokemon_name" placeholder="Ex: pikachu or starmie">
						<input type="submit" name="submitPokemonName" class="mt-1 btn btn-primary" value="Submit" />
					</div>
					<div id="input2" style="display: none; margin-top: 2%;">
						<input type="text" name="pokemon_type" placeholder="Ex: fire, fairy, or dark">
						<input type="submit" name="submitPokemonType" class="btn btn-primary" value="Submit" />
					</div>
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
						<select>
					<?php foreach ($names as $name): ?>
						<option value="<?php echo $name; ?>"><?php echo $name; ?></option>
					<?php endforeach; ?>
					</select>
					<input type="submit" name="submitPokemonType" class="mt-1 btn btn-primary" value="Submit" />
					</div>
				</form>
			</div>
			<div class="col">
				<p>Test</p>
			</div>
		</div>
    </div>
  </div>
  <script>
  function showInput() {
	document.getElementById("input").style.display = "block";
	document.getElementById("input2").style.display = "none";
	document.getElementById("dropdown").style.display = "none";
	localStorage.setItem("choice", "input");
  }

  function showInput2() {
  	document.getElementById("input").style.display = "none";
	document.getElementById("input2").style.display = "block";
	document.getElementById("dropdown").style.display = "none";
	localStorage.setItem("choice", "input2");
  }
  
  function showDropdown() {
  	document.getElementById("input").style.display = "none";
	document.getElementById("input2").style.display = "none";
	document.getElementById("dropdown").style.display = "block";
	localStorage.setItem("choice", "dropdown");
  }

  // Retrieve the stored radio button value and set the initial state
  window.onload = function() {
	var choice = localStorage.getItem("choice");
	if (choice === "input") {
		document.getElementsByName("choice")[0].checked = true;
		showInput();
	} else if (choice === "input2") {
  		document.getElementsByName("choice")[1].checked = true;
  		showInput2();
	} else if (choice === "dropdown") {
		document.getElementsByName("choice")[2].checked = true;
		showDropdown();
	}
  }
  </script>
  <script src="script.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
</body>
</html>

