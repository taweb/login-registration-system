<?php
require_once 'database_connection.php'; 
session_start();
// if user visits page when not logged in, redirect to login page
if(!isset($_SESSION['logged_in'])) {
	header("Location:login.php");
	exit;
}
// destroy any existing session 
session_destroy();
?>

<!DOCTYPE html>
<html> 
<head>
	<title>Are you sure?</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
	<style type="text/css">
		.wrapper {
			max-width: 1200px;
			padding: 30px;
			margin: 0 auto;
		}
	</style>
</head>
<body>
	<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
		<a class="navbar-brand" href="http://php.scotchbox/registration_system/registration.php">SuperSecureSite</a>
	</nav>
	<section class="wrapper">
		<h1>You are logged out</h1>
		<a class="btn btn-success" role="button" href="http://php.scotchbox/registration_system/login.php">Log back in</a>
		<a class="btn btn-secondary" role="button" href="http://php.scotchbox/registration_system/registration.php">Take me to Home Page</a>
	</section>

	<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>