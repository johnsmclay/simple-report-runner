$(document).ready(function() {
	// get current location from URL
	var url = window.location.pathname;
	// get the final name in the url path
	var filename = url.substring(url.lastIndexOf('/') + 1);

	// hack for getting the main page to highlight
	if(filename == '')
		filename = 'customreport';

	highlight(filename);

	function highlight(filename) {
		// Navigation link highlighter
		$('#sidenav a').each(function() {// Loop through each nav link to highlight the current link
			var href = $(this).attr('href');
			var link = href.substring(href.lastIndexOf('/') + 1);
			// Get last word after final /
			if(link == filename) {
				$(this).addClass('active');
				// Highlight current page link
			}
		});
	}

	// Set up buttons to use jQuery UI
	$('input:button, input:submit').button();

	if($('#dateButtons').length > 0) {
		$('#dateButtons').css('display', 'none');
		$('#dateButtons').show();
	}

	////////////////////////////
	//						  //
	//  On page load methods  //
	//						  //
	////////////////////////////

	// Set date fields on Enrollments page to use jQuery UI Datepicker widget
	if($('#dateFrom').length > 0) {
		$('#dateFrom').datepicker({
			changeMonth : true,
			changeYear : true,
			showButtonPanel : true
		});

		$('#dateTo').datepicker({
			changeMonth : true,
			changeYear : true,
			showButtonPanel : true
		});
	}

	// Hide iFrame that will allow dynamic download AJAX hack
	if($('#secretIFrame').length > 0)
		$('#secretIFrame').css({
			'display' : 'none',
			'visibility' : 'hidden'
		});

});
// ---------------------------------//
//									//
//	  Date Adujstment Functions		//
//									//
// ---------------------------------//

// shiftDates
//
// Various methods for adjusting date values in text fields,
// each method returns an object consisting of a beginDate(String) and an endDate(String)
// @type Object
var shiftDates = {
	// To obtain the correct month with a 0 indexed offset for the date methods
	monthsArray : ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'],
	// Returns the days in a given month provided with the month and year NOTE: months are 0 indexed i.e. 0=January and 11=December
	getDaysInMonth : function(month, year) {
		return 32 - new Date(year, month, 32).getDate();
	},
	// getDifferentMonth
	//
	// Given the direction to go, this function will return begin and end dates formatted as mm/dd/yyyy
	//
	// @direction String - Acceptable values are 'next' and 'previous'
	// @returns Object: beginDate, endDate formatted as mm/dd/yyyy
	getDifferentMonth : function(direction, month, year) {
		var setDate = new Date(year, month);

		switch (direction) {
			case 'next':
				var currentDate = new Date();
				// Check to make sure that requested date range is not in the future (data will not exist for report)
				if(currentDate.getMonth() <= setDate.getMonth() + 1 && currentDate.getFullYear() <= setDate.getFullYear()) {
					var dates = {
						beginDate : this.getThisMonth().beginDate,
						endDate : this.getThisMonth().endDate
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

		var days = this.getDaysInMonth(month, year);
		// Get days of given month, within the given year

		var dates = {
			beginDate : this.monthsArray[month] + '/01/' + year,
			endDate : this.monthsArray[month] + '/' + days + '/' + year
		};

		return dates;
	},
	// getThisMonth
	//
	// Gets the current month up to the previous day
	//
	// @returns Object: beginDate, endDate formatted as mm/dd/yyyy
	getThisMonth : function() {
		var date = new Date();
		var dates = {
			beginDate : this.monthsArray[date.getMonth()] + '/01/' + date.getFullYear(),
			endDate : this.monthsArray[date.getMonth()] + '/' + (date.getDate() - 1) + '/' + date.getFullYear()
		};

		return dates;
	},
	// getThisQuarter
	//
	// Gets the current quarter up to the previous day
	//
	// @returns Object: beginDate, endDate formatted as mm/dd/yyyy
	getThisQuarter : function() {
		var date = new Date();
		var quarter = Math.floor(date.getMonth() / 3);
		var firstDate = new Date(date.getFullYear(), quarter * 3, 1);
		var dates = {
			beginDate : this.monthsArray[firstDate.getMonth()] + '/01/' + firstDate.getFullYear(),
			endDate : this.monthsArray[date.getMonth()] + '/' + (date.getDate() - 1) + '/' + date.getFullYear()
		};

		return dates;
	},
	// getPreviousQuarter
	//
	// Gets the previous quarter from the date entered into the To: input box
	//
	// @returns Object: beginDate, endDate formatted as mm/dd/yyyy
	getPreviousQuarter : function(month, year) {
		var date = new Date(year, month);
		var quarter = Math.floor(date.getMonth() / 3);
		var firstDate = new Date(date.getFullYear(), quarter * 3 - 3, 1);
		var finishDate = new Date(firstDate.getFullYear(), firstDate.getMonth() + 3, 0);
		var days = this.getDaysInMonth(finishDate.getMonth(), finishDate.getFullYear());
		var dates = {
			beginDate : this.monthsArray[firstDate.getMonth()] + '/01/' + firstDate.getFullYear(),
			endDate : this.monthsArray[finishDate.getMonth()] + '/' + days + '/' + finishDate.getFullYear()
		};

		return dates;
	},
	// getFiscalYear
	//
	// Gets the current Fiscal year up to the current date
	// (current date is actually previous day, since no data exists for the current date)
	//
	// @returns Object: beginDate, endDate formatted as mm/dd/yyyy
	getFiscalYear : function() {
		var date = new Date();
		var month = date.getMonth();
		var year = date.getFullYear();
		if(month >= 0 && month <= 5) {
			var previousYear = date.getFullYear() - 1;
			var firstDate = new Date(previousYear, 6, 1);
			var dates = {
				beginDate : this.monthsArray[firstDate.getMonth()] + '/01/' + firstDate.getFullYear(),
				endDate : this.monthsArray[date.getMonth()] + '/' + (date.getDate() - 1) + '/' + date.getFullYear()
			};

			return dates;
		} else if(month >= 6 && month <= 11) {
			var firstDate = new Date(year, 6, 1);
			var dates = {
				beginDate : this.monthsArray[firstDate.getMonth()] + '/01/' + firstDate.getFullYear(),
				endDate : this.monthsArray[date.getMonth()] + '/' + (date.getDate() - 1) + '/' + date.getFullYear()
			};

			return dates;
		}
	}
};
