<?php
// User input
$username = "mzj3";
$password = "yourMom";
$email = "mzj3@njit.edu";
$firstname = "Max";
$lastname = "Jacob";

// Connect to the database
$servername = "192.168.191.240";
$username_db = "test";
$password_db = "test";
$dbname = "test";

$conn = mysqli_connect($servername, $username_db, $password_db, $dbname);

// Check if the connection is successful
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Insert the user data into the database
$sql = "INSERT INTO users (username, password, email, firstname, lastname) VALUES ('$username', '$password', '$email', '$firstname', '$lastname')";

if (mysqli_query($conn, $sql)) {
    echo "New record created successfully";
} else {
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}

// Close the database connection
mysqli_close($conn);
?>
