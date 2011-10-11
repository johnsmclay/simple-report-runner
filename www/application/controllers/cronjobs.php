<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
if ( ! defined('CRON') || CRON != TRUE) exit('This controller is only meant to be called via the CLI in a cron job.');

class Cronjobs extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/cronjobs
	 *	- or -  
	 * 		http://example.com/index.php/cronjobs/index
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 */
	public function index()
	{
		// Do nothing.  This will not be called outside of the CLI.
	}
	
	public function processscheduledreports()
	{
		// find the reports that need to run
		$this->load->model('Scheduled_report_model');
		$scheduled_reports = $this->Scheduled_report_model->GetReportsDue();
		if(!$scheduled_reports) return true;
		
		// run the scheduled reports one at a time
		foreach($scheduled_reports as $scheduled_report)
		{
			//TODO: run report
			
			//TODO: get user
			
			//TODO: get send email (only one email per user?)
		}
	}
	
}

/* End of file cronjobs.php */
/* Location: ./application/controllers/cronjobs.php */