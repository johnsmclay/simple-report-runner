<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * CodeIgniter modelutils Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		M. Clay Johns
 * @link		
 */

// ------------------------------------------------------------------------

/**
 * Check for required fields
 *
 * @access	public
 * @return	bool
 */
if ( ! function_exists('fields_required'))
{
	function fields_required($required,$data)
	{
		foreach($required as $field)
			// make sure it is set
			if(!isset($data[$field])) return false;
			// make sure it's not blank
			if($data[$field] == '') return false;
		
		return true;
	}
}

// ------------------------------------------------------------------------

/**
 * Return MySQL date formatted equivilant of a given PHP date
 * respons similar to MySQL's NOW() function if date is null
 *
 * @access	public
 * @return	string
 */
if ( ! function_exists('mysql_date'))
{
	function mysql_date($date=null)
	{
		date('Y-m-d H:i:s',$date);
	}
}

// ------------------------------------------------------------------------

/* End of file modelutils_helper.php */
/* Location: ./application/helpers/modelutils_helper.php */