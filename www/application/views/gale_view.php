<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<title>New Dashboard</title>
		<? $this->load->view('dependencies/source_links'); ?>
	</head>
	<body>
		<div id="wrapper">
			<h1 class="arial center">The Dashboard</h1>	
			<?php
				$this->load->view('dependencies/navigation_menu');
			?>
			
			<div id="main">
				<div id="formElements">
					<div id="createReports" class="section">
						<h2>Create Reports</h2>
						<input type="button" id="getReportbtn" name="getReportbtn" value="Get Report" />
					</div>
				</div>
			</div>
			<div class="clear"></div>
		</div>
		<iframe id="secretIFrame" src=""></iframe>
	</body>
</html>