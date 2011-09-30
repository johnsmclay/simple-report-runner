// Add page specific JavaScript to this file
$(function() 
{
	
	$('#reportForm').hide();
	// EVENT LISTENERS
	$('#reportForm').submit(function() 
	{
		validateFields(this);
		return false;
		var values = {};
		$.each($('#reportForm').serializeArray(),function(i,field) 
		{
			values[field.name] = field.value;
			console.log(field.name + ' : ' + field.value);
		});
		
		// Keep the form from actually submitting
		return false;
		
	});
	
	$('#reportList li').each(function() 
	{
		$(this).hover(function() 
		{
			$(this).stop(false,true).animate(
				{
					marginLeft	: "10px",
					marginRight	: "-10px",
					borderRight : "3px solid #000000",
					borderLeft : "3px solid #000000"
				}, 200);
		}, function ()
		{
			$(this).stop(false,true).animate(
				{
					marginLeft	: "0",
					marginRight	: "0",
					border		: "none"
				}, 200);
		});
		
		$(this).click(function() 
		{
			$('#reportForm #report').text('You clicked : ' + $(this).children().first().attr('id'));
			console.log($(this).children().first().attr('id'));
			$('#reportList').hide(500);
			$('#reportForm').show(500);
		});
	});
	
	
	function validateFields(form) 
	{
		var numRegEx = /^[0-9]*$/;
		var dateRegEx = /^(0|1)[1-9]\/(0[1-9]|[12][1-9]|3[01])\/20[01][0-9]$/;
		
		// Iterate through all form elements and test their value for the correct data type
		$('#' + $(form).attr('id') + ' :input').each(function()
		{
			var id = $(this).attr('id');
			
			if($(this).attr('valtype') == 'INT') {
				
				// Check to see if numbers only were entered
				if($(this).val().match(numRegEx)) 
				{
					if($(this).hasClass('inputError'))
						$(this).removeClass('inputError');
				}
					else 
					{
						$(this).addClass('inputError');
						$('#errorModal').text('').append('<p>The highlighted field accepts numbers only, please correct it.</p>').dialog(
							{
								modal			: true,
								closeOnEscape	: true,
								draggable		: false,
								position		: ['center',200],
								resizable		: false,
								title			: 'There seems to be a problem',
								minHeight		: 20,
								maxHeight		: 100,
								close			: function() 
								{
									$('#'+id).focus();
								}
							});
						
						return false;
					}
			}
			
			if($(this).attr('valtype') == 'DATETIME') 
			{
				
				// Check that the format of the date is correct
				if($(this).val().match(dateRegEx)) 
				{
					if($(this).hasClass('inputError'))
						$(this).removeClass('inputError');
				}
					else 
					{
						$(this).addClass('inputError').focus();
						$('#errorModal').text('').append('<p>The highlighted field has an invalid date format, please correct it.</p>').dialog(
							{
								modal			: true,
								closeOnEscape	: true,
								draggable		: false,
								position		: ['center',40],
								resizable		: false,
								title			: 'There seems to be a problem',
								close			: function(event, ui) 
								{
									$('#'+id).focus();
								}
							});
						return false;
					}
			}
			
		});
		return false;
	}
	
});