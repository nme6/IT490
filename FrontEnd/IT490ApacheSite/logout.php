<?php
session_start();

if (!isset($_SESSION["username"])) {
	die(header("Location: errorPage.php"));
}

function reset_session() {
	session_unset();
	session_destroy();
	session_start();
}

reset_session();
die(header("Location: successLogout.php"));
