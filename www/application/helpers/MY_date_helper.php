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

 
/**
 * Get Months
 *
 * Returns an array of months with their numerical value as the array index
 *
 * @access	public
 * @param	bool $shorten Choose whether return values should be full name or 3 letter month names 
 * @return	array
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
			$months = substr(strtoupper($months),0,2);
		}
	}
	
	return $months;
 }
 
/**
 * Get Hours
 *
 * Returns an array of hours of the day with their 24hr equivalent as the index key
 *
 * @access	public
 * @return	array
 */
 function getHours()
 {
 	$hours = array(
		"0" => "12:00 AM",
		"1" => "1:00 AM",
		"2" => "2:00 AM",
		"3" => "3:00 AM",
		"4" => "4:00 AM",
		"5" => "5:00 AM",
		"6" => "6:00 AM",
		"7" => "7:00 AM",
		"8" => "8:00 AM",
		"9" => "9:00 AM",
		"10" => "10:00 AM",
		"11" => "11:00 AM",
		"12" => "12:00 AM",
		"13" => "1:00 PM",
		"14" => "2:00 PM",
		"15" => "3:00 PM",
		"16" => "4:00 PM",
		"17" => "5:00 PM",
		"18" => "6:00 PM",
		"19" => "7:00 PM",
		"20" => "8:00 PM",
		"21" => "9:00 PM",
		"22" => "10:00 PM",
		"23" => "11:00 PM",
	);
	
	return $hours;
 }
?>