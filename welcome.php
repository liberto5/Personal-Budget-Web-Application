<?php

	session_start();
	if (!isset($_SESSION['successful_registration']))
	{
		header ('Location: index.php');
		exit();
	}
	else
	{
		unset($_SESSION['successful_registration']);
	}
	// Unset variables after unsuccessful validation
	if (isset($_SESSION['fr_name'])) unset($_SESSION['fr_name']);
	if (isset($_SESSION['fr_email'])) unset($_SESSION['fr_email']);
	if (isset($_SESSION['fr_password1'])) unset($_SESSION['fr_password1']);
	if (isset($_SESSION['fr_password2'])) unset($_SESSION['fr_password2']);
	
	// unset regiastration errors
	if (isset($_SESSION['e_name'])) unset($_SESSION['e_name']);
	if (isset($_SESSION['e_email'])) unset($_SESSION['e_email']);
	if (isset($_SESSION['e_password'])) unset($_SESSION['e_password']);

?>

<!DOCTYPE HTML>
<html lang="en">
<head>
	<title>Personal Budget</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
	<link rel="stylesheet" href="style.css" type="text/css"/>
	<link href="https://fonts.googleapis.com/css?family=Anton|Pacifico&amp;subset=latin-ext" rel="stylesheet">
</head>
<body> 
	<div class="container bg-white text-center col-12 col-sm-8 col-md-6 col-lg-5 col-xl-4 mt-sm-5 p-1 p-sm-3 shadow">
		<h1>Personal Budget</h1>
		<p class="heading">Take control of your finances</p>
		
		Thank you for registering. Now you can sign in!<br /><br />
		<a href="index.php">Sign in!</a>
		<br /><br />

	</div>
</body>
</html>