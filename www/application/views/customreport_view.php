<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<title>Reporting Dashboard</title>
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
							<?php
							$category = '';
							foreach($reportList AS $category => $reportArray) {
								echo "<li class='categoryHeading'>{$category}";
								echo "<ul>";
								$counter = 1;
								foreach($reportArray AS $report) {
										
									if ($counter % 2 == 0)
									{
										echo "<li class='even reportItem'><a id='{$report['id']}'>{$report['display_name']}</a></li>";
									}
										else
											{
												echo "<li class='odd reportItem'><a id='{$report['id']}'>{$report['display_name']}</a></li>";
											}
									$counter ++;
								}
								echo "</ul></li>";
							}
							?>
						</ul>
						<? 
						// The dynamicForm div is for the purpose of loading the dynamically built form
						// via an ajax request by the jQuery $.load method.
						?>
						<div id="dynamicForm">
						</div>
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