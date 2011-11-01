<div id="newConnectionSection">
	<fieldset>
		<ul>
			<li>
				<label><span class="required">*</span>Display Name:</label>
				<input type="text" id="connection_display_name" name="connection_display_name" req="true" />
				<input id="connectionForm" type="hidden" name="connectionForm" value="true" /> <? //This is just a flag that is checked to see if this form exists ?>
			</li>
			<li>
				<label>url:</label>
				<input type="text" id="connection_url" name="connection_url" />
			</li>
			<li>
				<label><span class="required">*</span>username:</label>
				<input type="text" id="connection_username" name="connection_username" req="true" />
			</li>
			<li>
				<label><span class="required">*</span>password:</label>
				<input type="text" id="connection_password" name="connection_password" req="true" />
			</li>
			<li>
				<label><span class="required">*</span>host:</label>
				<input type="text" id="connection_hostname" name="connection_hostname" req="true" />
			</li>
			<?
				// TODO: Change the "type" input to a dynamic dropdown that pulls ENUM values from DB
			?>
			<li>
				<label>type:</label>
				<select id="connection_type" name="connection_type">
					<option value="MySQL">MySQL</option>
					<option value="MSSQL">MSSQL</option>
					<option value="Brain Honey">Brain Honey</option>
					<option value="Brain Honey">PostgreSQL</option>
				</select>
			</li>
			<li>
				<label><span class="required">*</span>Database:</label>
				<input type="text" id="connection_database" name="connection_database" title="The name of the database you want to connect to" req="true" />
			</li>
			<li>
				<label>Database Prefix:</label>
				<input type="text" id="connection_dbprefix" name="connection_dbprefix" />
			</li>
			<li>
				<label>Persistent Conneciton:</label>
				<input type="checkbox" id="connection_pconnect" checked="checked" />
				<input type="hidden" name="connection_pconnect" id="hidden_pconnect" value="TRUE" />
			</li>
			<li>
				<label>Display Database Errors?</label>
				<input type="checkbox" id="connection_db_debug" checked="checked" />
				<input type="hidden" name="connection_db_debug" id="hidden_db_debug" value="TRUE" />
			</li>
			<li>
				<label>Database Cache:</label>
				<input type="checkbox" id="connection_cache_on" />
				<input type="hidden" name="connection_cache_on" id="hidden_cache_on" />
			</li>
			<li>
				<label>Database Cache Directory:</label>
				<input type="text" id="connection_cachedir" name="connection_cachedir" />
			</li>
			<li>
				<label>Character Set:</label>
				<input type="text" id="connection_char_set" name="connection_char_set" value="utf8" />
			</li>
			<li>
				<label>Character Collation:</label>
				<input type="text" id="connection_dbcollat" name="connection_dbcollat" value="utf8_general_ci" />
			</li>
			<li>
				<label>Swap Database Prefix:</label>
				<input type="text" id="connection_swap_pre" name="connection_swap_pre" />
			</li>
			<li>
				<label>Auto Initialize Connection:</label>
				<input type="checkbox" id="connection_autoinit" checked="checked" />
				<input type="hidden" name="connection_autoinit" id="hidden_auto_init" value="TRUE" />
			</li>
			<li>
				<label>Strict Mode:</label>
				<input type="checkbox" id="connection_stricton" />
				<input type="hidden" name="connection_stricton" id="hidden_stricton" value="FALSE" />
			</li>
			<li>
				<label>Port:</label>
				<input type="text" id="connection_port" name="connection_port" value="3306" />
			</li>
		</ul>
	</fieldset>
</div>