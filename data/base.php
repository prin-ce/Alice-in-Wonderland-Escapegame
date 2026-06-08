<?php
	ob_start();
	session_start();

	$timezone = date_default_timezone_set("Europe/London");

	$connexion = mysqli_connect("localhost", "root", "", "escapegame");

	if(mysqli_connect_errno()) {
		echo "Failed to connect: " . mysqli_connect_errno();
	}
?>