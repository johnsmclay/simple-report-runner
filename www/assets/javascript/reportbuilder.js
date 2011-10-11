$(function()
{
	
	// Handle default text in query textarea's
	var textarea = 'Enter query here...';
	$('#reportQuery, #optionsQuery').val(textarea);
	
	$('#reportQuery, #optionsQuery').click(function()
	{
		if ($(this).val() == 'Enter query here...')
		{
			$(this).val('');
		}
	});
	
	$('#reportQuery, #optionsQuery').blur(function()
	{
		if ($(this).val() == '')
		{
			$(this).val(textarea);
		}
	});
});
