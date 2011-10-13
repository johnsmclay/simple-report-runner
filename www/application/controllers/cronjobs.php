<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

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
	
	function __construct()
	{
		parent::__construct();
		if ( ! $this->input->is_cli_request()) exit('This controller is only meant to be called via the CLI in a cron job.');
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
			$email_report = array();
			
			//run report
			$this->load->model('Custom_report_model');
			$report = $this->Custom_report_model->GetByID($scheduled_report->report_id);
			$result_array = $this->Custom_report_model->runReport($scheduled_report->report_id,get_object_vars(json_decode($scheduled_report->variables)));
			$email_report['name'] = $report->display_name;
			
			//get CSV url
			$this->load->helper(array('report','url'));
			$email_report['url'] = base_url().outputCSV($result_array);
			
			//get user
			$this->load->model('User_model');
			$user = $this->User_model->GetUserByID($scheduled_report->user_id);
			
			$template = array();
			
			$this->_SendEmail(array($email_report),$user->email_address,$template);
		}
	}
	
	private function _SendEmail($reports,$to,$template=array())
	{
		$default_template = array(
			'from_email' => 'reports@middil.com',
			'from_name' => 'reports@middil.com',
			'subject' => 'MIL Scheduled Reports',
			'header' => "The following report(s) were scheduled to be ran for you.<br/><br/>",
			'per_report' => "<display_name> -- <a href='<file_url>'>Click Here To Retreive File</a><br/>",
			'footer' => "<br/>Thanks!!<br/><br/>If you recieved this email in error please contact the Help Desk @ xxx.xxx.xxxx",
		);
		
		$this->load->library('email');
		$this->email->from(isset($template['from_email']) ? $template['from_email'] : $default_template['from_email'] , isset($template['from_name']) ? $template['from_name'] : $default_template['from_name']);
		$this->email->to($to); 
		$this->email->subject(isset($template['subject']) ? $template['subject'] : $default_template['subject']);
		$message = isset($template['header']) ? $template['header'] : $default_template['header'];
		foreach($reports as $report)
		{
			$line = isset($template['per_report']) ? $template['per_report'] : $default_template['per_report'];
			$line = str_replace('<display_name>',$report['name'],$line);
			$line = str_replace('<file_url>',$report['url'],$line);
			$message .= $line;
		}
		$message .= isset($template['footer']) ? $template['footer'] : $default_template['footer'];
		$this->email->message($message);	
		$this->email->send();
	}
	
}

/* End of file cronjobs.php */
/* Location: ./application/controllers/cronjobs.php */