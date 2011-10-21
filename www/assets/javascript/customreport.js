// Add page specific JavaScript to this file
$(function() 
{
	// A few global variables do not pollute the namespace with globals!
	months = {
		'1' : 'January',
		'2' : 'February',
		'3' : 'March',
		'4' : 'April',
		'5' : 'May',
		'6' : 'June',
		'7' : 'July',
		'8' : 'August',
		'9' : 'September',
		'10': 'October',
		'11': 'November',
		'12': 'December'
	}
	//////////////////////////////////
	//								//
	//		On Page Load Events		//
	//								//
	//////////////////////////////////
	
	
	if ($('#reportForm').length > 0)
	{
		$('#reportForm').hide();
	}
	
	if ($('#scheduleFormBlock').length > 0)
	{
		$('#scheduleFormBlock').hide();
	}
	
	// highlight table rows on hover
	$('tr').live('mouseover mouseout',function(event)
	{
		if (event.type == "mouseover")
		{
			$(this).children().each(function()
			{
				$(this).addClass('change');
			})
		}
			else
			{
				$(this).children().each(function()
				{
					$(this).removeClass('change');
				});
			}
	});
	
	// This creates the sliding menu animations for the report list
	$('#reportList').each(function()
	{
	 
		var e = $(this).attr('id');
		var opened = /opened\.png/;
		var collapsed = /collapsed\.png/;
		 
		$('#'+e+' li > ul').each(function(i) {
		   	var parent_li = $(this).parent('li');
		   	var sub_ul = $(this).remove();
		    
		   // Create 'a' tag for parent if it does not exist
			if (parent_li.children('a').not_exists()) {
				parent_li.wrapInner('<a/>');
		    }
		    
		    parent_li.find('a').addClass('jqcNode').css('cursor','pointer').click(function() {
		    	if (collapsed.test($(this).parent().attr('style')) == true || $(this).parent().attr('style') == undefined)
		    	{
	        		$(this).parent().attr('style','background: url("assets/images/opened.png") 0px 8px no-repeat');
	        	}
	        		else {
		        		$(this).parent().attr('style','background: url("assets/images/collapsed.png") 0px 8px no-repeat');
	        		}
	        		
	        	sub_ul.toggle();
				animateItems(sub_ul);
		    });
		    parent_li.append(sub_ul);
		    
		});
		
		//Hide all sub-lists
		 $('#'+e+' ul').hide();
	});
	
	//////////////////////////////////
	//								//
	//			FUNCTIONS			//
	//								//
	//////////////////////////////////

	// Cycle through each line item to add animation and click event listeners. Each
	// line item represents a single report.
	function animateItems(element) 
	{
		$(element).children().each(function() 
		{
			// Animated user feedback for each line item
			$(this).hover(function() 
			{
				$(this).stop().animate(
					{
						marginLeft	: "20px",
						marginRight	: "-20px",
						borderRight : "3px solid #000000",
						borderLeft : "3px solid #000000"
					}, 100);
			}, function ()
			{
				$(this).stop().animate(
					{
						marginLeft	: "0",
						marginRight	: "0",
						border		: "none"
					}, 100);
			});
			
			// Click functions for each line item
			$(this).click(function() 
			{
				var post = {};
				post['report_name'] = $(this).children().first().text();
				post['report_id'] = $(this).children().first().attr('id');
				
				// Load the dynamic form into the dynamicForm div
				$('#dynamicForm').load('customreport/buildForm',post,function(str)
				{
					$('#reportForm #report').text(post.report_name);
					$('#reportList').stop(true).hide();
					$('#reportForm').stop(true).show();
					
					// this had to be placed here as well otherwise some reports did not get the button styling
					$('#reportForm input:button, input:submit').addClass('shrinkButton');
					$('#reportForm input:button, #reportForm input:submit').button();
					loadFeatures();
				});
	
			});
		});
	}
	
	
	
	
	// loadFeatures
	//
	// These are event listeners and UI widgets that normally would be loaded on page load,
	// However since the elements are now created dynamically they must be called after the element
	// is available to act upon. 
	function loadFeatures() 
	{
		if ($('#loaderImg').length > 0)
		{
			var submitWidth = $('#submitReportBtn').width();
			var imgWidth = $('#loaderImg').width();
			var calculatedMargin = Math.floor((submitWidth - imgWidth)/2);
			
			$('#loaderImg').css(
				{
					width 			: submitWidth + 'px',
					paddingLeft		: parseInt($('#submitReportBtn').css('paddingLeft')) +  parseInt($('#submitReportBtn').css('borderLeftWidth')),
					paddingRight	: parseInt($('#submitReportBtn').css('paddingRight')) + parseInt($('#submitReportBtn').css('borderRightWidth'))
				}
			);
			
			$('#loaderImg').hide();
		}
		
		if ($('#reportForm').length > 0)
		{
			
			if ($('#scheduleReportBtn').length > 0)
			{
				$('#scheduleReportBtn').click(function()
				{
					createScheduleView();
				})
			}
			
			if ($('input:submit').length > 0)
			{
				$('#reportForm input:submit').attr('id','submitReportBtn');
			}
			
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
			
			//////////////////////////////
			//							//
			//	   Form Submission		//
			//							//
			//////////////////////////////
			$('#reportForm').submit(function() 
			{
				// First validate that all fields have been filled out correctly
				
				var validation = validateFields(this);
				
				if(validation != false) 
				{
					// Get form values
					var values = serializeForm($('#reportForm'));
					
					$('#submitReportBtn').hide();
					$('#loaderImg').show();
					// Request the report to run
					$.ajax({
						url			: 'customreport/processReport',
						type		: 'POST',
						data		: values,
						dataType	: 'json',
						success		: function(data) 
						{
							if (data.status == 'failed')
							{
								if($('#htmlTable').children().length > 0)
								{
									console.log('should not be here');
									$('#htmlTable').children().each(function()
									{
										$(this).remove();
									});
								}
								$('#errorModal').text('').append('<p>The report you requested returned no results.</p>').dialog(
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
								
								$('#submitReportBtn').show();
								$('#loaderImg').hide();
								
								return false;
							}
								else if(data.type == 'csv')
								{
									$('#secretIFrame').attr('src', data.url);
									
									$('#submitReportBtn').show();
									$('#loaderImg').hide();
								}
									else if(data.type == 'html')
									{
										if($('#htmlTable').children().length > 0)
										{
											$('#htmlTable').children().each(function()
											{
												$(this).remove();
											});
										}
										$('#htmlTable').append(data.htmlTable);
										var div_width = $('#htmlTable').width();
										
										// Create scrollable table 600px in height
										$('.reportTable').scrollbarTable(600);
										
										$('#submitReportBtn').show();
										$('#loaderImg').hide();
									}
						}
					});
				}
				
				// Keep the form from actually submitting
				return false;
			});
		}
	}
	
	// form validation
	function validateFields(form) 
	{
		var numRegEx = /^[0-9]*$/;
		var dateRegEx = /^(0?[1-9]|1[012])\/(0?[1-9]|[12][0-9]|3[01])\/20[01][0-9]$/;
		
		// Iterate through all form elements and test their value for the correct data type
		$('#' + $(form).attr('id') + ' :input').each(function()
		{
			var id = $(this).attr('id');
			
			if($(this).attr('valtype') == 'integer') {
				
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
						// Did not pass validation
						return false;
					}
			}
			
			if($(this).attr('valtype') == 'datetime') 
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
						
						// Did not pass validation
						return false;
					}
			}
		});
		
		// Passed validation!
		return true;
	}
	
	function createScheduleView()
	{
		// Since slide toggle is being used this function is called even when hiding the report
		// therefore we must delete any forms in the div before creating it so that only one form will always show
		if($('#scheduleFormBlock').children().length > 0)
		{
			$('#scheduleFormBlock').children().remove();
		}
		
		// load the form data and append it to the div
		$.ajax(
		{
			url			: 'customreport/loadScheduleReport',
			type		: 'POST',
			dataType	: 'json',
			success		: function(response)
			{
				$('#scheduleFormBlock').append(response.html);
				
					for (var t = 0; t < 31; t++)
					{
						$('#day_of_month').append('<option value="' + (t+1) + '">' + (t+1) + '</option>');
					}
				
				// Load any features needed before displaying the form
				loadFeatures();
				
				// display the form
				$('#scheduleFormBlock').slideToggle(300);
			}
		});
		
		$('#scheduleReportForm').live({
			submit	: function()
			{
				var scheduleData = serializeForm($('#scheduleReportForm'));
				var reportData = serializeForm($('#reportForm'));
				var data = {
					'schedule' 	: scheduleData,
					'report'	: reportData
				};
				
				$.ajax(
				{
					url			: 'schedule_report/scheduleIt',
					type		: 'post',
					data		: data,
					dataType	: 'json',
					success		: function(data)
					{
						
					}
				});
				return false;
			}
		});
		
		// attach a change listener to remove the error class from select lists if they exist
		$('#day_of_month').live({
			change	: function()
			{
				if($(this).hasClass('selectError'))
				{
					$(this).removeClass('selectError');
				}
			}
		});
		
		//*********************************************************************************************
		// Setup all features to deal with the correct day selection depending on what month is chosen
		//*********************************************************************************************
		$('#month_of_year').live({
			change :function()
			{
				// Remember what day was selected when the month was changed
				var selectedDay = $('#day_of_month').val();
				var date = new Date(); // A date object for comparing month and days
				
				// Remove all options except the default
				$('#day_of_month').children().each(function()
				{
					if (!$(this).hasClass('default'))
					{
						$(this).remove();
					}
				});
				
				
				// Append the correct number of days (options) depending on which month was chosen
				if($(this).val() < (date.getMonth() + 1))
				{
					// Get the number of days in the chosen month for the appropriate year (if month has already passed set year to next year)
					var numOfDays = shiftDates.getDaysInMonth($(this).val() - 1, date.getFullYear() + 1);
				}
					else
					{
						var numOfDays = shiftDates.getDaysInMonth($(this).val() - 1, date.getFullYear());
					}
					
					// If the default month is selected then show 31 days
					if ($(this).val() == '*')
					{
						for (var t = 0; t < 31; t++)
						{
							$('#day_of_month').append('<option value="' + (t+1) + '">' + (t+1) + '</option>');
						}
					}
						// Otherwise show however many days there are in the month chosen
						else 
						{
							for (var i = 1; i - 1 < numOfDays; i++)
							{
								$('#day_of_month').append('<option value="' + i + '">' + i + '</option>');
							}
						}
				
				// If the default month is not chosen, check to see if the chosen date still exists in the new month selected
				if ($(this).val() !== '*')
				{	
					// Check if the previous date selected is still available or not
					var exists = 0 != $('#day_of_month option[value='+selectedDay+']').length;
				
					// if the date exists, set it
					if(exists)
					{
						$('#day_of_month').val(selectedDay);
					}
						// if it does not, warn the user
						else
						{
							$('#errorModal').text('').append('<p>' + months[$('#month_of_year').val()] + ' does not have ' + selectedDay +' days, please select a new day of the month</p>').dialog(
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
										$('#day_of_month').addClass('selectError');
									}
								});
						}
				}
			}
		});
	}
	
});