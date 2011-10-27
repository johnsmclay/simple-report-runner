$(function()
{
	//**********************************//
	//									//
	//		On load requirements		//
	//									//
	//**********************************//
	
	// A global var for storing the value of the report connection drop down
	var rememberConnection;
	
	// Reset the form on refresh
	$(':input','#reportBuilderForm')
	 .not(':button, :submit, :reset, :hidden, #visibilityCheckbox')
	 .val('')
	 .removeAttr('checked')
	 .removeAttr('selected');
	
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
		
		reportConnection = $('#connection_id').val();
		
		$('#connection_id').val('').removeAttr('req');
		
		$.ajax(
		{
			url			: 'reportbuilder/getConnectionForm',
			dataType	: 'json',
			success		: function(data)
			{
				if (data.status == true)
				{
					$('#reportConnectionSection').append(data.html);
					// Setup checkboxes to toggle hidden input values. This overcomes jQuery's failure to grab unchecked checkboxes using serializeArray
					$('input:checkbox').each(function()
					{
						$(this).click(function()
						{
							var checked = $(this).is(':checked') ? 'TRUE' : 'FALSE';
							$(this).next().val(checked);
						})
					})
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
		
		$('#connection_id').val(reportConnection).attr('req','true');
	}
});
