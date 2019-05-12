<?php

	session_start();
	
	if (!($_SESSION['logged_in']==true))
	{
		header ('Location: index.php');
		exit();
	}
	
	// Unset variables after unsuccessful income adding
	if (isset($_SESSION['fr_income_amount'])) unset($_SESSION['fr_income_amount']);
	if (isset($_SESSION['fr_income_date'])) unset($_SESSION['fr_income_date']);
	if (isset($_SESSION['fr_income_category'])) unset($_SESSION['fr_income_category']);
	if (isset($_SESSION['fr_income_comment'])) unset($_SESSION['fr_income_comment']);
	
	// unset errors during income adding
	if (isset($_SESSION['e_income_amount'])) unset($_SESSION['e_income_amount']);
	if (isset($_SESSION['e_income_date'])) unset($_SESSION['e_income_date']);
	if (isset($_SESSION['e_income_comment'])) unset($_SESSION['e_income_comment']);
	
	// Unset variables after unsuccessful expense adding
	if (isset($_SESSION['fr_expense_amount'])) unset($_SESSION['fr_expense_amount']);
	if (isset($_SESSION['fr_expense_date'])) unset($_SESSION['fr_expense_date']);
	if (isset($_SESSION['fr_payment_category'])) unset($_SESSION['fr_payment_category']);
	if (isset($_SESSION['fr_expense_category'])) unset($_SESSION['fr_expense_category']);
	if (isset($_SESSION['fr_expense_comment'])) unset($_SESSION['fr_expense_comment']);
	
	// unset errors during income adding
	if (isset($_SESSION['e_expense_amount'])) unset($_SESSION['e_expense_amount']);
	if (isset($_SESSION['e_expense_date'])) unset($_SESSION['e_expense_date']);
	if (isset($_SESSION['e_expense_comment'])) unset($_SESSION['e_expense_comment']);
	
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
		<h4><?php echo $_SESSION['username'] ?>, what you're gonna do?</h4>
		<div class="mt-3" id="main-menu">
			<?php
				if(isset($_SESSION['income_successfully_added']) && $_SESSION['income_successfully_added'] == true)
					{
						echo '<span style="color:blue;"><i>Income added successfully</i><br /><br /></span>';
						unset ($_SESSION['income_successfully_added']);
					}
				
				if(isset($_SESSION['expense_successfully_added']) && $_SESSION['expense_successfully_added'] == true)
					{
						echo '<span style="color:blue;"><i>Expense added successfully</i><br /><br /></span>';
						unset ($_SESSION['expense_successfully_added']);
					}
			?>
			<a href="add-income.php"><button type="button" class="btn btn-primary btn-block">Add income</button></a>
			<a href="add-expense.php"><button type="button" class="btn btn-primary btn-block">Add expense</button></a>
			<a href="show-balance.php"><button type="button" class="btn btn-primary btn-block">Show balance</button></a>
			<button type="button" class="btn btn-primary btn-block">Settings</button>
			<a href="logout.php"><button type="button" class="btn btn-primary btn-block">Logout</button></a>
		</div>
	</div>
</body>
</html>