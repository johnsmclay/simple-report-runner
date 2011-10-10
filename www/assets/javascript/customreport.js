// Add page specific JavaScript to this file
$(function() 
{
	
	$('#reportForm').hide();
	
	// This creates the sliding menu animations
	$('#reportList').each(function(){
	 
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
		console.log('called');
		if ($('#reportForm').length > 0)
		{
			
			if ($('input:submit').length > 0)
			{
				$('input:submit').attr('id','submitReportBtn');
			}
			
			// The back button that causes the report list to be shown again
			if ($('#backButton'). length > 0)
			{
				$('#backButton').click(function() {
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
				var values = {};
				
				// First validate that all fields have been filled out correctly
				
				var validation = validateFields(this);
				
				if(validation != false) 
				{
					// Add the report ID to the values object, it is a hidden input
					$.each($('#reportForm :hidden'),function()
					{
						values[this.id] = this.value;
					});
					
					
					// loop through each input and store its value in the values object
					$.each($('#reportForm').serializeArray(),function(i,field) 
					{
						values[field.name] = field.value;
					});
					
	
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
								return false;
							}
								else
								{
									$('#secretIFrame').attr('src',data.url);
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
		var dateRegEx = /^(0?[1-9]|1[012])\/(0?[1-9]|[12][1-9]|3[01])\/20[01][0-9]$/;
		
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
	
	// ---------------------------------//
	//									//
	//		Date Button Functions		//
	//									//
	// ---------------------------------//
	
	// shiftDates
	//
	// Various methods for adjusting date values in text fields,
	// each method returns an object consisting of a beginDate(String) and an endDate(String)
	// @type Object
	var shiftDates = 
	{
		// To obtain the correct month with a 0 indexed offset for the date methods
		monthsArray	: ['01','02','03','04','05','06','07','08','09','10','11','12'],
		// Returns the days in a given month provided with the month and year NOTE: months are 0 indexed i.e. 0=January and 11=December
		getDaysInMonth : function(month,year) 
		{
			return 32 - new Date(year, month, 32).getDate();
		},
		
		// getDifferentMonth
		//
		// Given the direction to go, this function will return begin and end dates formatted as mm/dd/yyyy
		// 
		// @direction String - Acceptable values are 'next' and 'previous'
		// @returns Object: beginDate, endDate formatted as mm/dd/yyyy
		getDifferentMonth : function(direction,month,year) 
		{
			var setDate = new Date(year,month);
			
			switch (direction) 
			{
				case 'next':
					var currentDate = new Date();
					// Check to make sure that requested date range is not in the future (data will not exist for report)
					if (currentDate.getMonth() <= setDate.getMonth() + 1 && currentDate.getFullYear() <= setDate.getFullYear()) 
					{
						var dates = 
						{
							beginDate	: this.getThisMonth().beginDate,
							endDate		: this.getThisMonth().endDate
						}
						
						return dates;
					}
					
					// Calculates correct next month
					var month = setDate.getMonth() == 11 ? 0 : setDate.getMonth() + 1;
									
					// Gets year and accounts for getting the next year if next month is January
				 	var year = (month == 0) ? setDate.getFullYear() + 1 : setDate.getFullYear();	  
					
					break;
				case 'previous':
					// Calculates correct previous month
					var month = setDate.getMonth() - 1 == 0 ? 11 : setDate.getMonth() - 1;
					// Gets year and accounts for getting the previous year if previous month is December
					var year = (month == 11) ? setDate.getFullYear() - 1 : setDate.getFullYear();
					break;
			}
				
		 	var	days = this.getDaysInMonth(month,year); // Get days of given month, within the given year
			
			var dates = 
			{
				beginDate 	: this.monthsArray[month] + '/01/' + year,
				endDate		: this.monthsArray[month] + '/' + days + '/' + year
			};
			
			return dates;
		},
		
		// getThisMonth
		//
		// Gets the current month up to the previous day
		//
		// @returns Object: beginDate, endDate formatted as mm/dd/yyyy
		getThisMonth : function() 
		{
			var date = new Date();
			var previousDay = (date.getDate() - 1) < 10 ? '0' + (date.getDate() - 1) : (date.getDate() - 1); 
			var dates = 
			{
				beginDate 	: this.monthsArray[date.getMonth()] + '/01/' + date.getFullYear(),
				endDate		: this.monthsArray[date.getMonth()] + '/' + previousDay + '/' + date.getFullYear()
			};
			
			return dates;
		},
		
		// getThisQuarter
		//
		// Gets the current quarter up to the previous day
		//
		// @returns Object: beginDate, endDate formatted as mm/dd/yyyy
		getThisQuarter : function() 
		{
			var date = new Date();
			var quarter = Math.floor(date.getMonth() / 3);
			var firstDate = new Date(date.getFullYear(), quarter * 3,1);
			var previousDay = (date.getDate() - 1) < 10 ? '0' + (date.getDate() - 1) : (date.getDate() - 1);
			var dates = 
			{
				beginDate 	: this.monthsArray[firstDate.getMonth()] + '/01/' + firstDate.getFullYear(),
				endDate		: this.monthsArray[date.getMonth()] + '/' + previousDay + '/' + date.getFullYear()
			};
			
			return dates;
		},
		
		// getPreviousQuarter
		//
		// Gets the previous quarter from the date entered into the To: input box
		//
		// @returns Object: beginDate, endDate formatted as mm/dd/yyyy
		getPreviousQuarter : function(month,year)
		{
			var date = new Date(year,month);
			var quarter = Math.floor(date.getMonth() / 3);
			var firstDate = new Date(date.getFullYear(), quarter * 3 - 3,1);
			var finishDate = new Date(firstDate.getFullYear(),firstDate.getMonth() + 3, 0);
			var days = this.getDaysInMonth(finishDate.getMonth(),finishDate.getFullYear());
			var dates = 
			{
				beginDate	: this.monthsArray[firstDate.getMonth()] + '/01/' + firstDate.getFullYear(),
				endDate		: this.monthsArray[finishDate.getMonth()] + '/' + days + '/' + finishDate.getFullYear()
			};
			
			return dates;
		},
		
		// getFiscalYear
		//
		// Gets the current Fiscal year up to the current date 
		// (current date is actually previous day, since no data exists for the current date)
		//
		// @returns Object: beginDate, endDate formatted as mm/dd/yyyy
		getFiscalYear : function() 
		{
			var date = new Date();
			var previousDay = (date.getDate() - 1) < 10 ? '0' + (date.getDate() - 1) : (date.getDate() - 1);
			var month = date.getMonth();
			var year = date.getFullYear();
			if (month >= 0 && month <= 5) {
				var previousYear = date.getFullYear() - 1;
				var firstDate = new Date(previousYear,6,1);
				var dates = 
				{
					beginDate	: this.monthsArray[firstDate.getMonth()] + '/01/' + firstDate.getFullYear(),
					endDate		: this.monthsArray[date.getMonth()] + '/' + previousDay + '/' + date.getFullYear()
				};
				
				return dates;
			}
				else if (month >= 6 && month <= 11) 
				{
					var firstDate = new Date(year,6,1);
					var dates = 
					{
						beginDate	: this.monthsArray[firstDate.getMonth()] + '/01/' + firstDate.getFullYear(),
						endDate		: this.monthsArray[date.getMonth()] + '/' + previousDay + '/' + date.getFullYear()
					};
					
					return dates;
				}
		}
	} // end loadFeatures()
	
});