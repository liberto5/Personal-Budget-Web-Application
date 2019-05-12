<?php

	session_start();
	
	if (!($_SESSION['logged_in'] == true))
	{
		header ('Location: index.php');
		exit();
	}
	
	else if ((isset($_SESSION['logged_in'])) && ($_SESSION['logged_in'] == true) && (isset($_POST['expense_amount'])))
	{
		// is validation successfull?
		$everything_OK=true;
		
		$expense_amount = $_POST['expense_amount'];
		$expense_date = $_POST['expense_date'];
		$payment_category = $_POST['payment_category'];
		$expense_category = $_POST['expense_category'];
		$expense_comment = $_POST['expense_comment'];
		
		// change separator if needed (comma into dot)
		if (strpos($expense_amount, ",") == true)
		{
		   $expense_amount = str_replace(",",".",$expense_amount);
		}
		
		if (!is_numeric($expense_amount) || $expense_amount < 0) 
		{
			$everything_OK = false;
			$_SESSION['e_expense_amount'] = "Amount has to be a positive number";
		}
		
		// check the date (not before 1970-01-01 and after current date)
		$current_date = date("Y-m-d");
		
		if ($expense_date < 1970-01-01 || $expense_date > $current_date) 
		{
			$everything_OK = false;
			$_SESSION['e_expense_date'] = "Enter the correct date";
		}
		
		// check the comment's length
		if ((strlen($expense_comment) > 100)) 
		{
			$everything_OK = false;
			$_SESSION['e_expense_comment'] = "The comment can not exceed 100 characters";
		}

		// Remember entered data
		$_SESSION['fr_expense_amount'] = $expense_amount;
		$_SESSION['fr_expense_date'] = $expense_date;
		$_SESSION['fr_payment_category'] = $payment_category;
		$_SESSION['fr_expense_category'] = $expense_category;
		$_SESSION['fr_expense_comment'] = $expense_comment;
		
		if ($everything_OK == true)
		{
			require_once "connect.php";
			mysqli_report(MYSQLI_REPORT_STRICT);
		
			try 
			{
				$connection = new mysqli ($host, $db_user, $db_password, $db_name);
				
				if ($connection->connect_errno!=0)
				{
					throw new Exception(mysqli_connect_errno());
				}
				
				else
				{
					$user_id = $_SESSION['id'];
					$getting_expense_category_id = $connection->query("SELECT id FROM expenses_category_assigned_to_users WHERE user_id = '$user_id' AND name = '$expense_category'");
					$row = $getting_expense_category_id->fetch_assoc();
					$expense_category_id = $row['id'];
					$getting_payment_category_id = $connection->query("SELECT id FROM payment_methods_assigned_to_users WHERE user_id = '$user_id' AND name = '$payment_category'");
					$row = $getting_payment_category_id->fetch_assoc();
					$payment_category_id = $row['id'];
					
					if ($connection->query("INSERT INTO expenses VALUES (NULL, '$user_id', '$expense_category_id', '$payment_category_id', '$expense_amount', '$expense_date', '$expense_comment')"))
					{
						$_SESSION['expense_successfully_added']=true;
						header('location: main-menu.php');
					}
					else
					{
						throw new Exception($connection->error);
					}
				}
				$connection->close();
			}
			catch(Exception $e)
			{
				echo '<span style="color:red;">Server error! Please try again later.</span>';
			}
		}
	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<title>Add Expense to Personal Budget</title>
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
		<h4>Add details of expense</h4>
		<form method="post">
			<div class="input-group mb-2">
				<div class="input-group-prepend w-25">
					<span class="input-group-text w-100 justify-content-center">Amount</span>
				</div>
				<input type="text" class="form-control" placeholder="0.00" value="<?php 
										if(isset($_SESSION['fr_expense_amount']))
										{
											echo $_SESSION['fr_expense_amount'];
											unset($_SESSION['fr_expense_amount']);
										}
										?>" name="expense_amount" />
			</div>
			<?php
				if(isset($_SESSION['e_expense_amount']))
				{
					echo '<span style="color:red;">'.$_SESSION['e_expense_amount'].'</span>';
					unset($_SESSION['e_expense_amount']);
				}
			?>
			<div class="input-group mb-2">
				<div class="input-group-prepend w-25">
					<span class="input-group-text w-100 justify-content-center">Date</span>
				</div>
				<input type="date" class="form-control" value="<?php 
										if(isset($_SESSION['fr_expense_date']))
										{
											echo $_SESSION['fr_expense_date'];
											unset($_SESSION['fr_expense_date']);
										}
										?>" name="expense_date" />	
			</div>
			<?php
				if(isset($_SESSION['e_expense_date']))
				{
					echo '<span style="color:red;">'.$_SESSION['e_expense_date'].'</span>';
					unset($_SESSION['e_expense_date']);
				}
			?>
			<div class="input-group mb-2">
				<div class="input-group-prepend w-25">
					<span class="input-group-text w-100 justify-content-center">Payment by</span>
				</div>
				<div class="dropdown flex-grow-1"  >
					<select class="dropdown h-100 w-100" name="payment_category">
						<?php 
						
							require_once "connect.php";
							mysqli_report(MYSQLI_REPORT_STRICT);
				
							try
							{
								$connection = new mysqli ($host, $db_user, $db_password, $db_name);
								
								if ($connection->connect_errno!=0)
								{
									throw new Exception(mysqli_connect_errno());
								}
								else
								{
									$user_id = $_SESSION['id'];

									if (!$result = $connection->query(sprintf("SELECT name FROM payment_methods_assigned_to_users WHERE user_id = '%s'", 
									mysqli_real_escape_string($connection, $user_id)))) 
									{
										throw new Exception($connection->error);
									}
									
									while ($row = $result->fetch_assoc())
									{
										echo "<option>" . $row['name'] . "</option>";
									}
									
									$result->close();
									$connection->close();
								}
							}
							catch (Exception $e)
							{
								echo '<span style="color=red;">Server error. Please try again later.</span>';
								//echo '<br />Detailed information: '.$e;
							}
						?>
					</select>
				</div>
			</div>
			<div class="input-group mb-2">
				<div class="input-group-prepend w-25">
					<span class="input-group-text w-100 justify-content-center">Category</span>
				</div>
				<div class="dropdown flex-grow-1"  >
					<select class="dropdown h-100 w-100" name="expense_category">
						<?php 
						
							require_once "connect.php";
							mysqli_report(MYSQLI_REPORT_STRICT);
				
							try
							{
								$connection = new mysqli ($host, $db_user, $db_password, $db_name);
								
								if ($connection->connect_errno!=0)
								{
									throw new Exception(mysqli_connect_errno());
								}
								else
								{
									$user_id = $_SESSION['id'];

									if (!$result = $connection->query(sprintf("SELECT name FROM expenses_category_assigned_to_users WHERE user_id = '%s'", 
									mysqli_real_escape_string($connection, $user_id)))) 
									{
										throw new Exception($connection->error);
									}
									
									while ($row = $result->fetch_assoc())
									{
										echo "<option>" . $row['name'] . "</option>";
									}
									
									$result->close();
									$connection->close();
								}
							}
							catch (Exception $e)
							{
								echo '<span style="color=red;">Server error. Please try again later.</span>';
								//echo '<br />Detailed information: '.$e;
							}
						?>
					</select>
				</div>
			</div>
			<div class="input-group mb-2">
				<div class="input-group-prepend w-25">
					<span class="input-group-text w-100 justify-content-center">Comment</span>
				</div>
				<textarea class="form-control" name="expense_comment" />
					<?php 
						if(isset($_SESSION['fr_expense_comment']))
						{
							echo $_SESSION['fr_expense_comment'];
							unset($_SESSION['fr_expense_comment']);
						}
					?>
				</textarea>
			</div>
			<?php
				if(isset($_SESSION['e_expense_comment']))
				{
					echo '<span style="color:red;">'.$_SESSION['e_expense_comment'].'</span>';
					unset($_SESSION['e_expense_comment']);
				}
			?>
			<div class="w-100 mt-2">
				<button type="submit" class="btn btn-primary mr-2">Add</button>
				<button type="submit" class="btn btn-primary" formaction="main-menu.php">Cancel</button>
			</div>
		</form>
	</div>
</body>
</html>
