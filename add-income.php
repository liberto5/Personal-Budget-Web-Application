<?php

	session_start();
	
	if (!($_SESSION['logged_in'] == true))
	{
		header ('Location: index.php');
		exit();
	}
	
	else if ((isset($_SESSION['logged_in'])) && ($_SESSION['logged_in'] == true) && (isset($_POST['income_amount'])))
	{
		// is validation successfull?
		$everything_OK=true;
		
		$income_amount = $_POST['income_amount'];
		$income_date = $_POST['income_date'];
		$income_category = $_POST['income_category'];
		$income_comment = $_POST['income_comment'];
		
		// change separator if needed (comma into dot)
		if (strpos($income_amount, ",") == true)
		{
		   $income_amount = str_replace(",",".",$income_amount);
		}
		
		if (!is_numeric($income_amount) || $income_amount < 0) 
		{
			$everything_OK = false;
			$_SESSION['e_income_amount'] = "Amount has to be a positive number";
		}
		
		// check the date (not before 1970-01-01 and after current date)
		$current_date = date("Y-m-d");
		
		if ($income_date < 1970-01-01 || $income_date > $current_date) 
		{
			$everything_OK = false;
			$_SESSION['e_income_date'] = "Enter the correct date";
		}
		
		// check the comment's length
		if ((strlen($income_comment) > 100)) 
		{
			$everything_OK = false;
			$_SESSION['e_income_comment'] = "The comment can not exceed 100 characters";
		}

		// Remember entered data
		$_SESSION['fr_income_amount'] = $income_amount;
		$_SESSION['fr_income_date'] = $income_date;
		$_SESSION['fr_income_category'] = $income_category;
		$_SESSION['fr_income_comment'] = $income_comment;
		
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
					$getting_income_category_id = $connection->query("SELECT id FROM incomes_category_assigned_to_users WHERE user_id = '$user_id' AND name = '$income_category'");
					$row = $getting_income_category_id->fetch_assoc();
					$income_category_id = $row['id'];
					
					if ($connection->query("INSERT INTO incomes VALUES (NULL, '$user_id', '$income_category_id', '$income_amount', '$income_date', '$income_comment')"))
					{
						$_SESSION['income_successfully_added']=true;
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
	<title>Add Income to Personal Budget</title>
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
		<h4>Add details of income</h4>
		<form method="post">
			<div class="input-group mb-2">
				<div class="input-group-prepend w-25">
					<span class="input-group-text w-100 justify-content-center">Amount</span>
				</div>
				<input type="text" class="form-control" placeholder="0.00" value="<?php 
										if(isset($_SESSION['fr_income_amount']))
										{
											echo $_SESSION['fr_income_amount'];
											unset($_SESSION['fr_income_amount']);
										}
										?>" name="income_amount" />									
			</div>
			<?php
				if(isset($_SESSION['e_income_amount']))
				{
					echo '<span style="color:red;">'.$_SESSION['e_income_amount'].'</span>';
					unset($_SESSION['e_income_amount']);
				}
			?>
			<div class="input-group mb-2">
				<div class="input-group-prepend w-25">
					<span class="input-group-text w-100 justify-content-center">Date</span>
				</div>
				<input type="date" class="form-control" value="<?php 
										if(isset($_SESSION['fr_income_date']))
										{
											echo $_SESSION['fr_income_date'];
											unset($_SESSION['fr_income_date']);
										}
										?>" name="income_date" />		
			</div>
			<?php
				if(isset($_SESSION['e_income_date']))
				{
					echo '<span style="color:red;">'.$_SESSION['e_income_date'].'</span>';
					unset($_SESSION['e_income_date']);
				}
			?>
			<div class="input-group mb-2">
				<div class="input-group-prepend w-25">
					<span class="input-group-text w-100 justify-content-center">Category</span>
				</div>
				<div class="dropdown flex-grow-1">
					<select class="dropdown h-100 w-100" name="income_category">
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

									if (!$result = $connection->query(sprintf("SELECT name FROM incomes_category_assigned_to_users WHERE user_id = '%s'", 
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
				<textarea class="form-control" name="income_comment" />
					<?php 
						if(isset($_SESSION['fr_income_comment']))
						{
							echo $_SESSION['fr_income_comment'];
							unset($_SESSION['fr_income_comment']);
						}
					?>
				</textarea>
			</div>
			<?php
				if(isset($_SESSION['e_income_comment']))
				{
					echo '<span style="color:red;">'.$_SESSION['e_income_comment'].'</span>';
					unset($_SESSION['e_income_comment']);
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
