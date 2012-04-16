<?php
if ( !defined('BASEPATH'))
	exit('No direct script access allowed');

// To Load:
// $this->load->helper('report_helper');

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
	function outputCSV($array,$headers=array(),$preface=null)
	{
		$folder = "report_holder/";
		$filename = 'report_' . date('m_d_Y') . '_' . mt_rand(1, 9999) . '.csv';
		$handler = fopen($folder . $filename, 'wb');
		$utf8_bom="\xEF\xBB\xBF";
		fwrite($handler, $utf8_bom);
		
		if(! is_null($preface))
		{
			fwrite($handler,$preface);
		}

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
				$html .= "<tr><td colspan='{$numHeaders}'>The Report returned more results. However this temporary display is limited to {$limit} rows. If you want the full report, select the CSV button above and run it again.</td></tr>";
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


if (! function_exists('sendEmailReport'))
{
	function sendEmailReport($to_address,$subject,$html,$attachments,$print_debug=FALSE)
	{
		$CI =& get_instance();

		echo "Sending report email to $to_address"."<br/>\n";

		$cc_list = array(
			
		);

		$CI->load->library('email');
		$CI->load->helper('email');

		$config['mailtype'] = 'html';

		if(!valid_email($to_address))
		{
			echo "Invalid Email address -- ".$to_address." -- skipping"."<br/>\n";
			return true;
		}

		$CI->email->clear();
		$CI->email->from('reports@middil.com', 'Reporting Daemon');
		$CI->email->reply_to('bgaunce@middleburyinteractive.com', 'Beth Gaunce');
		$CI->email->to($to_address); 
		$CI->email->cc($cc_list); 

		$CI->email->subject($subject);
		$CI->email->message($html);
		print_r($attachments);//debug	
		if(!is_null($attachments))
		{
			foreach($attachments as $attachment)
			{
				if(file_exists($attachment)) $CI->email->attach($attachment);
			}
		}
		$CI->email->send();

		echo 'email sent'."<br/>\n";//debug

		if($print_debug)
		{
			echo $CI->email->print_debugger();
		}

		$CI->email->clear(TRUE);
	}
}
?>
