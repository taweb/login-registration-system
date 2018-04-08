
<?php
session_start();
// if user visits this url when they are not logged in, redirect them to the login page
if(!isset($_SESSION['logged_in'])) {
	header("Location:login.php");
	exit;
}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Profile</title>
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
	        		<a class="nav-link" href="http://php.scotchbox/registration_system/profile.php">Profile</a>
	      		</li>
	    		<li class="nav-item">
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
		<h1>Welcome</h1>
	</section>

	<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>

</body>
</html>