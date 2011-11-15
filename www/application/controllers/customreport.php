<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	class Customreport extends CI_Controller {

		private $reportDB;

		function __construct()
		{
			parent::__construct();
			$this->load->helper(array('form','report_helper','date_helper'));
			$this->load->model('custom_report_model','model');
			$this->load->model('connection_model','connection');
			$this->load->model('user_model','user');
			
			//----- This page requires login-----
			$this->load->library('UserAccess');
			$this->useraccess->LoginRequired();
			//-----------------------------------
		}

		public function index()
		{
			// Get the list of all reports
			$reportList = $this->model->getReportList();
			
			// Add it to the view data
			$view_data['reportList'] = $reportList;

			$this->load->view('customreports/customreport_view', $view_data);
		}

		/** buildForm
		 *
		 * Called via AJAX request, it is sent an integer representing
		 * the report ID of the requested report. After querying the DB
		 * for the associated variables for that report ID it returns an
		 * array of report variables which is then used in the view to
		 * dynamiccaly build out the for for the report.
		 *
		 * @access public
		 * @return mixed Outputs the HTML to the browser for the view that has been called
		 */ 
		public function buildForm()
		{
			// ID passed via ajax call
			$reportId = $_POST['report_id'];
			
			// Retrieve all the report variables	
			$report_vars = $this->model->getReportVars($reportId);
			
			// Obtain the description seperately from the report variables in order to display
			$description = $this->model->getReportDescription($reportId);
			
			// Load the variables into the view data array
			$view_data['report_vars'] = $report_vars;
			$view_data['description'] = $description;
			
			// Pass back the report id so it can be used when the form is submitted
			// in order to target the correct report via a hidden input value where
			// where the id is stored.
			$view_data['report_id'] = $reportId;

			// Calling the load view method in this instance will immediately
			// send back the view (HTML) to the ajax method that called this function
			$html = $this->load->view('customreports/dynamic_form_view', $view_data, true);
			
			echo $html;
			exit();
		}

		/**
		 * processReport
		 *
		 * Called via AJAX, recieves $_POST variables to be used in  the report query.
		 * 
		 * @access public
		 * @return mixed Echo's out the json encoded url for an iFrame src attribute in the view
		 */ 
		public function processReport()
		{
			$reportId = $_POST['reportID'];
			$reportFormat = $_POST['reportFormat']; // HTML or CSV?

			// Run the report query and get the results
			$resultsArray = $this->model->runReport($reportId,$_POST);
			
			if ($resultsArray == FALSE)
			{
				echo json_encode(array(
					'status'=> 'failed'
				));
				exit();
			}
			
			$headers = array_keys($resultsArray[0]);
			
			// Send the report information to the correct output method
			if ($reportFormat == 'csv')
			{
				// get the report
				$report = $this->model->GetByID($reportId);

				// Prepare Preface
				$preface = 'Report: ' . $report->display_name . "\n";
				$preface .= '------------------------------------'."\n";
				$preface .= "Parameters: \n";
				foreach($_POST AS $item=>$value)
				{
					if($item != 'reportID' && $item != 'reportFormat')
					{
						$preface .= $item . " = " . $value . "\n";
					}
				}
				$preface .= '------------------------------------'."\n";

				// Create the csv file
				$filename = outputCSV($resultsArray,$headers,$preface);
				// Return the path to be passed to an iFrame that will cause the file to be downloaded.
				echo json_encode(array(
					'type' => $reportFormat,
					'url' => base_url() . 'customreport/downloadReport/' . $filename
				));
				exit();
			}
				elseif($reportFormat == 'html')
				{
					$html = createHTMLTable($resultsArray,$headers,200);
					
					echo json_encode(array(
						'type' => $reportFormat,
						'htmlTable' => $html
					));
					exit();
				}
		}

		/**
		 * downloadReport
		 * 
		 * This is the target of an iFrame in the view. Its purpose is
		 * to offer up the generated report as a csv download
		 * 
		 * @access public
		 * @param string $filename The name of the file to be downloaded
		 */
		public function downloadReport($path,$filename) 
		{
			header("Expires: 0");
			header("Cache-Control: no-cache, no-store");
			header("Content-Description: File Transfer");
			header("Content-type: text/csv");
			header("Content-Disposition: attachment; filename=$filename");
			header("Content-Length: " . filesize($path . DIRECTORY_SEPARATOR . $filename));
			readfile($path . DIRECTORY_SEPARATOR . $filename);
			
			unlink($path . DIRECTORY_SEPARATOR . $filename);
			exit();
		}
		
		/**
		 * loadScheduleReport
		 * 
		 * Loads the 
		 * to offer up the generated report as a csv download
		 * 
		 * @access public
		 * @param string $filename The name of the file to be downloaded
		 */
		public function loadScheduleReport()
		{
			$users = $this->user->GetAllActive();
			$data['users'] = $users;
			$html = $this->load->view('customreports/schedule_report_view',$data,true);
			echo json_encode(array('html'=>$html));
			exit();
		}
	}
?>