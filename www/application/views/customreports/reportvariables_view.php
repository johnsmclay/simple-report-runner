<?php
	// Track how many variables are left to determine whether the separator is needed or not
	$count = count($variables);
	
	// Loop through all the variables and produce form elements for them
	foreach ($variables AS $val):
		--$count; 
		$variable = preg_replace('/~/', '', $val);
		
?>

<ul>
	<li>
		<label><span class="required">*</span>Variable Type:</label>
		<select id="variable_type__<?=$variable;?>" name="variable_type__<?=$variable;?>" req="true">
			<option value=""></option>
			<option value="integer">Integer</option>
			<option value="string">String</option>
			<option value="datetime">Date/Time</option>
		</select>
	</li>
	<?if (!preg_match('/date/i',$variable)):?>
	<li>
		<label title="Add a value that should be the default for this variable [optional]">Default Value:</label>
		<input id="default_value__<?=$variable;?>" name="default_value__<?=$variable;?>" type="text" title="Add a value that should be the default for this variable [optional]" />
	</li>
	<?endif; ?>
	<li>
		<label><span class="required">*</span>Text Identifier:</label>
		<input id="text_identifier__<?=$variable;?>" name="text_identifier__<?=$variable;?>" type="text" value="<?=$variable;?>" req="true" />
		<input name="variableName_<?=$variable;?>" value="<?=$variable;?>" type="hidden" />
	</li>
	<li>
		<label title="The name that will be displayed next to the input in the form" ><?if(!preg_match('/date_range/i',$variable)):?><span class="required">*</span><?endif;?>Display Name:</label>
		<input id="display_name__<?=$variable;?>" name="display_name__<?=$variable;?>" type="text" <? if(!preg_match('/date_range/i',$variable)):?> req="true" <?endif;?> title="The name that will be displayed next to the input in the form" />
	</li>
	<li>
		<label title="Enter a description to help the user understand the need for this variable">Description:</label>
		<input id="description__<?=$variable;?>" name="description__<?=$variable;?>" type="text" title="Enter a description to help the user understand the need for this variable" />
	</li>
	<li>
		<label title="Enter a MySQL Query to generate options for a dropdown menu">Options Query:</label>
		<input type="checkBox" class="optionsCheck" title="Enter a MySQL Query to generate options for a dropdown menu" />
		<textarea class="optionsQuery" id="options_query__<?=$variable;?>" name="options_query__<?=$variable;?>" title="Enter a MySQL Query to generate options for a dropdown menu" ></textarea>
	</li>
</ul>
<?if($count != 0){?>
<div class="separator"></div>
<?php } 
	endforeach?>