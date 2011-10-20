<?php $this->load->view('dependencies/header',array(
	'title' => 'User Administration',
	'header1' => 'The Reporting Dashboard',
	'header2' => 'PGLMS Weekly Enrollment Reports',
)); ?>
		<p><?=validation_errors()?></p>
		<?=form_open('pglmsweeklies/requestReport','id="pglmsWeekliesForm"')?>
		<fieldset>
			<ul>
				<li class="inputElement">
					<label class="formLabel">School:</label>
					<select name="school_id" id"school_id">
						<?
							foreach($schools AS $school)
							{
								// Set PG Super School as the default selected school
								$selected = $school['id'] == 1 ? "selected='selected'" : '';
								echo "<option value='{$school['id']}' {$selected}>{$school['description']}</option>";
							}
						?>
					</select>
				</li>				
				<li>
					<label class="formLabel">From: </label>
							<input type="text" id="dateFrom" name="from_date" />
				</li>				
				<li>
					<label class="formLabel">To: </label>
					<input type="text" id="dateTo" name="to_date" />
				</li>				
				<li id="dateButtonsGroup">
					<div>
						<input type="button" id="previousMonth" class="dateButton" value="&lt;&lt;&lt; Month" />							
						<input type="button" id="thisMonth" class="dateButton" value="This Month" />							
						<input type="button" id="nextMonth" class="dateButton" value="Month &gt;&gt;&gt;" />
					</div>
					<div>						
						<input type="button" id="thisQuarter" class="dateButton" value="This Quarter" />							
						<input type="button" id="previousQuarter" class="dateButton" value="Previous Quarter" />
						<input type="button" id="fiscalYear" class="dateButton" value="Fiscal Year" />
					</div>
				</li>				
				<li>
					<input type="hidden" value="test" name="hiddenInput" />
					<?=form_submit('submit', 'Run Report')?>
				</li>				
			</ul>
		</fieldset>
		<?=form_close()?>
<?php $this->load->view('dependencies/footer'); ?>