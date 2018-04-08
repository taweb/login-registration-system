
<?php
require_once 'database_connection.php';
session_start();
// if user not logged in, redirect them to login page
if(!isset($_SESSION['logged_in'])) {
	header("Location:login.php");
	exit;
}
// setting defaults
$password = "";
$error = false;
$error_messages = [];
$pwd_change_success = false;
$pwd_change_success_message = "Password successfully updated";
// if user submits form to change account password
if ($_POST) {	
	// collecting new password
	$password_change = $_POST["password"];
	// checking input info in form
	if ($password_change ==="") {
		$error = true;
		$error_messages[] = "Please provide a password";
	}else {
		if(strlen($password_change) <= 8) {
			$error = true;
			$error_messages[] = "Password must be at least 8 characters in length";
		}
		if(!preg_match('/[^A-Za-z0-9 ]/', $password_change)) {
			$error = true;
			$error_messages[] = 'Password must include at least one symbol (non-alphanumeric) character';
		}
		if($error === false) {
			$change_pwd_query = "SELECT `password` FROM `users` WHERE `id` = '{$_SESSION['user']}'";
			$change_pwd_query_result = mysqli_query($db_connection, $change_pwd_query);
			if ($change_pwd_query_result) {
				if (mysqli_num_rows($change_pwd_query_result) !== 1) {
					$error = true;
					$error_messages[] = "How the hell did that happen?!";
				}
				else {
					$row_change_pwd = mysqli_fetch_assoc($change_pwd_query_result);
					if (password_verify($password_change, $row_change_pwd['password'])) {
						$error = true;
						$error_messages[] = 'That is your current password, please choose a new one';
					}
					else {
						// Password is ok, and not the old one, save this to database
						$hashed_password = password_hash($password_change, PASSWORD_DEFAULT);
						$cleaned_hashed_password = mysqli_real_escape_string($db_connection, $hashed_password);

						$update_password = "UPDATE `users` SET `password` = '$cleaned_hashed_password' WHERE `id` = '{$_SESSION['user']}'"; 
						$update_password = mysqli_query($db_connection, $update_password);

						if ($update_password && mysqli_affected_rows($db_connection) === 1){
								$pwd_change_success = true;
						}
					}
				}
			}
		}
	}
}

?>

<!DOCTYPE html>
<html>
<head>
	<title>Update Details</title>
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
	        		<a class="nav-link" href="http://php.scotchbox/registration_system/profile.php">Profile</a>
	      		</li>
	    		<li class="nav-item active">
	        		<a class="nav-link" href="http://php.scotchbox/registration_system/updatedetails.php">Update Your Details</a>
	      		</li>
	      		<li class="nav-item">
	        		<a class="nav-link" data-toggle="modal" data-target="#Modal" href="#">Sign Out</a>
	      		</li>
	    	</ul>
	  	</div>
	</nav>
	<div class="modal fade" id="Modal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
	  	<div class="modal-dialog" role="document">
	    	<div class="modal-content">
	      		<div class="modal-header">
			        <h5 class="modal-title" id="ModalLabel">Are you sure?</h5>
			        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			          <span aria-hidden="true">&times;</span>
			        </button>
	      		</div>
		      	<div class="modal-body">
		        	Please confirm your selection below.
		      	</div>
		      	<div class="modal-footer">
			       	<a href="#link" class="btn btn-secondary" role="button" data-dismiss="modal">Cancel</a>
			        <a class="btn btn-primary" role="button" href="http://php.scotchbox/registration_system/logout.php">Yes, log out</a>
		      	</div>
	    	</div>
	  	</div>
	</div>


	<section class="wrapper"> 
		<h1>Update Details</h1>
		<?php
			if($error === true) {
				echo '<ul>';
					foreach ($error_messages as $error_message) {
						echo '<li style="color:red">'.$error_message.'</li>';
					}
				echo '</ul>';
				}
			if($pwd_change_success === true) {
				echo '<p style="color:green">'.$pwd_change_success_message.'</p>';
			}
		?>

	<form action="updatedetails.php" method="post">
  		<div class="form-group">
    		<label for="password">New Password</label>
    		<input type="password" name="password" class="form-control" id="exampleInputPassword1" placeholder="Password" value="<?php if($error===true){echo $password_change;} ?>">
  		</div>
  		<input class="btn btn-primary btn-dark" type="submit" name="submit" value="Update Password">
	</form>
	</section>

	<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>

</body>
</html>

