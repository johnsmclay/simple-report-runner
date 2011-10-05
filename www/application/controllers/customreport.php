<?php
class Customreport extends CI_Controller {
		
	function __construct() {
		parent::__construct();
		$this->load->helper('form');
	}
	
	function index() {
		$db1 = $this->load->database('local',TRUE);
		
		$reportListQuery = 
			'
			SELECT
				report.id as id,
				report.display_name,
				(SELECT rc.title FROM report_category rc WHERE rc.id = report.category_id) AS category
			FROM 
				report
			';
		$reportListResult = $db1->query($reportListQuery);
		foreach($reportListResult->result_array() AS $row) {
			$reportList[$row['category']][] = array(
				'id' 			=> $row['id'],
				'display_name' 	=> $row['display_name']
			);
		}
		
		$view_data['reportList'] = $reportList;
		
		// Allows you to name an individual JavaScript file to be loaded for this page.
		// Just provide the name of the file, without the .js extension. Then create the 
		// file in the 'assets/javascript' folder located in the root of the codeIgniter folder
		$view_data['javascript'] = 'customreport';
		$this->load->view('customreport_view',$view_data);
	}

	
	function buildForm() 
	{
		$db1 = $this->load->database('local',TRUE);
		$db2 = $this->load->database('temp',TRUE);
		
		$reportId = $_POST['report_id'];
		
		$reportVarsQuery = 
			"
			SELECT
				*
			FROM
				report_variable
			WHERE
				report_id = {$_POST['report_id']}
			";
			
		$reportVarsResult = $db1->query($reportVarsQuery);
		foreach($reportVarsResult->result_array() AS $row) 
		{
			if (! empty($row['options_query']))
			{
				$optionsResult = $db2->query($row['options_query']);
				foreach($optionsResult->result_array() AS $optRow)
				{
					$options[$optRow['id']] = $optRow['description'];
				}
			}
				else
				{
					$options = NULL;
				}
			$report_vars[] = array(
				'text_identifier' 	=> $row['text_identifier'],
				'variable_type' 	=> $row['variable_type'],
				'default_value'		=> $row['default_value'],
				'display_name'		=> $row['display_name'],
				'description'		=> $row['description'],
				'options'			=> $options
			);
		}

		$view_data['report_vars'] = $report_vars;
		
		$this->load->view('dynamic_form_view',$view_data);
	}

	function test($exit=false) {
		$this->show($_POST,$exit);
	}
	
	
}
?>