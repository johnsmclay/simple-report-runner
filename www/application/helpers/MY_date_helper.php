<?php
/**
 * CodeIgniter MY Date Helpers
 *
 * @package		Helpers
 * @subpackage	MY Helpers
 * @category	Helpers
 * @author		Adam Haymond
 * @extends 
 */

 function getMonths($shorten=false)
 {
 	$months = array(
		'1' => 'January',
		'2' => 'February',
		'3' => 'March',
		'4' => 'April',
		'5' => 'May',
		'6' => 'June',
		'7' => 'July',
		'8' => 'August',
		'9' => 'September',
		'10' => 'October',
		'11' => 'November',
		'12' => 'December'
	);
	
	if ($shorten)
	{
		foreach($months AS &$months)
		{
			$months = substr(strtoupper($months), 0,3);
		}
	}
	
	return $months;
 }
 
 function getHours()
 {
 	// $hours = array(
		// "0"
	// );
 }
?>