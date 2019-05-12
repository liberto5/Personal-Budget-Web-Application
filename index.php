<?php

	session_start();
	
	if ((isset($_SESSION['logged_in'])) && ($_SESSION['logged_in'] == true))
	{
		header ('Location: main-menu.php');
		exit();
	}
	
	else if ((isset($_POST['login'])) && (isset($_POST['password'])))
	{
		require_once "connect.php";
		mysqli_report(MYSQLI_REPORT_STRICT);
		
		try
		{
			$connection = new mysqli ($host, $db_user, $db_password, $db_name);
			if ($connection->connect_errno != 0)
			{
				throw new Exception(mysqli_connect_errno());
			}
			else
			{
				$login = $_POST['login'];
				$password = $_POST['password'];
				
				$login = htmlentities($login, ENT_QUOTES, "UTF-8");
				
				if (!$result = $connection->query(sprintf("SELECT * FROM users WHERE email = '%s'", 
				mysqli_real_escape_string($connection, $login)))) 
				{
					throw new Exception($connection->error);
				}
				
				$how_many_records = $result->num_rows;
				if ($how_many_records>0)
				{
					$row = $result->fetch_assoc();
					
					if (password_verify($password, $row['password']))
					{
						// Save user's data in session
						$_SESSION['logged_in'] = true;
						$_SESSION['id'] = $row['id'];
						$_SESSION['username'] = $row['username'];
						$_SESSION['email'] = $row['email'];
						unset($_SESSION['e_login']);				
						$result->close();				
						header('Location: main-menu.php');
					}
					else
					{
						$_SESSION['e_login'] = '<span style = "color:red">Incorrect e-mail or password!</span>';
						//Remember entered login
						$_SESSION['fr_login'] = $login;
					}
				}
				else
				{
					$_SESSION['e_login'] = '<span style = "color:red">Incorrect e-mail or password!</span>';
					//Remember entered login
					$_SESSION['fr_login'] = $login;
				}
				
				$connection->close();
			}
		}
		catch (Exception $e)
		{
			echo '<span style="color=red;">Server error. Please try again later.</span>';
			//echo '<br />Detailed information: '.$e;
		}
	}
	
	else if ((isset($_POST['name'])) && (isset($_POST['email'])) && (isset($_POST['password1'])) && (isset($_POST['password2'])))
	{
		// is validation successfull?
		$everything_OK = true;
		
		// check name
		$name = $_POST['name'];
		
		// check name's length
		if ((strlen($name)<3) || (strlen($name)>20))
		{
			$everything_OK = false;
			$_SESSION['e_name']="Name has to consist of 3 to 20 characters";
		}
		
		if (ctype_alnum($name) == false)
		{
			$everything_OK = false;
			$_SESSION['e_name']="Name has to consist only of alphanumeric characters";
		}

		// check email
		$email = $_POST['email'];
		$emailB = filter_var($email, FILTER_SANITIZE_EMAIL);
		if ((filter_var($emailB, FILTER_VALIDATE_EMAIL) == false) || ($emailB != $email))
		{
			$everything_OK = false;
			$_SESSION['e_email'] = "Enter the correct e-mail address";
		}
		
		// check passwords
		$password1 = $_POST['password1'];
		$password2 = $_POST['password2'];
		
		if ((strlen($password1) < 8) || (strlen($password1) > 20))
		{
			$everything_OK = false;
			$_SESSION['e_password']="Password has to consist of 8 to 20 characters";
		}
		
		if ($password1 != $password2)
		{
			$everything_OK = false;
			$_SESSION['e_password']="Passwords are not the same";
		}
		
		$password_hash = password_hash($password1, PASSWORD_DEFAULT);
		
		// Remember entered data
		$_SESSION['fr_name'] = $name;
		$_SESSION['fr_email'] = $email;
		
		require_once "connect.php";
		mysqli_report(MYSQLI_REPORT_STRICT);
		
		try 
		{
			$connection = new mysqli ($host, $db_user, $db_password, $db_name);
			if ($connection->connect_errno != 0)
			{
				throw new Exception(mysqli_connect_errno());
			}
			else
			{
				// does email exist in database?
				if (!$result = $connection->query("SELECT id FROM users WHERE email = '$email'")) 
				{
					throw new Exception($connection->error);
				}
				
				$how_many_mails = $result->num_rows;
				$result->close();
				
				if($how_many_mails > 0)
				{
					$everything_OK = false;
					$_SESSION['e_email']="The account registered with this e-mail address already exists";
				}			
				
				if ($everything_OK == true)
				{
					$connection->query("START TRANSACTION");
					
					$adding_user = $connection->query("INSERT INTO users VALUES (NULL, '$name', '$password_hash', '$email')");
					$getting_users_id = $connection->query("SELECT id FROM users WHERE email = '$email'");
					$row = $getting_users_id->fetch_assoc();
					$user_id = $row['id'];
					$adding_standard_payment_methods = $connection->query("INSERT INTO payment_methods_assigned_to_users (name) SELECT name FROM payment_methods_default");
					$adding_standard_incomes_categories = $connection->query("INSERT INTO incomes_category_assigned_to_users (name) SELECT name FROM incomes_category_default");
					$adding_standard_expenses_categories = $connection->query("INSERT INTO expenses_category_assigned_to_users (name) SELECT name FROM expenses_category_default");
					$adding_user_id_to_payment_methods = $connection->query("UPDATE payment_methods_assigned_to_users SET user_id = '$user_id' WHERE user_id = 0");
					$adding_user_id_to_incomes_categories = $connection->query("UPDATE incomes_category_assigned_to_users SET user_id = '$user_id' WHERE user_id = 0");
					$adding_user_id_to_expenses_categories = $connection->query("UPDATE expenses_category_assigned_to_users SET user_id = '$user_id' WHERE user_id = 0");
					
					if ($adding_user && $adding_standard_payment_methods && $adding_standard_incomes_categories && $adding_standard_expenses_categories && $adding_user_id_to_payment_methods && $adding_user_id_to_incomes_categories && $adding_user_id_to_expenses_categories)
					{
						$connection->query("COMMIT");
						$_SESSION['successful_registration']=true;
						header('location: welcome.php');
					}
					else
					{
						$connection->query("ROLLBACK");
						throw new Exception($connection->error);
					}
				}
			
				$connection->close();
			}
		}
		catch(Exception $e)
		{
			echo '<span style="color:red;">Server error! Please try again later.</span>';
		}
	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<title>Welcome to Personal Budget</title>
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
			<div id="accordion">
				<div class="card">
					<a class="card-link " data-toggle="collapse" href="#signIn">
						<div class="card-header">
							Sign in
						</div>
					</a>
					<form method="post">
						<div id="signIn" class="collapse <?php 
										if(isset($_SESSION['fr_name']))
										{
											echo '';
										}
										else
										{
											echo 'show';
										}
										?>" data-parent="#accordion">
							<div class="card-body">
								<div class="p-1">
									<input type="email" class="form-control form-control" placeholder="E-mail address" name="login" value="<?php 
										if(isset($_SESSION['fr_login']))
										{
											echo $_SESSION['fr_login'];
											unset($_SESSION['fr_login']);
										}
										?>">
								</div>
								<div class="p-1">
									<input type="password" class="form-control form-control" placeholder="Password" name="password">
								</div>
								<?php
									if (isset($_SESSION['e_login']))
									{
										echo '<div class="error">'.$_SESSION['e_login'].'</div>';
										unset($_SESSION['e_login']);
									}
								?>
								<div class="p-1">
									<button type="submit" class="btn btn-primary">Sign in</button>
								</div>
							</div>
						</div>
					</form>
				</div>
				<div class="card">
					<a class="collapsed card-link" data-toggle="collapse" href="#signUp">
						<div class="card-header">
							Sign up
						</div>
					</a>
					<form method="post">
						<div id="signUp" class="collapse <?php 
										if(isset($_SESSION['fr_name']))
										{
											echo 'show';
										}
										else
										{
											echo '';
										}
										?>" data-parent="#accordion">
							<div class="card-body">
								<div class="p-1">
									<input type="text" class="form-control form-control" value="<?php 
										if(isset($_SESSION['fr_name']))
										{
											echo $_SESSION['fr_name'];
											unset($_SESSION['fr_name']);
										}
										?>" name="name" placeholder="Name" />
										<?php
											if(isset($_SESSION['e_name']))
											{
												echo '<span style="color:red;">'.$_SESSION['e_name'].'</span>';
												unset($_SESSION['e_name']);
											}
										?>
								</div>
								<div class="p-1">
									<input type="text" class="form-control form-control" value="<?php 
										if(isset($_SESSION['fr_email']))
										{
											echo $_SESSION['fr_email'];
											unset($_SESSION['fr_email']);
										}
										?>" name="email" placeholder="E-mail address" />
										<?php
											if(isset($_SESSION['e_email']))
											{
												echo '<span style="color:red;">'.$_SESSION['e_email'].'</span>';
												unset($_SESSION['e_email']);
											}
										?>
								</div>
								<div class="p-1">
									<input type="password" class="form-control form-control" name="password1" placeholder="Password" />
										<?php
											if(isset($_SESSION['e_password']))
											{
												echo '<span style="color:red;">'.$_SESSION['e_password'].'</span>';
												unset($_SESSION['e_password']);
											}
										?>
								</div>
								<div class="p-1">
									<input type="password" class="form-control form-control" name="password2" placeholder="Confirm password" />
								</div>
								<div class="p-1">
									<button type="submit" class="btn btn-primary">Sign up</button>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
	</div>
</body>
</html>
