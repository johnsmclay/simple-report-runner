// Add page specific JavaScript to this file
$(function() 
{
	// IT MAY BE POSSIBLE TO DELETE THIS GLOBAL VAR
	// A few global variables do not pollute the namespace with globals!
	// var months = {
		// '1' : 'January',
		// '2' : 'February',
		// '3' : 'March',
		// '4' : 'April',
		// '5' : 'May',
		// '6' : 'June',
		// '7' : 'July',
		// '8' : 'August',
		// '9' : 'September',
		// '10': 'October',
		// '11': 'November',
		// '12': 'December'
	// }
	
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
	
	//*********************************************************************************************
	// Setup all features to deal with the correct day selection depending on what month is chosen
	//*********************************************************************************************
	$('#month_of_year').live({
		change	: function()
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
				// Get the number of days in the chosen month for the appropriate year
				var numOfDays = shiftDates.getDaysInMonth($(this).val() - 1,date.getFullYear());
			}
				
				// If the default month is selected then show 31 days
				if ($(this).val() == '*')
				{
					for (var t = 0; t < 31; t++)
					{
						$('#day_of_month').append('<option value="' + (t+1) + '">' + (t+1) + '</option>');
					}
					$('#day_of_month').val(selectedDay);
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
				// skip the boolean check for 'exists' if the value is *
				if (selectedDay == '*')
				{
					var exists = true;
				}
					else
					{
						// Check if the previous date selected is still available or not
						var exists = 0 != $('#day_of_month option[value='+selectedDay+']').length;
					}
			
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
				$('#dynamicForm').load('schedulereport/buildForm',post,function(str)
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
					handleScheduleReport();
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
		}
	}
	
	/**
	 * handleScheduleReport
	 * 
	 * Submit function for scheduling the report.
	 */
	function handleScheduleReport()
	{
		// Grab all the values of the form in an Array object
		var values = serializeForm($('#reportForm'));
		
		$('#scheduleReportBtn').hide();
		$('#loaderImg').show();
		
		// Pass the form data via AJAX
		$.ajax(
			{
				url			: 'schedulereport/scheduleIt',
				data		: values,
				type		: 'post',
				dataType	: 'json',
				success		: function(data)
				{
					if (data.status == 'success')
					{
						$('#notices').append('<p class="notice">Your report has been successfully scheduled</p>').delay(2500).fadeOut();
					}
				}
			}
		);
		
		$('#loaderImg').hide();
		$('#scheduleReportBtn').show();
		
		return false;
	}
	
});