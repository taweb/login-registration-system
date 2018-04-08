<?php

// load the database connections
require_once 'database_connection.php'; 
require_once'functions.php';

// Setting defaults 
$login_email = "";
$login_password = "";
$login_error = false;
$login_error_messages = []; 
$resend_activation_button = false;

// If user submits form
if ($_POST) {
	// collecting input values
	$login_email = $_POST["email"];
	$login_password = $_POST["password"];

	// checking input info in form
	if ($login_email ==="") {
		$login_error = true;
		$login_error_messages[] = "Please provide an email address";
	} 
	if ($login_password ==="") {
		$login_error = true;
		$login_error_messages[] = "Please provide a password";
	}

	if ($login_email !=="" && $login_password !=="") {
		$clean_login_email = mysqli_real_escape_string($db_connection, $login_email);

		$login_query = "SELECT * FROM `users` WHERE `email` = '$clean_login_email'";
		$login_query_result = mysqli_query($db_connection, $login_query);

		if ($login_query_result) {
			if (mysqli_num_rows($login_query_result) > 1) {
				$login_error = true;
				$login_error_messages[] = "There is a problem with the database";
			}elseif (mysqli_num_rows($login_query_result) === 0) {
				$login_error = true;
				$login_error_messages[] = "Login credentials not found";
			}else{
				$row_login = mysqli_fetch_assoc($login_query_result);
				if (password_verify($login_password, $row_login['password'])) {

					// If user submits a request to get a new activation email
					if($_POST['submit'] == 'Resend activation email') {

						$activation_code = md5(microtime().rand());
						$clean_activation_code = mysqli_real_escape_string($db_connection, $activation_code);
						// update activation code in the database for that user
						$query = "UPDATE `users` SET `activation_code` = '$clean_activation_code' WHERE `email` = '$clean_login_email'"; 
						$query_result = mysqli_query($db_connection, $query);
						// check query ran ok
						if ($query_result && mysqli_affected_rows($db_connection) == 1){
							// if query ran ok, send email
							$subject = 'New Activation Request';
							$message = '
							<h1>Hello!</h1>
							<p>You requested a new activation email! Please <a href="http://php.scotchbox/registration_system/activate.php?code='.$clean_activation_code.'">follow the link</a> to activate your account.</p>
							';
							// send email
							sendEmail($login_email, 'team@example.com', $subject, $message);
						}
					}elseif($row_login['account_status'] == "pending") {
						$login_error = true;
						$login_error_messages[] = "The account associated with this email is not yet activated, please check your inbox for instructions to activate your account. Alternatively, click below to request a new activation email to be sent";
						$resend_activation_button = true;
					}else{
						// Login details match database, start session
						session_start();
						$_SESSION['logged_in'] = 'YES';
						$_SESSION['user'] = $row_login['id'];
						header("Location:profile.php");
						exit;
					}	
				}else{
					$login_error = true;
					$login_error_messages[] = "Login credentials not found";
				}
			}
		}else{
			$login_error = true;
			$login_error_messages[] = "There was a problem retrieving credentials from the database";
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
	    		<li class="nav-item">
	        		<a class="nav-link" href="http://php.scotchbox/registration_system/registration.php">Register</a>
	      		</li>
	      		<li class="nav-item active">
	        		<a class="nav-link" href="http://php.scotchbox/registration_system/login.php">Login</a>
	      		</li>
	    	</ul>
	  
	  	</div>
	</nav>
	<section class="wrapper">
		<h1>Login</h1>
		<?php
		if($login_error === true) {
			echo '<ul>';
			foreach ($login_error_messages as $login_error_message) {
				echo '<li style="color:red">'.$login_error_message.'</li>';
			}
			echo '</ul>'; 
		}
		?> 

		<form action="login.php" method="post">
			<?php
			// if account not yet activated, show button to allow user to request a new activation email (and code) to be sent
			if ($resend_activation_button === true) {
				echo '<input class="btn btn-primary btn-dark" type="submit" name="submit" value="Resend activation email">';
			}
			?>
	  		<div class="form-group">
	    		<label for="email">Email address</label>
	    		<input type="email" name="email" class="form-control" id="email" aria-describedby="emailHelp" placeholder="Enter email" value="<?php if($login_error==true){echo $login_email;} ?>">
	  		</div>
	  		<div class="form-group">
	    		<label for="password">Password</label>
	    		<input type="password" name="password" class="form-control" id="exampleInputPassword1" placeholder="Password" value="<?php if($login_error===true){echo $login_password;} ?>">
	  		</div>
	  		<input class="btn btn-primary btn-dark" type="submit" name="submit" value="Login">
		</form>
	</section>

	<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>