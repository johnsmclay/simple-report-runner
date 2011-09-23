<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<title>New Dashboard</title>
		<?$this->load->view('dependencies/source_links'); ?>
	</head>
	<body>
		<div id="wrapper">
			<h1 class="arial center">The Dashboard</h1>	
			<?
				$this->load->view('dependencies/navigation_menu');
			?>			
			<div id="main">
				<!-- CUSTOMER SECTION -->
				<div id="formElements">
					<div id="customerSelection" class="section">
						<h2>Customer Selection</h2>
						<div>
							<span class="formLabel">Customer:</span>
							<select id="schoolList">
								<option value="0">All</option>
							</select>
							<div id="customerId">Id: <span id="idNumber"></span></div>
							<img id="schoolLoader" class="hideLoader" src="<?=base_url();?>assets/images/ajax-loader.gif" alt="loader gif" />
						</div>
						<div>
							<span class="formLabel">School Locations:</span>
							<select id="subSchoolList">
								<option>Please select a customer</option>
							</select>
							<img id="subSchoolLoader" class="hideLoader" src="<?=base_url();?>assets/images/ajax-loader.gif" alt="loader gif" />
						</div>
					</div>
					
					<div id="viewOptions" class="section">
						<h2>View Options</h2>
						<div>
							<span class="formLabel">Report Grouping - Primary: </span>
							<select>
								<option>Nothing yet</option>
								<option>Nothing yet</option>
								<option>Nothing yet</option>
								<option>Nothing yet</option>
								<option>Nothing yet</option>
							</select>
						</div>
					</div>
					
					<div id="createReports" class="section">
						<h2>Create Reports</h2>
						<input type="button" id="getReportbtn" name="getReportbtn" value="Get Report" />
						<input type="button" id="getExcelbtn" name="getExcelbtn" value="Get Excel" />
						<input type="button" id="cancelbtn" name="cancelbtn" value="Cancel" />
					</div>
				</div>
			</div>
			<div class="clear"></div>
		</div>
	</body>
</html>