<?php
// function to set up the formatting of html email
function sendEmail($to, $from, $subject, $message) {
	$headers = "From: ".$from."\r\n";
	$headers .= "Reply-To: ".$to."\r\n";
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/html;\r\n";

	$emailmessage = ' 
	<html>
	<head>
	<title>Reset Your Password</title>
	</head>
	<body>
	'.$message.'
	</body>
	</html>
	';

	return mail($to, $subject, $emailmessage, $headers);
}


?>