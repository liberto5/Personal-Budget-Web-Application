<?php

	session_start();
	
	if (!($_SESSION['logged_in'] == true))
	{
		header ('Location: index.php');
		exit();
	}
	
	else
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
				
				if ((!isset($_POST['periodsOptions']) || $_POST['periodsOptions'] == "current_month") && !isset($_POST['customize_period']))
				{
					
					if (!$_SESSION['result_incomes'] = $connection->query("SELECT cat.name, SUM(inc.amount) FROM incomes_category_assigned_to_users cat INNER JOIN incomes inc WHERE inc.income_category_assigned_to_user_id = cat.id AND date_of_income >=((EXTRACT(YEAR_MONTH FROM CURDATE())*100)+1) AND inc.user_id = '$user_id' GROUP BY cat.name ORDER BY SUM(inc.amount) DESC"))
					{
						throw new Exception($connection->error);
					}
					
					if (!$_SESSION['result_expenses'] = $connection->query("SELECT cat.name, SUM(exp.amount) FROM expenses_category_assigned_to_users cat INNER JOIN expenses exp WHERE exp.expense_category_assigned_to_user_id = cat.id AND date_of_expense >=((EXTRACT(YEAR_MONTH FROM CURDATE())*100)+1) AND exp.user_id = '$user_id' GROUP BY cat.name ORDER BY SUM(exp.amount) DESC"))
					{
						throw new Exception($connection->error);
					}
					
					if(!$_SESSION['total_income'] = $connection->query("SELECT SUM(inc.amount) FROM incomes_category_assigned_to_users cat INNER JOIN incomes inc WHERE inc.income_category_assigned_to_user_id = cat.id AND date_of_income >=((EXTRACT(YEAR_MONTH FROM CURDATE())*100)+1) AND inc.user_id = '$user_id'"))
					{
						throw new Exception($connection->error);
					}
					
					if(!$_SESSION['total_expense'] = $connection->query("SELECT SUM(exp.amount) FROM expenses_category_assigned_to_users cat INNER JOIN expenses exp WHERE exp.expense_category_assigned_to_user_id = cat.id AND date_of_expense >=((EXTRACT(YEAR_MONTH FROM CURDATE())*100)+1) AND exp.user_id = '$user_id'"))
					{
						throw new Exception($connection->error);
					}
				}
				
				if (isset($_POST['periodsOptions']) && $_POST['periodsOptions'] == "previous_month")
				{
				
					if (!$_SESSION['result_incomes'] = $connection->query("SELECT cat.name, SUM(inc.amount) FROM incomes_category_assigned_to_users cat INNER JOIN incomes inc WHERE inc.income_category_assigned_to_user_id = cat.id AND YEAR(date_of_income) = YEAR(CURDATE() - INTERVAL 1 MONTH) AND MONTH(date_of_income) = MONTH(CURDATE() - INTERVAL 1 MONTH) AND inc.user_id = '$user_id' GROUP BY cat.name ORDER BY SUM(inc.amount) DESC"))
					{
						throw new Exception($connection->error);
					}
					
					if (!$_SESSION['result_expenses'] = $connection->query("SELECT cat.name, SUM(exp.amount) FROM expenses_category_assigned_to_users cat INNER JOIN expenses exp WHERE exp.expense_category_assigned_to_user_id = cat.id AND YEAR(date_of_expense) = YEAR(CURDATE() - INTERVAL 1 MONTH) AND MONTH(date_of_expense) = MONTH(CURDATE() - INTERVAL 1 MONTH) AND exp.user_id = '$user_id' GROUP BY cat.name ORDER BY SUM(exp.amount) DESC"))
					{
						throw new Exception($connection->error);
					}
					
					if(!$_SESSION['total_income'] = $connection->query("SELECT SUM(inc.amount) FROM incomes_category_assigned_to_users cat INNER JOIN incomes inc WHERE inc.income_category_assigned_to_user_id = cat.id AND YEAR(date_of_income) = YEAR(CURDATE() - INTERVAL 1 MONTH) AND MONTH(date_of_income) = MONTH(CURDATE() - INTERVAL 1 MONTH) AND inc.user_id = '$user_id'"))
					{
						throw new Exception($connection->error);
					}
					
					if(!$_SESSION['total_expense'] = $connection->query("SELECT SUM(exp.amount) FROM expenses_category_assigned_to_users cat INNER JOIN expenses exp WHERE exp.expense_category_assigned_to_user_id = cat.id AND YEAR(date_of_expense) = YEAR(CURDATE() - INTERVAL 1 MONTH) AND MONTH(date_of_expense) = MONTH(CURDATE() - INTERVAL 1 MONTH) AND exp.user_id = '$user_id'"))
					{
						throw new Exception($connection->error);
					}
				}
				
				if (isset($_POST['periodsOptions']) && $_POST['periodsOptions'] == "current_year")
				{
				
					if (!$_SESSION['result_incomes'] = $connection->query("SELECT cat.name, SUM(inc.amount) FROM incomes_category_assigned_to_users cat INNER JOIN incomes inc WHERE inc.income_category_assigned_to_user_id = cat.id AND YEAR(date_of_income) = YEAR(CURDATE()) AND inc.user_id = '$user_id' GROUP BY cat.name ORDER BY SUM(inc.amount) DESC"))
					{
						throw new Exception($connection->error);
					}
					
					if (!$_SESSION['result_expenses'] = $connection->query("SELECT cat.name, SUM(exp.amount) FROM expenses_category_assigned_to_users cat INNER JOIN expenses exp WHERE exp.expense_category_assigned_to_user_id = cat.id AND YEAR(date_of_expense) = YEAR(CURDATE()) AND exp.user_id = '$user_id' GROUP BY cat.name ORDER BY SUM(exp.amount) DESC"))
					{
						throw new Exception($connection->error);
					}
					
					if(!$_SESSION['total_income'] = $connection->query("SELECT SUM(inc.amount) FROM incomes_category_assigned_to_users cat INNER JOIN incomes inc WHERE inc.income_category_assigned_to_user_id = cat.id AND YEAR(date_of_income) = YEAR(CURDATE()) AND inc.user_id = '$user_id'"))
					{
						throw new Exception($connection->error);
					}
					
					if(!$_SESSION['total_expense'] = $connection->query("SELECT SUM(exp.amount) FROM expenses_category_assigned_to_users cat INNER JOIN expenses exp WHERE exp.expense_category_assigned_to_user_id = cat.id AND YEAR(date_of_expense) = YEAR(CURDATE()) AND exp.user_id = '$user_id'"))
					{
						throw new Exception($connection->error);
					}
				}
				
				if (isset($_POST['custom_start']) && isset($_POST['custom_end']))
				{
					$custom_start = $_POST['custom_start'];
					$custom_end = $_POST['custom_end'];
					
					if (!$_SESSION['result_incomes'] = $connection->query("SELECT cat.name, SUM(inc.amount) FROM incomes_category_assigned_to_users cat INNER JOIN incomes inc WHERE inc.income_category_assigned_to_user_id = cat.id AND date_of_income BETWEEN '$custom_start' AND '$custom_end' AND inc.user_id = '$user_id' GROUP BY cat.name ORDER BY SUM(inc.amount) DESC"))
					{
						throw new Exception($connection->error);
					}
					
					if (!$_SESSION['result_expenses'] = $connection->query("SELECT cat.name, SUM(exp.amount) FROM expenses_category_assigned_to_users cat INNER JOIN expenses exp WHERE exp.expense_category_assigned_to_user_id = cat.id AND date_of_expense BETWEEN '$custom_start' AND '$custom_end' AND exp.user_id = '$user_id' GROUP BY cat.name ORDER BY SUM(exp.amount) DESC"))
					{
						throw new Exception($connection->error);
					}
					
					if(!$_SESSION['total_income'] = $connection->query("SELECT SUM(inc.amount) FROM incomes_category_assigned_to_users cat INNER JOIN incomes inc WHERE inc.income_category_assigned_to_user_id = cat.id AND date_of_income BETWEEN '$custom_start' AND '$custom_end' AND inc.user_id = '$user_id'"))
					{
						throw new Exception($connection->error);
					}
					
					if(!$_SESSION['total_expense'] = $connection->query("SELECT SUM(exp.amount) FROM expenses_category_assigned_to_users cat INNER JOIN expenses exp WHERE exp.expense_category_assigned_to_user_id = cat.id AND date_of_expense BETWEEN '$custom_start' AND '$custom_end' AND exp.user_id = '$user_id'"))
					{
						throw new Exception($connection->error);
					}
				}
			}
			$connection->close();
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
	<title>Balance in Personal Budget</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
	<link rel="stylesheet" href="style.css" type="text/css"/>
	<link href="https://fonts.googleapis.com/css?family=Anton|Pacifico&amp;subset=latin-ext" rel="stylesheet">

	<script>
	window.onload = function() 
	{
		
		var chart_with_incomes = new CanvasJS.Chart("chartIncomesContainer", 
			{
				animationEnabled: true,
				title: 
				{
					text: "Summary of your incomes"
				},
				data: [
				{
					type: "pie",
					startAngle: 270,
					yValueFormatString: "##0.00\"\"",
					indexLabel: "{label} {y}",
					dataPoints: 
					[
						<?php
							while ($row_with_incomes = $_SESSION['result_incomes']->fetch_assoc())
							{
								$amount = $row_with_incomes['SUM(inc.amount)'];
								$name = $row_with_incomes['name'];
								echo "{y: " . $amount . ", label: \" $name \"},";
							}
							$_SESSION['result_incomes']->data_seek(0); 
						?>
					]
				}]
			});
			
		chart_with_incomes.render();
		
		var chart_with_expenses = new CanvasJS.Chart("chartExpensesContainer", 
			{
				animationEnabled: true,
				title: 
				{
					text: "Summary of your expenses"
				},
				data: [
				{
					
					type: "pie",
					startAngle: 270,
					yValueFormatString: "##0.00\"\"",
					indexLabel: "{label} {y}",
					dataPoints: 
					[
						<?php
							while ($row_with_expenses = $_SESSION['result_expenses']->fetch_assoc())
							{
								$amount = $row_with_expenses['SUM(exp.amount)'];
								$name = $row_with_expenses['name'];
								echo "{y: " . $amount . ", label: \" $name \"},";
							}
							$_SESSION['result_expenses']->data_seek(0); 
						?>
					]
				}]
			});
		chart_with_expenses.render();
	}
	</script>
</head>
<body> 
	<div class="container bg-white text-center col-12 col-lg-10 col-xl-8 mt-lg-5 p-1 p-lg-3 shadow">
		<h1>Personal Budget</h1>
		<p class="heading">Take control of your finances</p>
		
		<fieldset class="border">
		
			<legend class="border bg-light"> Your finances </legend>
			
			<form method="post">
				<div class="input-group mb-2 w-75 mx-auto">
					<div class="input-group-prepend w-50">
						<span class="input-group-text w-100 justify-content-center">Select period of time</span>
					</div>
					<select id="periodsOptions" name="periodsOptions" class="w-50" onchange="if(this.options[this.selectedIndex].value!='custom'){ this.form.submit(); }">
						<option value="current_month" <?php 
										if(!isset($_POST['periodsOptions']) || $_POST['periodsOptions'] == "current_month")
										{
											echo 'selected';
										}
										else
										{
											echo '';
										}
										?>>Current month</option>
						<option value="previous_month" <?php 
										if(isset($_POST['periodsOptions']) && $_POST['periodsOptions'] == "previous_month")
										{
											echo 'selected';
										}
										else
										{
											echo '';
										}
										?>>Previous month</option>
						<option value="current_year" <?php 
										if(isset($_POST['periodsOptions']) && $_POST['periodsOptions'] == "current_year")
										{
											echo 'selected';
										}
										else
										{
											echo '';
										}
										?>>Current year</option>
						<option value="custom" <?php 
										if(isset($_POST['customize_period']) && $_POST['customize_period'] == "OK")
										{
											echo 'selected';
										}
										else
										{
											echo '';
										}
										?>>Custom</option>
					</select>	
				</div>
			</form>

			<script>			
			$("#periodsOptions").on("change", function () {        
				$modal = $('#myModal');
				if($(this).val() === 'custom'){
					$modal.modal('show');
				}
			});
			</script>

			<div class="modal fade" id="myModal">
				<div class="modal-dialog">
					<div class="modal-content">
						<form method="post">
							<!-- Modal Header -->
							<div class="modal-header">
								<h4 class="modal-title">Select custom period of time: </h4>
								<button type="button" class="close" data-dismiss="modal">&times;</button>
							</div>
						
							<!-- Modal body -->
							<div class="modal-body">
								<div class="input-group mb-2 w-75">
									<div class="input-group-prepend w-25">
										<span class="input-group-text w-100 justify-content-center">Start:</span>
									</div>
									<input type="date" name="custom_start" class="form-control" required>
								</div>
								<div class="input-group mb-2 w-75">
									<div class="input-group-prepend w-25">
										<span class="input-group-text w-100 justify-content-center">End:</span>
									</div>
									<input type="date" name="custom_end" class="form-control" required>
								</div>
							</div>
						
							<!-- Modal footer -->
							<div class="modal-footer">
							<input type="submit" class="btn btn-info" name="customize_period" value="OK">
							<button type="button" class="btn btn-info" data-dismiss="modal">Cancel</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		
			<?php
			
				$_SESSION['total_income']->data_seek(0);
				$row_with_total_income = $_SESSION['total_income']->fetch_assoc();
				$_SESSION['sum_of_income'] = $row_with_total_income['SUM(inc.amount)'];
				$_SESSION['total_expense']->data_seek(0);
				$row_with_total_expense = $_SESSION['total_expense']->fetch_assoc();
				$_SESSION['sum_of_expense'] = $row_with_total_expense['SUM(exp.amount)'];
				$balance = $_SESSION['sum_of_income'] - $_SESSION['sum_of_expense'];
				
				if($balance < 0)
				{
					echo '<div class="bg-danger py-2 px-4" id="summary">';
					echo '<h5 class="font-weight-bold">Total balance: ' . $balance . ' EUR</h5>';
					echo '<h5 class="font-weight-bold">Be careful, you run into debt!</h5>';
					echo '</div>';
				}
				
				else
				{
					echo '<div class="bg-success py-2 px-4" id="summary">';
					echo '<h5 class="font-weight-bold">Total balance: ' . $balance . ' EUR</h5>';
					echo '<h5 class="font-weight-bold">Congratulations! You manage your finances very well!</h5>';
					echo '</div>';
				}
			
			?>
			
			<a href="main-menu.php"><button type="button" class="btn btn-primary">Back to main menu</button></a>
			
			<fieldset class="border m-3">
			
				<legend class="border"> Your incomes </legend>
			  
				<div class="d-md-flex justify-content-around">
					<table class="table table-striped m-2 col-11 col-md-6">
						<thead>
							<tr>
								<th>Category</th>
								<th>Amount</th>
							</tr>
						</thead>
						<tbody>
							<?php
								
								while ($row_with_incomes = $_SESSION['result_incomes']->fetch_assoc())
								{
									echo "<tr><td>" . $row_with_incomes['name'] . "</td><td>" . $row_with_incomes['SUM(inc.amount)'] . "</td></tr>";
									$_SESSION['name']=$row_with_incomes['name'];
									$_SESSION['amount']=$row_with_incomes['SUM(inc.amount)'];
								}
								$_SESSION['result_incomes']->data_seek(0);
							?>
						<thead>
							 <tr>
								<th>Total</th>
								<?php										
									$sum_of_income = $_SESSION['sum_of_income'];
									echo "<th>" . $sum_of_income . "</th>";
								?>	
							</tr>
						</thead>
						</tbody>
					</table>
									
					<?php 
						if($_SESSION['result_incomes']->num_rows > 0)
						{
							echo '<div id="chartIncomesContainer" class="col-11 col-md-7">';
							echo '<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>';
							echo '</div>';
						}
						else
						{
							echo '<span style="color:blue;"><br />You have no incomes in selected period of time</span>';
						}
						$_SESSION['result_incomes']->data_seek(0);
					?>
				
				</div>	
			</fieldset>
		
			<fieldset class="border m-3">
			
				<legend class="border"> Your expenses </legend>
			  
				<div class="d-md-flex justify-content-around">
					<table class="table table-striped m-2 col-11 col-md-6">
						<thead>
							<tr>
								<th>Category</th>
								<th>Amount</th>
							</tr>
						</thead>
						<tbody>
							<?php
								while ($row_with_expenses = $_SESSION['result_expenses']->fetch_assoc())
								{
									echo "<tr><td>" . $row_with_expenses['name'] . "</td><td>" . $row_with_expenses['SUM(exp.amount)'] . "</td></tr>";
									$_SESSION['name']=$row_with_expenses['name'];
									$_SESSION['amount']=$row_with_expenses['SUM(exp.amount)'];
								}
								$_SESSION['result_expenses']->data_seek(0);
							?>
						<thead>
							 <tr>
								<th>Total</th>
								<?php										
									$sum_of_expense = $_SESSION['sum_of_expense'];
									echo "<th>" . $sum_of_expense . "</th>";
								?>
							</tr>
						</thead>
						</tbody>
					</table>
									
					<?php
						if($_SESSION['result_expenses']->num_rows > 0)
						{
							echo '<div id="chartExpensesContainer" class="col-11 col-md-7">';
							echo '<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>';
							echo '</div>';
						}
						else
						{
							echo '<span style="color:blue;"><br />You have no expenses in selected period of time</span>';
						}
						$_SESSION['result_expenses']->data_seek(0);
					?>	
				</div>	
			</fieldset>
		</fieldset>	
	</div>
</body>
</html>