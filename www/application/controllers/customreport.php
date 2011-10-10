<?php
	class Customreport extends CI_Controller {

		var $filename = '';
		var $reportDB;

		function __construct()
		{
			parent::__construct();
			$this->load->helper('form');
			$this->load->model('custom_report_model','model');
			$this->load->model('connection_model','connection');
		}

		function index()
		{
			// Get the list of all reports
			$reportList = $this->model->getReportList();
			
			// Add it to the view data
			$view_data['reportList'] = $reportList;

			// Allows you to name an individual JavaScript file to be loaded for this page.
			// Just provide the name of the file, without the .js extension. Then create the
			// file in the 'assets/javascript' folder located in the root of the codeIgniter folder
			$view_data['javascript'] = 'customreport';
			
			$this->load->view('customreport_view', $view_data);
		}

		/** buildForm
		 *
		 * Called via AJAX request, it is sent an integer representing
		 * the report ID of the requested report. After querying the DB
		 * for the associated variables for that report ID it returns an
		 * array of report variables which is then used in the view to
		 * dynamiccaly build out the for for the report.
		 *
		 * @return mixed Outputs the HTML to the browser for the view that has been called
		 */ 
		function buildForm()
		{
			// ID passed via ajax call
			$reportId = $_POST['report_id'];
			
			// Get the conneciton array data for running the custom report
			$connection = $this->connection->getConnection($reportId);
			
			$this->model->setReportDB($connection);
			
			// Retrieve all the report variables	
			$report_vars = $this->model->getReportVars($reportId);

			// Load the variables into the view data array
			$view_data['report_vars'] = $report_vars;
			
			// Pass back the report id so it can be used when the form is submitted
			// in order to target the correct report via a hidden input value where
			// where the id is stored.
			$view_data['report_id'] = $reportId;

			// Calling the load view method in this instance will immediately
			// send back the view (HTML) to the ajax method that called this function
			$this->load->view('dynamic_form_view', $view_data);
		}

		/**
		 * processReport
		 *
		 * Called via AJAX, recieves $_POST variables to be used in  the report query.
		 * 
		 * @return mixed Echo's out the json encoded url for an iFrame src attribute in the view
		 */ 
		function processReport()
		{

			$reportId = $_POST['reportID'];

			// Get the query to be run
			$reportData = $this->model->getReportData($reportId);
			
			$reportQuery = $reportData['report_data'];
			
			// Get the conneciton array data for running the custom report
			$connection = $this->connection->getConnection($reportId);

			// Get all of the report variables to loop through them and use them
			// to match the terms in the query to be replaced
			$reportVars = $this->model->getReportVars($reportId,false);

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
			
			// Run the report query and get the results
			$resultsArray = $this->model->runReportQuery($reportQuery);
			
			if ($resultsArray == FALSE)
			{
				echo json_encode(array(
					'status'=> 'failed'
				));
				exit();
			}
			
			// Create the csv file
			$this->outputCSV($resultsArray);

			// Return the path to be passed to an iFrame that will cause the file to be downloaded.
			echo json_encode(array(
				'status' => 'success',
				'url' => base_url() . 'customreport/downloadReport/' . $this->filename
			));
			exit();
		}

		/**
		 * outputCSV
		 * 
		 * Creates the CSV file when passed an array of information retrieved
		 * via the report database query.
		 * 
		 * @param array $array The array of data to be encoded into a CSV file
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
		 * 
		 * @param string $filename The name of the file to be downloaded
		 */
		public function downloadReport($filename) 
		{
			$path = readlink('/var/www/newdashboard/report_holder/');
	
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