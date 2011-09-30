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
						<h2>Report Elements</h2>
						
						<form id="reportForm" method="post">
						<?
						foreach ($report_vars AS $input) 
						{
							if (!empty($input['options'])) 
							{
								// If there are options available then add a dropdown input
								echo '<div class="inputElement">';
								echo '<span class="formLabel">' . $input['display_name'] . ': </span>';
								echo form_dropdown($input['text_identifier'],$input['options'],$input['default_value']);
								echo (!empty($input['description']) ? '<div class="description">' . $input['description'] . '</div>' : '');
								echo '</div>';
							}	
								else 
								{
									// If the variable type is VARCHAR, INT or CHAR output text inputs
									if (preg_match('~(varchar|int|char)~i',$input['variable_type'])) 
									{
										$data = array(
											'id' => $input['text_identifier'],
											'name' => $input['text_identifier'],
											'value' => $input['default_value'],
											'valtype' => preg_replace("/\([0-9]*\)/i", "", $input['variable_type']),
											'maxlength' => preg_replace("/[^0-9]*/i", "", $input['variable_type'])
										);
										
										echo '<div class="inputElement">';
										echo '<span class="formLabel">' . $input['display_name'] . ': </span>';
										echo form_input($data);
										echo (!empty($input['description']) ? '<div class="description">' . $input['description'] . '</div>' : '');
										echo '</div>';
									}
										// If variable type is DATETIME output date controls
										elseif ($input['variable_type'] == 'DATETIME') 
										{
											$dateFrom = array(
												'id' => 'dateFrom',
												'name' => 'dateFrom',
												'valtype' => preg_replace("/\([0-9]*\)/i", "", $input['variable_type'])
											);
											
											$dateTo = array(
												'id' => 'dateTo',
												'name' => 'dateTo',
												'valtype' => preg_replace("/\([0-9]*\)/i", "", $input['variable_type'])
											);
											
											echo '<div class="inputElement">';
											echo '<span class="formLabel">To: </span>';
											echo form_input($dateFrom);
											echo '<span class="formLabel">&nbsp;&nbsp;From: </span>';
											echo form_input($dateTo);
											echo '</div>';
											
											// Date control buttons
											echo '<div id="dateButtons">';
												echo '<div>';
													$prevMonth = array('id'=>'previousMonth','value'=>"<<< Month",'type'=>'button');
													echo form_input($prevMonth);
													$thisMonth = array('id'=>'thisMonth','value'=>"This Month",'type'=>'button');
													echo form_input($thisMonth);
													$nextMonth = array('id'=>'nextMonth','value'=>">>> Month",'type'=>'button');
													echo form_input($nextMonth);
												echo '</div>';
												echo '<div>';
													$thisQrtr = array('id'=>'thisQuarter','value'=>"This Quarter",'type'=>'button');
													echo form_input($thisQrtr);
													$previousQrtr = array('id'=>'previousQuarter','value'=>"Previous Quarter",'type'=>'button');
													echo form_input($previousQrtr);
													$fiscalYear = array('id'=>'fiscalYear','value'=>"Fiscal Year",'type'=>'button');
													echo form_input($fiscalYear);
												echo '</div>';
											echo '</div>';
											
										}
								}
							
						}
						echo '<div class="inputElement">';
						echo form_submit('reportSubmit','Get Report');
						echo '</div>';
						?>
						</form>
						<div id="errorModal"></div>
					</div>
				</div>
			</div>
			<div class="clear"></div>
		</div>
		<iframe id="secretIFrame" src=""></iframe>
	</body>
</html>