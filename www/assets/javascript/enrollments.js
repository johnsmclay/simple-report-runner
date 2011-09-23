$(function(){
	$("#getReportbtn").click(function(){
		enrollmentFunction();
	});
	
	function enrollmentFunction () {
		var dateFrom 	= $('#dateFrom').val()
			dateTo		= $('#dateTo').val();
		$.ajax({
			url		: 'enrollments/getEnrollments',
			type	: 'POST',
			dataType: 'json',
			data	: {},
			success : function(response) {
				if (response.status == true) {
				}
			}
		});
	}
});
