<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<title>New Dashboard</title>
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
		<script type="text/javascript" src="javascript/main.js"></script>
		<link type="text/css" rel="stylesheet" href="css/main.css" />
	</head>
	<body>
		<div id="wrapper">
			<h1 class="arial center">The Dashboard</h1>	
			<?php
				include ('includes/navigation.php');
			?>
			
			<div id="main">
				<!-- CUSTOMER SECTION -->
				<div id="formElements">
					<!-- <div id="customerSelection" class="section">
					    <h2>Customer Selection</h2>
						<div>
							<span class="formLabel">Customer:</span>
							<select>
								<option>Nothing yet</option>
								<option>Nothing yet</option>
								<option>Nothing yet</option>
								<option>Nothing yet</option>
								<option>Nothing yet</option>
								<option>Nothing yet</option>
							</select>
							<div id="customerId">Id: <span id="idNumber">3254</span></div>
						</div>
						<div>
							<span class="formLabel">School Locations:</span>
							<select>
								<option>Nothing yet</option>
								<option>Nothing yet</option>
								<option>Nothing yet</option>
								<option>Nothing yet</option>
								<option>Nothing yet</option>
							</select>
						</div>
					</div>
					
					<div id="viewOptions" class="section">
						<h2>View Options</h2>
					</div>
					
					<div id="createReports" class="section">
						<h2>Create Reports</h2> 
					</div> -->
					<div id="createReports" class="section">
						<h2>Create Reports</h2>
						<input type="button" id="getReportbtn" report="BiweeklyMil" name="getReportbtn" value="Get Report" />
						<!-- <input type="button" id="getExcelbtn" name="getExcelbtn" value="Get Excel" /> -->
						<input type="button" id="cancelbtn" name="cancelbtn" value="Cancel" />
					</div>
				</div>
			</div>
			<div class="clear"></div>
		</div>
	</body>
</html>