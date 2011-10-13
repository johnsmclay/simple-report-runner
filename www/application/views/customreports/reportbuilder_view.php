<?php $this->load->view('dependencies/header',array(
	'title' => 'Report Builder',
	'header1' => 'The Reporting Dashboard',
	'header2' => 'Middlebury Interactive Reporting',
)); ?>
						<form id="reportBuilderForm">
							<div id="reportDataSection" class="shadow">
								<span class="sectionTitle">Report Data</span>
								<fieldset>
									<ul>
										<li>
											<label for="visibilityCheckbox">Visible?</label>
											<input id="visibilityCheckbox" type="checkbox" checked="checked" />
										</li>
										<li>
											<label for="reportDisplayName">Display Name:</label>
											<input id="reportDisplayName" name="reportDisplayName" type="text" />
										</li>
										
										<li>
											<label for="queryType">Type: </label>
											<select id="queryType">
												<option value="MySQL">MySQL</option>
												<option value="MSSQL">MSSQL</option>
												<option value="Brain Honey">Brain Honey</option>
											</select>
										</li>
										<li>
											<span>*denote query variables by surrounding them with ~ (tilde)</span>
											<textarea id="reportQuery" name="reportQuery">Enter query here...</textarea>
										</li>
										<li>
											<input id="generateVarBtn" type="button" value="Generate Variables" />
										</li>
									</ul>
								</fieldset>
							</div>
							<div id="reportVariableSection" class="shadow">
								<span class="sectionTitle">Report Variables</span>
								<fieldset>
									<ul>
										<li>
											<label>Variable Type:</label>
											<select id="variableType" name="variableType">
												<option value=""></option>
												<option value="integer">Integer</option>
												<option value="string">String</option>
												<option value="datetime">Date/Time</option>
											</select>
										</li>
										<li>
											<label>Default Value:</label>
											<input id="defaultValue" type="text" />
										</li>
										<li>
											<label>Text Identifier:</label>
											<input id="textIdentifier" type="text" />
										</li>
										<li>
											<label>Display Name:</label>
											<input id="displayName" type="text" />
										</li>
										<li>
											<label>Description:</label>
											<input id="description" type="text" />
										</li>
										<li>
											<label></label>
											<textarea id="optionsQuery" name="optionsQuery">Enter query here...</textarea>
										</li>
									</ul>
								</fieldset>
							</div>
							<div id="reportConnectionSection" class="shadow">
								<span class="sectionTitle">Report Connection</span>
								
							</div>
						</form>
<?php $this->load->view('dependencies/footer'); ?>