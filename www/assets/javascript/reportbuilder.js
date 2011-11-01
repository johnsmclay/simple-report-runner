$(function()
{
	//**********************************//
	//									//
	//		On load requirements		//
	//									//
	//**********************************//
	
	// A global var for storing the value of the report connection drop down
	var reportConnection;
	
	// Reset the form on refresh
	clearForm('reportBuilderForm');
	
	// Call the createNewConnection function when this button is clicked
	$('#newConnectionBtn').click(function()
	{
		createNewConnection();
	});
	
	// Call the cancelNewConnection function when this button is clicked
	$('#cancelConnectionBtn').click(function()
	{
		if ($('#connectionForm').length > 0)
		{
			cancelNewConnection();
		}
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
			cancelNewConnection($(this).val());
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
	
	/**
	 * Once the form has been submitted handle all
	 * necessary preparations before passing it to the controller
	 */
	function handleReportForm(form)
	{
		var values = serializeForm(form,true);
		
		$('#createReportBtn').hide();
		$('#ajaxLoader').show();
		
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
				$('#ajaxLoader').hide();
				$('#createReportBtn').show();
				
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
						clearForm('reportBuilderForm');
					}
			}
		});
		return false;
	}
	
	/**
	 * Clear all errors on the form
	 */
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
	
	/**
	 * Generate the variables by parsing the SQL statement
	 */
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
		
		// Pass the SQL statement to the controller where it will be parsed
		$.ajax(
		{
			url			: 'reportbuilder/generateVariables',
			type		: 'post',
			dataType	: 'json',
			data		: query,
			success		: function(data)
			{
				// Remove any variables (form elements) that exist to avoid duplication
				if($('#reportVariablesFieldset').children())
				{
					$('#reportVariablesFieldset').children().each(function()
					{
						$(this).remove();
					});
				}
				
				// Append the html received from the controller into the fieldset
				$('#reportVariablesFieldset').append(data.html);
				
				// If the submit button for the form does not exist yet create it
				if($('#createReportBtn').length == 0)
				{
					// Append the report button into the button column
					$('#buttonColumn').append('<input type="submit" value="Submit" id="createReportBtn">');
					// Append an ajax loader image into the button column
					$('#buttonColumn').append('<img id="ajaxLoader" src="assets/images/ajax-loader-2.gif" />');
					// Hide the ajax loader until we are ready for it
					$('#ajaxLoader').hide();
					
					// Hide the submit button and then show it with an easing effect *for UI interaction and notification*
					$('#createReportBtn').hide();				
					$('#createReportBtn').show('bounce',{direction:'up',distance : 40,mode : 'show',times : 6},500);
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
	
	/**
	 * Create the new connection form
	 */
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
	
	/**
	 * Destroy the new connection form so no values from it will be passed
	 * to the controller when the form is created causing needless processing
	 */
	function cancelNewConnection(passedVal)
	{
		if ($('#newConnectionSection').length > 0)
		{
			$('#newConnectionSection').slideToggle().remove();
		}

		if (typeof passedVal == 'undefined')
		{
			$('#connection_id').val(reportConnection).attr('req','true');
		}
			else
			{
				$('#connection_id').val(passedVal);
			}
	}
	
});
