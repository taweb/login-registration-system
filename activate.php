	
<?php 
  
// load the database connectionsn from external file
require_once 'database_connection.php';

// setting defaults
$activate = true; 
$activation_messages = [];
$activation_success = false;
$activation_success_message = "Activation completed successfully! Please follow the link below to log in";
$show_login_button = false;

// if activation code not in url, error
if (!isset($_GET['code'])) {
	$activation_messages[] = "There was a problem retrieving the activation code";
	$activate = false;
} else {
	// if activation code in url, clean the code ready for mysql query
	$clean_email_verification = mysqli_real_escape_string($db_connection, $_GET['code']);

	// query mysql database on the activation code provided
	$verification_check = "SELECT * FROM `users` WHERE `activation_code` = '$clean_email_verification'";
	$verification_check_result = mysqli_query($db_connection, $verification_check);
	if ($verification_check_result){

		// If not 1 unique activation code
		if (mysqli_num_rows($verification_check_result) != 1){
			$activation_messages[] = "There was a problem activating your account. To request your account to be activated again, please submit your credentials on the log in page and then click the button to request a new activation email to be sent";
			$activate = false;
		// If 1 activation code
		}elseif (mysqli_num_rows($verification_check_result) == 1){
			$row = mysqli_fetch_assoc($verification_check_result);
			// If account status is not already activated
			if ($row['account_status'] != 'activated') {
				// Change account status pending to activated
				$change_status = "UPDATE `users` SET `account_status` = 'activated' WHERE `activation_code` = '$clean_email_verification'"; 
				$change_status_result = mysqli_query($db_connection, $change_status);
				// check query ran ok
				if ($change_status_result){
					// query ran okay
					if (mysqli_affected_rows($db_connection) == 1){
						$activation_success = true;
						$show_login_button = true;
					}else{
						$activation_messages[] = "There was a problem updating the database credentials, please try again later";
						$activate = false;
					}
				}else{
					$activation_messages[] = "There was problem during the verification process, please try again later";
					$activate = false;
				}
			}else{
				// Account already activated
				$activation_messages[] = "This account has already been activated!";
				$activate = false;
				$show_login_button = true;
			}
		}
	}
}

?>


<!DOCTYPE html>
<html>
<head>
	<title>Account Activation</title>
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
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
	    <span class="navbar-toggler-icon"></span>
	  	</button>
		<div class="collapse navbar-collapse" id="navbarText">
	    	<ul class="navbar-nav ml-auto">
	    		<li class="nav-item active">
	        		<a class="nav-link" href="http://php.scotchbox/registration_system/registration.php">Register</a>
	      		</li> 
	      		<li class="nav-item">
	        		<a class="nav-link" href="http://php.scotchbox/registration_system/login.php">Login</a>
	      		</li>
	    	</ul>
	  
	  	</div>
	</nav>

	<section class="wrapper">
	<?php
		// if activation errors, return each in a list
		if($activate === false) {
		echo '<ul>';
			foreach ($activation_messages as $activation_message) {
				echo '<li style="color:red">'.$activation_message.'</li>';
			}
		echo '</ul>';
		}
		// display activation message if accound activation successful
		if($activation_success === true) {
			echo'<p style="color:green">'.$activation_success_message.'</p>';
		}
		// show login button
		if($show_login_button === true) {
			echo '<a href="http://php.scotchbox/registration_system/login.php" class="btn btn-success btn-lg" role="button">Proceed to Login</a>';
		}
	?>
	</section>
	<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>