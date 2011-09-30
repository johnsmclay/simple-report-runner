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
						<h2>Select Report</h2>
						<ul id="reportList">
							<?php
							for ($i=1;$i<16;$i++) 
							{
								if ($i%2 == 0)
								{
									echo '<li class="even"><a id="item' . $i . '">Item '. $i . '</a></li>';
								}
									else
										{
											echo '<li class="odd"><a id="item' . $i . '">Item '. $i . '</a></li>';
										}
							}
							?>
						</ul>
						
					</div>
				</div>
			</div>
			<div class="clear"></div>
		</div>
		<iframe id="secretIFrame" src=""></iframe>
	</body>
</html>