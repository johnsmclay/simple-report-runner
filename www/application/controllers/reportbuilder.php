<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	class Reportbuilder extends CI_Controller {
		function __construct()
		{
			parent::__construct();
			$this->load->model('reportbuilder_model','report');
			//----- This page requires login-----
			$this->load->library('UserAccess');
			$this->useraccess->LoginRequired();
			if(!$this->useraccess->HasRole(array('system admin','report admin',))) redirect('/', 'refresh');
			//-----------------------------------
		}
		
		function index()
		{
			$view_data['connections'] = $this->report->getConnections();
			$view_data['categories'] = $this->report->getCategories();
			$view_data['userID'] = $this->useraccess->CurrentUserId();
			$this->load->view('customreports/reportbuilder_view',$view_data);
		}
		
		function generateVariables()
		{
			$variables = array();
			preg_match_all("/~([a-zA-Z_])*~/", $_POST['query'],$variables,PREG_PATTERN_ORDER);
			$variables = array_unique($variables[0]);
			
			$view_data['variables'] = $variables;
			
			$html = $this->load->view('customreports/reportvariables_view',$view_data,TRUE);
			
			echo json_encode(array('html'=>$html));
			exit();
		}
		
		public function getConnectionForm()
		{
			$html = $this->load->view('customreports/report_connection_form_view',null,true);
			echo json_encode(array('html'=>$html,'status'=>true));
			exit();
		}
		
		/**
		 * checkInputs
		 * 
		 * Loops through $_POST variables and checks if required inputs have a value
		 * 
		 * @return json An object returned via AJAX containing the id's of required elements which are missing values
		 */
		public function checkInputs()
		{
			$errors = array();
			
			
			foreach($_POST AS $key => $val)
			{
				if ($val['req'] == 'true')
				{
					if ($val['value'] == '')
					{
						$errors[] = $key;
					}
				}
			}
			
			if (!empty($errors))
			{
				echo json_encode(array('status'=>'error','errors'=>$errors));
				exit();
			}
				else
				{
					$success = $this->insertReport();
				}
		}

		private function insertReport()
		{
			
			// Insert connection if a new one exists
			if (isset($_POST['connectionForm']))
			{
				$connectionId = $this->report->insertConnection($connectionVars);
			}
				else
				{
					$connectionId = $_POST['connection_id']['value'];
				}
			
			// Insert report
			$reportId = $this->report->insertReport($connectionId);
			
			// Insert report Variables
			$this->report->insertVariables($reportId);
			
			echo json_encode(array('status'=>'passed'));
			exit();
		}
	}
?>