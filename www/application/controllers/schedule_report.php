<?php
	class Schedule_report extends CI_Controller {
		function __construct()
		{
			parent::__construct();
			$this->load->model('scheduled_report_model','schedule');
			$this->load->model('custom_report_model','report');
		}
		
		function index()
		{
			// nothing goes here
		}
		
		function scheduleIt() 
		{
			// $report = $_POST['report'];
			// $schedule = $_POST['schedule'];
// 			
			// $reportVars = $this->report->getReportVars($report['reportID'],false);
			// $this->show($reportVars);
// 			
			// $temp=array();
			// foreach ($reportVars AS $var)
			// {
				// if($var['text_identifier'] == 'date_range')
				// {
					// $temp
				// }
				// $temp[$var['text_identifier']] = $report[$var['text_identifier']];
			// }
			// $this->show($temp);
			// exit;
		}
	}
?>