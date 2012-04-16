<script type="text/javascript">
	$("#reportDefinition").hide();
</script>
<form id="reportForm">
	<div id="goBack">
		<input id="backButton" type="button" value="< Back" />
	</div>
	<div id="notices"></div>
	<fieldset>
		<legend id='report'></legend>
		<p id='reportDescription'>DESCRIPTION: <?=$description;?> <a id="showdefs" href="#" onclick="$('#reportDefinition').show(); $(this).hide();">More...</a></p>
		<div id='reportDefinition'><?=$definition;?></div>
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
							// If the view is beeing called by the Schedulereport controller display this othewise display the "else" statement.
							if ($this->router->class == 'schedulereport')
							{?>
								<li>
								<label>Timeframe:</label>
								The last -&gt;
								<input id="timeQuantifier" name="timeQuantifier" type="text" maxlength="2" />
								<select id='reportTimeFrame' name="reportTimeFrame">
									<option value="day">Day(s)</option>
									<option value="week">Week(s)</option>
									<option value="month">Month(s)</option>
								</select>
								&nbsp;<span class="note">(if the number field is left blank it will default to 1, i.e. 1 day ago)</span>
								</li>
							<?}
								else {
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

				}

				if ($this->router->class == 'schedulereport')
				{?>
					<fieldset id="scheduleTime">
						<legend>Schedule When The Report Should Run</legend>
						<div id="scheduleInstructions">
							<p>Set the desired values to schedule your report to be processed at a future date.
							Any value (Month, Day, Hour) left empty will default to meaning -- every.
							</p>
							<p class"exampleBlock">
							<h4>Examples:</h4>
							<ul>
								<li class="example">
									February,<span class="bold italic">&lt;blank&gt;</span>,Monday,4:00AM -- The report would run every Monday in February at 4:00AM
									
								</li>
								<li class="example">
									<span class="bold italic">&lt;blank&gt;</span>,12,<span class="bold italic">&lt;blank&gt;</span>,1:00PM -- The report would run on the 12th of every month at 1:00PM
								</li>
							</ul>
							</p>
						</div>
						<ul>
							<li>
								<label>Month of the Year: </label>
							<select id="month_of_year" name="month_of_year">
								<option value="*"></option>
								<? 
								$months = getMonths();
								foreach($months AS $val => $month)
								{
									echo "<option value='{$val}'>{$month}</option>";
								}
								?>
							</select>
							</li>
							<li>
								<label>Day of the Month: </label>
							<select id="day_of_month" name="day_of_month">
								<option class="default" value="*" selected="selected"></option>
								<?
									$count = 1;
									while ($count < 32)
									{
										echo "<option value='{$count}'>{$count}</option>";
										++$count;
									}
								?>
							</select>
							</li>
							<li>
								<label>Day of the Week: </label>
							<select id="day_of_week" name="day_of_week">
								<option value="*" selected="selected"></option>
								<option value="0">Sunday</option>
								<option value="1">Monday</option>
								<option value="2">Tuesday</option>
								<option value="3">Wednesday</option>
								<option value="4">Thursday</option>
								<option value="5">Friday</option>
								<option value="6">Saturday</option>
							</select>
							</li>
							<li>
								<label>Hour of the Day: </label>
							<select id="hour_of_day" name="hour_of_day">
								<option value="*" selected="selected"></option>
								<?
								$hours = getHours();
								foreach($hours AS $val => $hour)
								{
									echo "<option value='{$val}'>{$hour}</option>";
								}
								?>
							</select>
							</li>
							<li>
								<label><span class="required">*</span>Send To:</label>
								<select id="user" name="user">
									<option value='' selected="selected"></option>
									<?
										foreach ($users AS $user)
										{
											echo "<option value='{$user->id}'>{$user->fname} {$user->lname}</option>";
										}
									?>
								</select>
							</li>
						</ul>
					</fieldset>
				<?}
				
				if ($this->router->class != 'schedulereport')
				{
					echo '<li class="inputElement"><label>Report Format:</label>CSV <input type="radio" name="reportFormat" checked="checked" value="csv" /> &nbsp;HTML <input type="radio" name="reportFormat" value="html" /></li>';
				}
					else
					{
						echo "<li class='inputElement><input type='hidden' value='csv' name='reportFormat' /></li>";
					}
				echo '<li class="inputElement">';
				
				if ($this->router->class != 'schedulereport')
				{
					$submit = array(
						'id' => 'submitReportBtn',
						'value' => 'Get Report',
						'name' => 'reportSubmit'
					);
					echo form_submit($submit);
				}
					else
					{
						$scheduleReport = array(
							'id' => 'scheduleReportBtn',
							'type' => 'button',
							'value' => 'Schedule Report'
						);
						echo form_input($scheduleReport);
					}
				echo '<div id="loaderImg"><img src="' . base_url() . 'assets/images/ajax-loader-2.gif" alt="Loader Image" /></div>';
				echo '</li>';
			?>
		</ul>
	</fieldset>
</form>
<div id="errorModal"></div>