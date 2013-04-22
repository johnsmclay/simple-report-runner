<?php $this->load->view('dependencies/header', array(
		'title' => 'Report Builder',
		'header1' => 'The Reporting Dashboard',
		'header2' => 'Reporting',
));
// @TODO: add user id into form so it will submit with the data and insert into the DB
?>
<form id="reportBuilderForm">
	<input type="hidden" name="creator_user_id" value="<?=$userID;?>" /> <? // Passes the ID of the user who created the report ?>
	<div id="reportBuilderSections">
		<div id="reportDataSection" class="shadow">
			<span class="sectionTitle">Report Data</span>
			<fieldset>
				<ul>
					<li>
						<label for="visibilityCheckbox" title="Will this be an internal (private) or external (public) report?" >Visibility</label>
						<select id="visibility" name="visibility" title="Will this be an internal (private) or external (public) report?">
							<option value="private">Private</option>
							<option value="public">Public</option>
						</select>
					</li>
					<li>
						<label for="category_id"><span class="required">*</span>Category:</label>
						<select id="category_id" name="category_id" req="true">
							<option value=""></option>
							<?
							foreach ($categories AS $val)
							{
								echo "<option value='{$val['id']}'>{$val['title']}</option>";
							}
							?>
						</select>
					</li>
					<li>
						<label title="The display name for this report in the report menu" for="display_name"><span class="required">*</span>Display Name:</label>
						<input id="display_name" name="display_name" type="text" req="true" />
					</li>
					<li>
						<label for="description"><span class="required">*</span>Description:</label>
						<input id="description" name="description" type="text" req="true" />
					</li>
					<li>
						<label for="type">Type: </label>
						<select id="type" name="type">
							<option value="mysql" selected="selected">MySQL</option>
						</select>
					</li>
					<li>
						<label for="report_data"><span class="required">*</span>Report Query:</label>
						<br />
						<span>*denote query variables by surrounding them with ~ (tilde)</span>
						<textarea id="report_data" name="report_data" req="true"></textarea>
					</li>
					<li>
						<input id="generateVarBtn" type="button" value="Generate Variables" />
					</li>
				</ul>
			</fieldset>
		</div>
		<div id="reportVariableSection" class="shadow">
			<span class="sectionTitle">Report Variables</span>
			<fieldset id="reportVariablesFieldset"></fieldset>
		</div>
		<div id="reportConnectionSection" class="shadow">
			<span class="sectionTitle">Report Connection</span>
			<fieldset>
				<ul>
					<li>
						<label for="connection_id">Database Connection: </label>
						<select id="connection_id" name="connection_id" req="true">
							<option value=""></option>
						<?
						foreach($connections AS $val)
						{?>
							<option value="<?=$val['id'];?>"><?=$val['display_name'];?></option>
						<?}?>
						</select>
					</li>
					<li>
						<input id="newConnectionBtn" type="button" value="New Connection" />
						<input id="cancelConnectionBtn" type="button" value="Cancel" />
					</li>
				</ul>
			</fieldset>
		</div>
	</div>
	<div id="buttonColumn"></div>
	<div class="clear"></div>
</form>
<div id="errorModal"></div>
<?php $this->load->view('dependencies/footer');?>
