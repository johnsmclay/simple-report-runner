$(function()
{
	//**********************************//
	//									//
	//		On load requirements		//
	//									//
	//**********************************//
	
	// Reset the form on refresh
	$(':input','#reportBuilderForm')
	 .not(':button, :submit, :reset, :hidden, #visibilityCheckbox')
	 .val('')
	 .removeAttr('checked')
	 .removeAttr('selected');
	
	// Handle default text in query textarea's
	var textarea = 'Enter query here...';
	$('#report_data').val(textarea);
	
	$('#report_data').focus(function()
	{
		if ($(this).val() == 'Enter query here...')
		{
			$(this).val('');
		}
	});
	
	$('#report_data').blur(function()
	{
		if ($(this).val() == '')
		{
			$(this).val(textarea);
		}
	});
	
	// Call the createNewConnection function when this button is clicked
	$('#newConnectionBtn').click(function()
	{
		createNewConnection();
	});
	
	// Call the cancelNewConnection function when this button is clicked
	$('#cancelConnectionBtn').click(function()
	{
		cancelNewConnection();
	});
	
	// Call the generateVariables function when the button is clicked
	$('#generateVarBtn').click(function()
	{
		generateVariables();
	});
	
	// If the new connection form is showing and an existing connection is selected, remove the new connection form
	$('#connection_id').change(function()
	{
		if ($(this).val() != "" && $('#connectionForm').length > 0)
		{
			cancelNewConnection();
		}
	});
	
	// Hide the new connection form when the page loads
	$('#newConnectionSection').hide();
	
	$('#reportBuilderForm').submit(function()
	{
		handleReportForm($(this));
		return false;
	});
	
	$('#reportConnection').change(function()
	{
		var connectionId = $(this).val();
		console.log(connectionId);
		if (connectionId != 0  && $('#connectionForm').length == 0)
		{
			$(this).removeAttr('req');
		}
			else if($('#connectionForm').length > 0)
			{
				$(this).attr('req','true');
			}
	});
	
	//**********************//
	//						//
	//		Functions		//
	//						//
	//**********************//
	
	function handleReportForm(form)
	{
		var values = serializeForm(form,true);
		
		if ($('#connectionForm').length > 0)
		{
			$('#connection_id').val("");
		}
		
		$.ajax(
		{
			url			: 'reportbuilder/checkInputs',
			data		: values,
			type		: 'post',
			dataType	: 'json',
			success		: function(data)
			{
				clearErrors(form);
				if (data.status == 'error')
				{
					for (var i in data.errors)
					{
						$('#'+data.errors[i]).addClass('requiredError');
					}
				}
					else if (data.status == 'passed')
					{
						clearErrors(form);
					}
			}
		});
		return false;
	}
	
	function clearErrors(form)
	{
		$.each($(form).serializeArray(),function(i,field)
		{
			if ($('#'+field.name).hasClass('requiredError'))
			{
				$('#'+field.name).removeClass('requiredError');
			}
		});
	}
	
	function generateVariables()
	{
		var query = {'query' : $('#report_data').val()};

		// If there is nothing entered display a notice
		if (query.query == '' || query.query == 'Enter query here...')
		{
			$('#errorModal').text('').append('<p>Please enter a query in the box before generating variables.</p>').dialog(
			{
				modal			: true,
				closeOnEscape	: true,
				draggable		: false,
				position		: ['center',200],
				resizable		: false,
				title			: 'There seems to be a problem',
				minHeight		: 20,
				maxHeight		: 100
			});
			return;
		}
		
		$.ajax(
		{
			url			: 'reportbuilder/generateVariables',
			type		: 'post',
			dataType	: 'json',
			data		: query,
			success		: function(data)
			{
				if($('#reportVariablesFieldset').children())
				{
					$('#reportVariablesFieldset').children().each(function()
					{
						$(this).remove();
					});
				}
				
				$('#reportVariablesFieldset').append(data.html);
				if($('#createReportBtn').length <= 0)
				{
					$('#buttonColumn').append('<input type="submit" value="Submit" id="createReportBtn">');
					$('#createReportBtn').hide();				
					$('#createReportBtn').show('bounce',{direction:'up'},500);
				}
				$('input:submit').button();
				$('.optionsQuery').hide();
				$('.optionsCheck').click(function()
				{
					$(this).next().toggle();
					if ($(this).next().css('display') != 'none')
					{
						$(this).next().attr('req','true')
					}
						else
						{
							$(this).next().removeAttr('req');
						}
				})
			}
		})
	}
	
	function createNewConnection()
	{
		if ($('#newConnectionSection').length > 0)
		{
			return false;
		}
		
		$('#reportConnection').val('0').removeAttr('req');
		
		$.ajax(
		{
			url			: 'reportbuilder/getConnectionForm',
			dataType	: 'json',
			success		: function(data)
			{
				if (data.status == true)
				{
					$('#reportConnectionSection').append(data.html);
				}
			}
		});
	}
	
	function cancelNewConnection()
	{
		if ($('#newConnectionSection').length > 0)
		{
			$('#newConnectionSection').slideToggle().remove();
		}
		
		if ($('#reportConnection').val() == 0)
		{
			$('#reportConnection').attr('req','true');
		}
	}
});
