<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<title>New Dashboard</title>
		<?$this->load->view('dependencies/source_links');?>
	</head>
	<body>
		<div id="wrapper">
			<h1 class="arial center">The Dashboard</h1>	
			<?php
				$this->load->view('dependencies/navigation_menu');
			?>
			
			<div id="main">
				<!-- CUSTOMER SECTION -->
				<div id="formElements">
					<div id="teacherSelection" class="section">
						<h2>Teacher Selection</h2>
						<div>
							<span class="formLabel">Teacher:</span>
							<select id="teacherList">
								<option value="0">All PowerSpeak Teachers</option>
							</select>
							<img id="teacherLoader" class="hideLoader" src="<?=base_url();?>assets/images/ajax-loader.gif" alt="loader gif" />
						</div>
					</div>
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
					</div>
					
					<div id="viewOptions" class="section">
						<h2>View Options</h2>
						<div>
							<span class="formLabel">Report Grouping - Primary: </span>
							<select>
								<option value="teacher">Teacher</option>
								<option value="clientLocation">Client Location</option>
							</select>
							
							<span class="formLabel">Secondary: </span>
							<select>
								<option value="none">none</option>
								<option value="clientLocation">Client Location</option>
								<option value="course">Course</option>
								<option value="language">Language</option>
								<option value="classroom">Classroom</option>
							</select>
						</div>
						<div>
							<span class="formLabel">Summary</span>
							<input type="radio" name="summaryDetailRadio" id="summaryView" checked="checked" value="summary"/>
							<span class="formLabel">Detail</span>
							<input type="radio" name="summaryDetailRadio" id="detailView" value="detail"/><br />
							<span class="formLabel">New</span>
							<input type="radio" name="newActiveRadio" id="newView" checked="checked" value="new"/>
							<span class="formLabel">Active</span>
							<input type="radio" name="newActiveRadio" id="activeView" value="active"/>
						</div>
					</div>
					
					<div id="enrollmentSelection" class="section">
						<h2>Enrollment Selection</h2>
						<div>
							<span class="formLabel">From: </span>
							<input type="text" id="dateFrom" name="dateFrom" />
							<span class="formLabel">To: </span>
							<input type="text" id="dateTo" name="dateTo" /> <br />
							<div id="dateButtons">
								<input type="button" id="previousMonth" class="monthBtn" value="&lt;&lt;&lt; Month" />							
								<input type="button" id="thisMonth" class="monthBtn" value="This Month" />							
								<input type="button" id="nextMonth" class="monthBtn" value="Month &gt;&gt;&gt;" />							
								<input type="button" id="thisQuarter" class="monthBtn" value="This Quarter" />							
								<input type="button" id="previousQuarter" class="monthBtn" value="Previous Quarter" />
								<input type="button" id="fiscalYear" class="monthBtn" value="Fiscal Year" />
							</div>
														
							
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