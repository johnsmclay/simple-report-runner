<!DOCTYPE >
<html>
	<head>
		<title>Report Builder</title>
		<? $this->load->view('dependencies/source_links'); ?>
	</head>
	<body>
		<div id="wrapper">
			<h1 class="arial center">The Reporting Dashboard</h1>	
			<?php
				$this->load->view('dependencies/navigation_menu');
			?>
			
			<div id="main">
				<div id="formElements">
					<div id="createReports" class="section">
						<h2>Middlebury Interactive Reporting</h2>
						
						<ul id="reportList">
						</ul>
					</div>
				</div>
			</div>
			<div class="clear"></div>
		</div>
		<?
		// The secret iFrame is for pushing an automatic download
		// of the report upon completing generation of the form via an AJAX call
		?>
		<iframe id="secretIFrame" src=""></iframe>
	</body>
</html>