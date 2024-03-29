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
		{
			
			$array_data = $data;
			if(is_object($data))
			{
				$array_data = get_object_vars($data);
			}
			
			log_message('debug', __METHOD__.' Data: '.json_encode($array_data).'  --  Required: '.json_encode($required));

			// make sure it is set
			if(!isset($array_data[$field])) return false;
			// make sure it's not blank
			if($array_data[$field] == '') return false;
		}
			
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
		if($date) return date('Y-m-d H:i:s',$date);
		return date('Y-m-d H:i:s');
	}
}

// ------------------------------------------------------------------------



/* End of file modelutils_helper.php */
/* Location: ./application/helpers/modelutils_helper.php */