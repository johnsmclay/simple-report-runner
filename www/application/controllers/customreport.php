<?php
	class Customreport extends CI_Controller {

		var $filename = '';

		function __construct()
		{
			parent::__construct();
			$this->load->helper('form');
		}

		function index()
		{
			$db1 = $this->load->database('application',TRUE);

			$reportListQuery = '
			SELECT
				report.id as id,
				report.display_name,
				(SELECT rc.title FROM report_category rc WHERE rc.id = report.category_id) AS category
			FROM 
				report
			';
			$reportListResult = $db1->query($reportListQuery);
			foreach ($reportListResult->result_array() AS $row)
			{
				$reportList[$row['category']][] = array(
					'id' => $row['id'],
					'display_name' => $row['display_name']
				);
			}
			$reportListResult->free_result();

			$view_data['reportList'] = $reportList;

			// Allows you to name an individual JavaScript file to be loaded for this page.
			// Just provide the name of the file, without the .js extension. Then create the
			// file in the 'assets/javascript' folder located in the root of the codeIgniter folder
			$view_data['javascript'] = 'customreport';
			$this->load->view('customreport_view', $view_data);
		}

		// buildForm
		//
		// Called via AJAX request, it is sent an integer representing
		// the report ID of the requested report. After querying the DB
		// for the associated variables for that report ID it returns an
		// array of report variables which is then used in the view to
		// dynamiccaly build out the for for the report.
		function buildForm()
		{
			$db1 = $this->load->database('application', TRUE);
			$db2 = $this->load->database('pglms', TRUE);

			$reportId = $_POST['report_id'];

			// Get the report variables for the given report ID
			$reportVarsQuery = "
			SELECT
				*
			FROM
				report_variable
			WHERE
				report_id = {$_POST['report_id']}
			";

			$reportVarsResult = $db1->query($reportVarsQuery);
			foreach ($reportVarsResult->result_array() AS $row)
			{
				if ( !empty($row['options_query']))
				{
					$optionsResult = $db2->query($row['options_query']);
					foreach ($optionsResult->result_array() AS $optRow)
					{
						$options[$optRow['id']] = $optRow['description'];
					}
					$optionsResult->free_result();
				}
					else
					{
						$options = NULL;
					}
				$report_vars[] = array(
					'text_identifier' => $row['text_identifier'],
					'variable_type' => $row['variable_type'],
					'default_value' => $row['default_value'],
					'display_name' => $row['display_name'],
					'description' => $row['description'],
					'options' => $options
				);
			}
			$reportVarsResult->free_result();

			$view_data['report_vars'] = $report_vars;
			// Pass back the report id so it can be used when the form is submitted
			// in order to target the correct report via a hidden input value where
			// where the id is stored.
			$view_data['report_id'] = $reportId;

			$this->load->view('dynamic_form_view', $view_data);
		}

		//processReport
		//
		// Called via AJAX, recieves $_POST variables to be used in  the report query.
		function processReport()
		{
			$db1 = $this->load->database('application', TRUE);
			$db2 = $this->load->database('pglms', TRUE);

			$id = $_POST['reportID'];

			// Get the report data
			$getReportDataQuery = "
			SELECT
				report_data
			FROM
				report
			WHERE
				id = " . $id;

			$getReportResult = $db1->query($getReportDataQuery);
			foreach ($getReportResult->result_array() AS $row)
			{
				$reportQuery = $row['report_data'];
			}
			$getReportResult->free_result();

			// Get all of the report variables to loop through them and use them
			// to match the terms in the query to be replaced
			$getReportVarsQuery = "
			SELECT
				text_identifier,
				variable_type
			FROM
				report_variable
			WHERE
				report_id = " . $id;

			$getReportVarsResult = $db1->query($getReportVarsQuery);
			$reportVars = $getReportVarsResult->result_array();
			$getReportResult->free_result();

			// Loop through the report variables and replace the matching
			// string in the report query with the appropriate $_POST variable value.
			foreach ($reportVars AS $var)
			{
				if ($var['text_identifier'] == 'date_range')
				{
					$reportQuery = preg_replace("/~date_range~/", '"' . date('Y-m-d H:i:s', strtotime($_POST['start_date'])) . '" AND "' . date('Y-m-d H:i:s', strtotime($_POST['end_date'])) . '"', $reportQuery);
				}
					elseif ($var['variable_type'] == 'string')
					{
						$reportQuery = preg_replace("/~" . $var['text_identifier'] . "~/i", "'" . $_POST[$var['text_identifier']] . "'", $reportQuery);
					}
						else
						{
							$reportQuery = preg_replace("/~" . $var['text_identifier'] . "~/i", $_POST[$var['text_identifier']], $reportQuery);
						}
			}

			$result = $db2->query($reportQuery);
			$setHeaderRow = false;
			$resultsArray = $result->result_array();
			$result->free_result();
			
			// Get the database fields from the array keys whcih are used for the
			// CSV files column headers row.
			$headerRow = array_keys($resultsArray[0]);
			
			// attach header row to beginning of array
			array_unshift($resultsArray, $headerRow);

			// Create the csv file
			$this->outputCSV($resultsArray);

			// Return the path to be passed to an iFrame that will cause the file to be downloaded.
			echo json_encode(array(
				'url' => base_url() . 'customreport/downloadReport/' . $this->filename
			));
			exit();
		}

		/**
		 * outputCSV
		 * 
		 * Creates the CSV file when passed an array of information retrieved
		 * via the report database query.
		 */
		private function outputCSV($array)
		{
			$this->filename = 'report_' . date('m_d_Y') . '_' . mt_rand(1, 999) . '.csv';
			$handler = fopen("report_holder/" . $this->filename, 'wb');
			
			foreach($array AS $val)
			{
				fputcsv($handler,$val);
			}
			fclose($handler);
		}
		
		/**
		 * downloadReport
		 * 
		 * This is the target of an iFrame in the view. Its purpose is
		 * to offer up the generated report as a csv download
		 */
		public function downloadReport($filename) 
		{
			$path = '/Users/ode/Documents/mi/mil-bi/www/report_holder/';
	
			header("Expires: 0");
			header("Cache-Control: no-cache, no-store");
			header("Content-Description: File Transfer");
			header("Content-type: text/csv");
			header("Content-Disposition: attachment; filename=$filename");
			header("Content-Length: " . filesize($path . $filename));
			readfile($path . $filename);
			
			unlink($path . $filename);
			exit();
		}

		function test($exit = false)
		{
			$this->show($_POST, $exit);
		}

	}
?>