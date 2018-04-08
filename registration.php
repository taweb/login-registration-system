<?php  
 
// load the database connections
require_once 'database_connection.php';

// Setting defaults 
$email = "";
$password = "";
$error = false;
$error_messages = [];
$reg_success = false;
$reg_success_message = "Registration complete. Please check your email inbox for instructions to activate your account";

// If user submits form
if ($_POST) {	

	// collect user input values
	$email = $_POST["email"];
	$password = $_POST["password"];

	// check email provided
	if ($email ==="") {
		$error = true;
		$error_messages[] = "Please provide an email address";
	} 
	// check password provided
	if ($password ==="") {
		$error = true;
		$error_messages[] = "Please provide a password";
	}else {
		// error if password not the required length
		if(strlen($password) <= 8) {
			$error = true;
			$error_messages[] = "Password must be at least 8 characters in length";
		}
		// error if password doesnt have at least 1 symbol
		if(!preg_match('/[^A-Za-z0-9 ]/', $password)) {
			$error = true;
			$error_messages[] = 'Password must include at least one symbol (non-alphanumeric) character';
		}
	}
	// if inputs pass validation tests, create database query
	if ($error === false) {
		// sanitise email input for use in mysql query
		$clean_email = mysqli_real_escape_string($db_connection, $email);
		// check that the email provided hasnt been registered before
		$email_check = "SELECT * FROM `users` WHERE `email` = '$clean_email'";
		// mysql query
		$email_check_result = mysqli_query($db_connection, $email_check);

		if ($email_check_result){
			// if the result of check returns rows in database, error as email registered previously
			if (mysqli_num_rows($email_check_result) > 0){
				$error = true;
				$error_messages[] = "the email you have entered is already registered";
			}
		}
	}

	// if all user input checks passed, continue
	if ($error === false) {

		$reg_success = true;

		// Generate activation code
		$activation_code = md5(microtime().rand());

		// clean / sanitise inputs for mysql input to prevent mysql injection attack
		// hash password first and then clean the hash
		$hashed_password = password_hash($password, PASSWORD_DEFAULT);
		$cleaned_hashed_password = mysqli_real_escape_string($db_connection, $hashed_password);
		$clean_activation_code = mysqli_real_escape_string($db_connection, $activation_code);

		// Build mysql query to insert account entry into database
		$query = "INSERT INTO `users` (`email`, `password`, `activation_code`) VALUES ('$clean_email', '$cleaned_hashed_password', '$clean_activation_code');";

		// Run the query
		$result = mysqli_query($db_connection, $query);

		// check query ran ok
		if ($result){
			// if 1 row of data changed
			if (mysqli_affected_rows($db_connection) == 1){
				// then send email to new registration email
				$headers = "From: Dev Me <team@example.com>\r\n";
				$headers .= "Reply-To: Help <help@example.com>\r\n";
				$headers .= "MIME-Version: 1.0\r\n";
				$headers .= "Content-Type: text/html;\r\n";

				$emailsubject = "New Registration";
				$emailmessage = '

				<html>
				<head>
				<title>Welcome</title>
				</head>
				<body>
				<h1>Welcome!</h1>
				<p>Thanks for registering. Please <a href="http://php.scotchbox/registration_system/activate.php?code='.$clean_activation_code.'">follow the link</a> to activate your account.</p>
				</body>
				</html>
				';
				// if email not sent to user, remove the account and provide error to user
				if (!mail($email, $emailsubject, $emailmessage, $headers)){
					echo "There was a problem registering your account, please try again later";
					$remove_account = "DELETE FROM `users` WHERE `email` = '$clean_email'";
					$remove_account_result = mysqli_query($db_connection, $remove_account);
				}

			}
		}else{
			$error = true;
			$error_messages[] = "Something went wrong";
		}
	}
}

?>


<!DOCTYPE html>
<html>
<head>
	<title>Registration Page</title>
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
		<h1>Register</h1>

		<?php
			// if errors, for each error, error list items in a list
			if($error === true) {
			echo '<ul>';
				foreach ($error_messages as $error_message) {
					echo '<li style="color:red">'.$error_message.'</li>';
				}
			echo '</ul>';
			}
			// if no errors, provide success message
			if($reg_success === true) {
				echo '<p style="color:green">'.$reg_success_message.'</p>'; 
			}
		?>
		<form action="registration.php" method="post">
	  		<div class="form-group">
	    		<label for="email">Email address</label>
	    		<input type="email" name="email" class="form-control" id="email" aria-describedby="emailHelp" placeholder="Enter email" value="<?php if($error==true){echo $email;} ?>">
	    		<small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
	  		</div>
	  		<div class="form-group">
	    		<label for="password">Password</label>
	    		<input type="password" name="password" class="form-control" id="exampleInputPassword1" placeholder="Password" value="<?php if($error===true){echo $password;} ?>">
	  		</div>
	  		<input class="btn btn-primary btn-dark" type="submit" name="submit" value="Create Account">
		</form>
	</section>

	<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>