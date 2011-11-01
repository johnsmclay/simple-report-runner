<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	class Schedulereport extends CI_Controller {
		public function __construct()
		{
			parent::__construct();
			$this->load->helper(array('form','report_helper','date_helper'));
			$this->load->model('custom_report_model','model'); // for the sake of not copying everything we re-use this model
			$this->load->model('connection_model','connection');
			$this->load->model('user_model','user');	
			$this->load->database();		
			
			//----- This page requires login-----
			$this->load->library('UserAccess');
			$this->useraccess->LoginRequired();
			//-----------------------------------
		}
		
		public function index()
		{
			// Get the list of all reports
			$view_data['reportList'] = $this->model->getReportList();

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
			
			// Get all of the currently active users to display in a drop down menu
			$view_data['users'] = $this->user->GetAllActive();

			// Calling the load view method in this instance will immediately
			// send back the view (HTML) to the ajax method that called this function
			$html = $this->load->view('customreports/dynamic_form_view', $view_data, true);
			
			echo $html;
			exit();
		}

		public function scheduleIt()
		{
			$vars = $_POST;
			$time = $vars['timeQuantifier'];
			// $var
			
			// Store the cron job values and unset the array values so that
			// all we are left with are the report variable values
			$cronMonth = $vars['month_of_year'];
			unset($vars['month_of_year']);
			$cronDayMonth = $vars['day_of_month'];
			unset($vars['day_of_month']);
			$cronDayWeek = $vars['day_of_week'];
			unset($vars['day_of_week']);
			$cronHour = $vars['hour_of_day'];
			unset($vars['hour_of_day']);
			$userID = $vars['user'];
			unset($vars['user']);
			$reportID = $vars['reportID'];
			unset($vars['reportID']);
					
			// Set date/time values
			if (isset($vars['reportTimeFrame']))
			{
				if (empty($time))
				{
					$time = 1;
				}
				
				switch($vars['reportTimeFrame'])
				{
					case 'day':
						// Add one to the timeQuantifier to include actual days since the current day is excluded
						// (i.e. if user inputs 3 it will calculate only 2 days back since today is excluded from the end date)
						$start_date = '-' . ($time == 1 ? $time : $time + 1) . 'days';
						$end_date = '-1 day';
						break;
					case 'week':
						$start_date = 'Monday -' . $time . ' weeks';
						$end_date = 'Friday last week';
						break;
					case 'month':
						$start_date = 'first day of this month -' . $time . ' months';
						$end_date = 'last day of last month';
						break;
				}
				// unset the last two POST variables so all we have are the report variables
				unset($vars['reportTimeFrame']);
				unset($vars['timeQuantifier']);
				$vars['start_date'] = $start_date;
				$vars['end_date'] = $end_date;
			}
			
			$variables = json_encode($vars);
			
			$data = array(
				'report_id' => $reportID,
				'user_id' => $userID,
				'variables' => $variables,
				'day_of_month' => $cronDayMonth,
				'month_of_year' => $cronMonth,
				'day_of_week' => $cronDayWeek,
				'hour_of_day' => $cronHour,
				'created' => date('Y-m-d H:i:s'),
				'created_by_user_id' => $this->useraccess->CurrentUserId()
			);
			
			$this->db->insert('scheduled_report',$data);
			
			echo json_encode(array('status'=>'success'));
			exit();
		}
	}
?>