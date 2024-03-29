$(function()
{
	if ($('#loaderImg').length > 0)
	{
		$('#loaderImg').hide();
	}
	
	$('#pglmsWeekliesForm').submit(function()
	{
		submitForm($(this));
		return false;
	});
	
	// this class is used to shrink the default size of the jQuery ui button widget which is applied righ after this
	$('input:button, input:submit').addClass('shrinkButton');
	$('input:button, input:submit').button();
	
	// The back button that causes the report list to be shown again
	if ($('#backButton'). length > 0)
	{
		$('#backButton').click(function() {
			$('#htmlTable table').remove()
			$('#reportList').show(500);
			$('#reportForm').hide(500);
		});
	}
	
	if($('#dateFrom').length > 0) 
	{
		// Set date fields on Enrollments page to use jQuery UI Datepicker widget
		$('#dateFrom').datepicker(
		{
			changeMonth: true,
			changeYear: true,
			showButtonPanel: true
		});
		
		$('#dateTo').datepicker(
		{
			changeMonth: true,
			changeYear: true,
			showButtonPanel: true
		});
		
		//set date from and date to fields to be the current quarter
		$('#dateFrom').val(shiftDates.getThisQuarter().beginDate);
		$('#dateTo').val(shiftDates.getThisQuarter().endDate);
		
		$('#dateButtonsGroup').css('display','none');
		
		// this class is used to shrink the default size of the jQuery ui button widget which is applied righ after this
		$('#reportForm input:button, input:submit').addClass('shrinkButton');
		$('#reportForm input:button, #reportForm input:submit').button();
		$('#dateButtonsGroup').show();
	}
	
	
	//////////////////////////////////////////////
	//											//
	//		 Date Button Event Listeners		//
	//											//
	//////////////////////////////////////////////
	if ($('#previousMonth').length > 0) 
	{
		$('#previousMonth').click(function() 
		{
			if($('#dateFrom').val() != '') 
			{
				var date = $('#dateFrom').val();
				var month = date.substr(0,2) - 1;
				var year = date.substr(6,4);
			}
			$('#dateFrom').val(shiftDates.getDifferentMonth('previous',month,year).beginDate);
			$('#dateTo').val(shiftDates.getDifferentMonth('previous',month,year).endDate);
		});
		
		$('#nextMonth').click(function() 
		{
			if($('#dateFrom').val() != '') 
			{
				var date = $('#dateFrom').val();
				var month = date.substr(0,2) - 1;
				var year = date.substr(6,4);
			}
			$('#dateFrom').val(shiftDates.getDifferentMonth('next',month,year).beginDate);
			$('#dateTo').val(shiftDates.getDifferentMonth('next',month,year).endDate);
		});
		
		$('#thisMonth').click(function() 
		{
			$('#dateFrom').val(shiftDates.getThisMonth().beginDate);
			$('#dateTo').val(shiftDates.getThisMonth().endDate);
		});
		
		$('#thisQuarter').click(function() 
		{
			$('#dateFrom').val(shiftDates.getThisQuarter().beginDate);
			$('#dateTo').val(shiftDates.getThisQuarter().endDate);
		});

		$('#previousQuarter').click(function() 
		{
			if($('#dateFrom').val() != '') {
				var date = $('#dateFrom').val();
				var month = date.substr(0,2) - 1;
				var year = date.substr(6,4);
			}
			$('#dateFrom').val(shiftDates.getPreviousQuarter(month,year).beginDate);
			$('#dateTo').val(shiftDates.getPreviousQuarter(month,year).endDate);
		});
		
		$('#fiscalYear').click(function() 
		{
			$('#dateFrom').val(shiftDates.getFiscalYear().beginDate);
			$('#dateTo').val(shiftDates.getFiscalYear().endDate);
		});
	}
});


function submitForm(form)
{
	var values = serializeForm($(form));
	
	$('#pglmsSubmit').hide();
	$('#loaderImg').show();
	
	$.ajax(
	{
		url			: 'pglmsweeklies/requestReport',
		dataType	: 'json',
		type		: 'post',
		data		: values,
		asynch		: false,
		success		: function(data)
		{
			if (data.results == false)
			{
				if($('#htmlTable').children().length > 0)
				{
					console.log('should not be here');
					$('#htmlTable').children().each(function()
					{
						$(this).remove();
					});
				}
				
				// Display an error message to the user letting them know 
				$('#errorModal').text('').append('<p>The report you requested returned no results for the selected school and date range.</p>').dialog(
				{
					modal			: true,
					closeOnEscape	: true,
					draggable		: false,
					position		: ['center',200],
					resizable		: false,
					title			: 'No Information Found',
					minHeight		: 20,
					maxHeight		: 100
				});
				
				$('#pglmsSubmit').show();
				$('#loaderImg').hide();
				
				return false;
			}
				else if (data.results == true)
				{
					// Set the iFrame source attribute to download the generated Excel file
					$('#secretIFrame').attr(
					{ 
						src	: data.base + data.file
					});
					
					$('#pglmsSubmit').show();
					$('#loaderImg').hide();
				}
		}
	});
	
}
