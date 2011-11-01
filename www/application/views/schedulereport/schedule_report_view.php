<form id="scheduleReportForm">
	<fieldset>
		<legend>Schedule When The Report Should Run</legend>
		<div id="scheduleInstructions">
			<p>Set the desired values to schedule your report to be processed at a future date.
			Any value (Month, Day, Hour) left empty will default to meaning -- every.
			</p>
			<p class"exampleBlock">
			Examples:<br />
			<span class="example">February,<span class="bold italic">&lt;blank&gt;</span>,Monday,4:00AM -- The report would run every Monday in February at 4:00AM</span>
			<span class="example"><span class="bold italic">&lt;blank&gt;</span>,12,<span class="bold italic">&lt;blank&gt;</span>,1:00PM -- The report would run on the 12th of every month at 1:00PM</span>
			</p>
		</div>
		<ul>
			<li>
				<label>Month of the Year: </label>
			<select id="month_of_year" name="month_of_year">
				<option value="*" selected="selected"></option>
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
				<label>Send To:</label>
				<select id="user" name="user">
					<option value='*' selected="selected"></option>
					<?
						foreach ($users AS $user)
						{
							echo "<option value='{$user->id}'>{$user->fname} {$user->lname}</option>";
						}
					?>
				</select>
			</li>
			<li>
				<?
					$schedule = array(
						'id' => 'scheduleSubmitBtn',
						'name' => 'scheduleSubmitBtn',
						'value' => 'Schedule It'
					);
					echo form_submit($schedule);
				?>
			</li>
		</ul>
	</fieldset>
	
</form>
