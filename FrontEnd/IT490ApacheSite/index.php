<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>PokéHub</title>
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
          <a class="nav-link" href="#">Register</a>
          <a class="nav-link" href="#">Login</a>
        </div>
        <div style="padding:5px"></div>
        <button class="btn btn-outline-dark" id="btnSwitch">
          <i class="bi bi-sun-fill"></i>
        </button>
      </div>
    </div>
  </nav>
  <div class="container shadow min-vh-100 py-2">
    <img src="All_Starter_Pokemon.png" alt="Starter Pokemon Image" class="mx-auto d-block" width="50%"/>
    <img src="PokeHub_FinalLogo2.png" alt="PokeHub Logo" width="17.5%" class="mx-auto d-block" />
    <h1 class="text-center">Welcome to PokéHub</h1>
    <h3 class="text-center">The ultimate platform for Pokemon trainers and enthusiasts!</h3>
    <p class="text-center pLimiter">With PokéHub, you can easily build your own custom Pokémon teams and explore detailed stats for each Pokémon. Our intuitive interface allows you to quickly search through a vast library of Pokémon, filter by type, generation, and more to find your perfect lineup. Whether you're a competitive battler or a casual player, PokéHub has everything you need to take your game to the next level. With our advanced team-building tools and comprehensive Pokémon stats, you can optimize your team's strengths and weaknesses and dominate the competition. Join the PokéHub community today and discover new strategies, connect with fellow trainers, and take your Pokémon journey to the next level. Sign up now and start building your dream team!</p>
    <div class="py-3"></div>
  </div>
  <script src="script.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
</body>
</html>
