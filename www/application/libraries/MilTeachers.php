<?php
class MilTeachers {
	
 	// An array of all MIL Teachers and all of their user id's
	var $milTeachers = array(
	);
	
	var $milTeachersWithLanguages = array(
		'Spanish' => array(
		),
		'French' => array(
		),
		'German' => array(
		),
		'Latin' => array(
		),
		'Chinese' => array(
		)
	);
	
	// If you have questions why these teachers are excluded from these schools see Michelle.
	// This array is not currenlty being implemented in any report script as of 07SEP11 -ahaymond
	var $specialCaseTeachers = array(
		// should not be included if section is under school ID 11 or 150
		// should not be included if section is under school ID 11 or 150
		// should not be included if section is under school ID 185 
	);
	
	
	// TODO: TUESDAY YOU NEED TO FINISH SETTING UP THE SPECIAL CASE TEACHERS IN THE TEACHER PAY REPORT
	// 			ACCORDING TO THE SCHOOL IDS LOCATED IN MICHELLE'S INVOICING QUERY!
}
?>
