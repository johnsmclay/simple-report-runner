<?php
class MilTeachers {
	
 	// An array of all MIL Teachers and all of their user id's
	var $milTeachers = array(
		'Heather Beaty' => '29017',
		'Theresa Bruns' => '210797',
		'Emybi Caballero' => '166064,166058,166052,164444,164436,109006,108639,82158',
		'Charlene Chung' => '172174,99467',
		'Deirdre Erb' => '209953,172173,90526,43570',
		'David Fisher' => '99466',
		'Mary Flanagan' => '205440',
		'Jennifer Fraser' => '205114',
		'Steve Garlick' => '172172,107647,146645,29024',
		'Rebecca Gimenez' => '177333',
		'Lisa Gustinelli' => '80337',
		'Weiwei Han' => '179256',
		'Susan Lafky' => '160447',
		'Anne LaMonica' => '209270',
		'Timothy Mitchell' => '130858,70460',
		'Stephanie Segretto' => '29378',
		'Monica Shang' => '176236',
		'Jocelyne Waddle' => '213063,214813',
		'Simon Wang' => '177437',
		'Rachel Woolley' => '178580,29020',
	);
	
	var $milTeachersWithLanguages = array(
		'Spanish' => array(
			'Heather Beaty' => '29017',
			'Theresa Bruns' => '210797',
			'Emybi Caballero' => '166064,166058,166052,164444,164436,109006,108639,82158',
			'Jennifer Fraser' => '205114',
			'Rebecca Gimenez' => '177333',
			'Timothy Mitchell' => '130858,70460',
			'Jocelyne Waddle' => '213063,214813'
			
		),
		'French' => array(
			'Mary Flanagan' => '205440',
			'Lisa Gustinelli' => '80337',
			'Stephanie Segretto' => '29378',
			'Rachel Woolley' => '178580,29020'
		),
		'German' => array(
			'Susan Lafky' => '160447',
			'Steve Garlick' => '172172,107647,146645,29024'
		),
		'Latin' => array(
			'David Fisher' => '99466',
			'Deirdre Erb' => '209953,172173,90526,43570',
			'Anne LaMonica' => '209270'
		),
		'Chinese' => array(
			'Charlene Chung' => '172174,99467',
			'Weiwei Han' => '179256',
			'Monica Shang' => '176236',
			'Simon Wang' => '177437'
		)
	);
	
	// If you have questions why these teachers are excluded from these schools see Michelle.
	// This array is not currenlty being implemented in any report script as of 07SEP11 -ahaymond
	var $specialCaseTeachers = array(
		'Steve Garlick' => '172172,107647,146645,29024', // should not be included if section is under school ID 11 or 150
		'Stephanie Segretto' => '29378', // should not be included if section is under school ID 11 or 150
		'Deirdre Erb' => '209953,172173,90526,43570' // should not be included if section is under school ID 185 
	);
	
	
	// TODO: TUESDAY YOU NEED TO FINISH SETTING UP THE SPECIAL CASE TEACHERS IN THE TEACHER PAY REPORT
	// 			ACCORDING TO THE SCHOOL IDS LOCATED IN MICHELLE'S INVOICING QUERY!
}
?>