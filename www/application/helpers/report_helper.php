<?php
if ( !defined('BASEPATH'))
	exit('No direct script access allowed');

/**
 * CodeIgniter Report Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Adam Haymond
 * @link
 */

/**
 * outputCSV
 * 
 * Creates the CSV file when passed an array of information retrieved
 * via the report database query.
 * 
 * @access public
 * @param array $array The array of data to be encoded into a CSV file
 */
if (! function_exists('outputCSV'))
{
	function outputCSV($array)
	{
		$filename = 'report_' . date('m_d_Y') . '_' . mt_rand(1, 9999) . '.csv';
		$handler = fopen("report_holder/" . $filename, 'wb');
		$utf8_bom="\xEF\xBB\xBF";
		fwrite($handler, $utf8_bom);
		foreach($array AS $val)
		{
			fputcsv($handler,$val);
		}
		fclose($handler);
		
		return $filename;
	}
}
 
/**
 * Check for required fields
 *
 * @access	public
 * @return	string HTML table text
 */
if (! function_exists('createHTMLTable'))
{
	function createHTMLTable($array, $headers = array())
	{

		$html = "<table>";

		if ( !empty($headers))
		{
			$html .= "<thead>";
			$html .= "<tr>";

			foreach ($headers AS $header)
			{
				$html .= "<th>{$header}</th>";
			}

			$html .= "</tr></thead>";
		}

		$html .= "<tbody>";

		foreach ($array AS $arrays)
		{
			$html .= "<tr>";
			foreach ($arrays AS $val)
			{
				$html .= "<td>{$val}</td>";
			}
			$html .= "</tr>";
		}
		$html .= "</tbody></table>";

		return $html;
	}

}
?>
