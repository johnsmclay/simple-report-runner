$(function() {
	$('#teacherLoader').removeClass('hideLoader').addClass('showLoader');
	
	$.ajax({
		url			: 'teachers/getTeachers',
		type		: 'POST',
		dataType	: 'json',
		success		: function(response) {
			$('#teacherLoader').removeClass('showLoader').addClass('hideLoader');
			
			if (response.success == true) {
				$.each(response.teachers, function(teacher,id) {
					$('#teacherList').append($("<option></option>").attr('value',id).text(teacher));
				});
			}
		}
	});
});
