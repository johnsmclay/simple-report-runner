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
	function outputCSV($array,$headers=array(),$name=null,$parameters=array())
	{
		$folder = "report_holder/";
		$filename = 'report_' . date('m_d_Y') . '_' . mt_rand(1, 9999) . '.csv';
		$handler = fopen($folder . $filename, 'wb');
		$utf8_bom="\xEF\xBB\xBF";
		fwrite($handler, $utf8_bom);
		
		if(! empty($headers))
		{
			fputcsv($handler,$headers);
		}
		
		foreach($array AS $val)
		{
			fputcsv($handler,$val);
		}
		fclose($handler);
		
		return $folder.$filename;
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
	function createHTMLTable($array, $headers = array(), $limit=null)
	{
		$count = (is_int($limit) && $limit) > 0 ? $limit : null;
		$numHeaders = count($headers);
		$numResults = count($array);

		$html = "<table class='reportTable'>";

		if (!empty($headers))
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
		
		// If a limit has been passed in, limit the rows to that amount
		if (is_int($count) && $count > 0)
		{
			foreach($array AS $arrays)
			{
				if($count > 0)
				{
					$html .= "<tr>";
					foreach ($arrays AS $val)
					{
						$html .= "<td>{$val}</td>";
					}
					$html .= "</tr>";
				}
				--$count;
			}
			
			if ($numResults > $limit)
			{
				$html .= "<tr><td colspan='{$numHeaders}'>The Report returned more results. However this temporary display has been limited to show only {$limit} results. If you want the full report, select the CSV radio button above and run it again.</td></tr>";
			}
		}
			// Otherwise show all rows
			else
			{
				foreach($array AS $arrays)
				{
					$html .= "<tr>";
					foreach ($arrays AS $val)
					{
						$html .= "<td>{$val}</td>";
					}
					$html .= "</tr>";
				}
			}
		$html .= "</tbody></table>";

		return $html;
	}

}
?>
