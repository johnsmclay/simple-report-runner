<form id="reportForm">
	<div id="goBack">
		<input id="backButton" type="button" value="< Back" />
	</div>
	<fieldset>
		<legend id='report'></legend>
		<p id='reportDescription'>DESCRIPTION: <?=$description;?></p>
		<ul>
			<input id="reportID" type="hidden" value="<?=$report_id;?>" name="reportID" />
			<?
				foreach ($report_vars AS $input)
				{
					if ( !empty($input['options']))
					{
						// If there are options available then add a dropdown input
						echo '<li  class="inputElement">';
						echo '<label class="formLabel">' . $input['display_name'] . ': </label>';
						echo form_dropdown($input['text_identifier'], $input['options'], $input['default_value']);
						echo (!empty($input['description']) ? '<div class="description">*' . $input['description'] . '</div>' : '');
						echo '</li>';
					}
					else
					{
						// If the variable type is VARCHAR, INT or CHAR output text inputs
						if (preg_match('~(string|integer)~i', $input['variable_type']))
						{
							$data = array(
									'id' => $input['text_identifier'],
									'name' => $input['text_identifier'],
									'value' => $input['default_value'],
									'valtype' => preg_replace("/\([0-9]*\)/i", "", $input['variable_type']),
							);

							echo '<li class="inputElement">';
							echo '<label class="formLabel">' . $input['display_name'] . ': </label>';
							echo form_input($data);
							echo( !empty($input['description']) ? '<div class="description">*' . $input['description'] . '</div>' : '');
							echo '</li>';
						}
						// If variable type is DATETIME output date controls
						elseif ($input['variable_type'] == 'datetime' && $input['text_identifier'] == 'date_range')
						{
							$dateFrom = array(
									'id' => 'dateFrom',
									'name' => 'start_date',
									'valtype' => preg_replace("/\([0-9]*\)/i", "", $input['variable_type'])
							);

							$dateTo = array(
									'id' => 'dateTo',
									'name' => 'end_date',
									'valtype' => preg_replace("/\([0-9]*\)/i", "", $input['variable_type'])
							);

							echo '<li class="inputElement">';
							echo '<label class="formLabel">From: </label>';
							echo form_input($dateFrom);
							echo '</li>';
							echo '<li class="inputElement">';
							echo '<label class="formLabel">To: </label>';
							echo form_input($dateTo);
							echo '</li>';

							// Date control buttons
							echo '<li id="dateButtonsGroup">';
							echo '<div>';
							
							$prevMonth = array(
									'id' => 'previousMonth',
									'class' => 'dateButton',
									'value' => "<<< Month",
									'type' => 'button'
							);
							echo form_input($prevMonth);
							
							$thisMonth = array(
									'id' => 'thisMonth',
									'class' => 'dateButton',
									'value' => "This Month",
									'type' => 'button'
							);
							echo form_input($thisMonth);
							
							$nextMonth = array(
									'id' => 'nextMonth',
									'class' => 'dateButton',
									'value' => ">>> Month",
									'type' => 'button'
							);
							echo form_input($nextMonth);
							echo '</div>';
							echo '<div>';
							
							$thisQrtr = array(
									'id' => 'thisQuarter',
									'class' => 'dateButton',
									'value' => "This Quarter",
									'type' => 'button'
							);
							echo form_input($thisQrtr);
							
							$previousQrtr = array(
									'id' => 'previousQuarter',
									'class' => 'dateButton',
									'value' => "Previous Quarter",
									'type' => 'button'
							);
							echo form_input($previousQrtr);
							
							$fiscalYear = array(
									'id' => 'fiscalYear',
									'class' => 'dateButton',
									'value' => "Fiscal Year",
									'type' => 'button'
							);
							echo form_input($fiscalYear);
							echo '</div>';
							echo '</li>';
						}
					}

				}
				echo '<li class="inputElement"><label>Report Format:</label>CSV <input type="radio" name="reportFormat" checked="checked" value="csv" /> &nbsp;HTML <input type="radio" name="reportFormat" value="html" /></li>';
				echo '<li class="inputElement">';
				$submit = array(
					'id' => 'submitReportBtn',
					'value' => 'Get Report',
					'name' => 'reportSubmit'
				);
				echo form_submit($submit);
				echo '<div id="loaderImg"><img src="' . base_url() . 'assets/images/ajax-loader-2.gif" alt="Loader Image" /></div>';
				$scheduleReport = array(
					'id' => 'scheduleReportBtn',
					'type' => 'button',
					'value' => 'Schedule Report'
				);
				echo form_input($scheduleReport);
				echo '</li>';
			?>
		</ul>
	</fieldset>
</form>
<div id="errorModal"></div>