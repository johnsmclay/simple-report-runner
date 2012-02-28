<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Teacherperformance extends CI_Controller {

	private $report_Output_folder = './report_holder/';
	private $query_folder = './queries/';

	public function index()
	{
		// Do nothing.  This will not be called outside of the CLI.
	}
	
	function __construct()
	{
		parent::__construct();
		//if ( ! $this->input->is_cli_request()) exit('This controller is only meant to be called via the CLI in a cron job.');
	}


	/**
	 * sendsummaries()
	 * 
	 * Called from the CLI to create and send out teacher pay report summaries to admins
	 * 
	 * 
	 */
	public function sendsummaries()
	{
		$this->sendall(TRUE);
	}

	

	public function sendall($skip_email=FALSE)
	{
		echo "Sending current reports"."<br/>\n";
		$pglms_db = $this->load->database('pglms');

		$teachers = $this->db->get_where('warehouse.mil_teachers', array('deleted' => '0000-00-00 00:00:00'))->result_array();

		$file_list = array();

		foreach($teachers AS $teacher)
		{
			$file_list[] = $this->singleteacher($teacher['id'],$month_id,$skip_email);
		}

		$this->load->dbutil();
		$this->load->helper('file');

		$SummaryQuery = read_file($this->query_folder.'teacherperformance_allteachers.sql');
		$squery = $this->db->query($SummaryQuery);

		//create the HTML
		$html = $this->load->view('email_templates/teacher_performance_report_email', array(
			'single' => FALSE,
			'TeacherInfo' => $teacher_info[0],
			'DetailsResults' => $DetailsResult,
			'SummaryResults' => $SummaryResult,
		), true);

		//output to an XLS file
		$filename = 'pay_'.$teacher_info[0]['email_address'].'_'.strftime('%Y-%m-%d',time()).'.xls';
		$this->load->helper('report_helper');
		write_file($this->report_Output_folder.$filename, $html);

		// add the XLS to the files to send
		$file_list[] = $this->report_Output_folder.$filename;

		//send the email
		$subject = 'Teacher Performance Report - Complete';
		$email = 'bgaunce@middil.com';
		$this->sendEmail($email, $subject, 'Here is your copy.', $file_list);
		if(!$skip_email) sendEmailReport($email, $subject, $html, array('./'.$filename));
	}

	public function singleteacher($teacher_id,$skip_email=FALSE)
	{

		echo "Sending pay report to teacher # $teacher_id"."<br/>\n";
		$pglms_db = $this->load->database('pglms');
		//$teacher_pay_db = $this->load->database('teacher_pay');
		
		$teacher_info = $this->db->get_where('warehouse.mil_teachers', array('id' => $teacher_id), 1)->result_array();

		$this->load->helper('file');

		$DetailsQuery = read_file($this->query_folder.'teacherperformance_singleteacher.sql');
		$DetailsQuery = str_replace('~mil_teacher_id~',$teacher_id,$DetailsQuery);
		$dquery = $this->db->query($DetailsQuery);
		$DetailsResults = $dquery->result_array();
		if(sizeof($DetailsResults) == 0)
		{
			echo "No data was found for ".$teacher_info[0]['display_name']."<br/>\n";
			return TRUE;
		}

		$html = $this->load->view('email_templates/teacher_performance_report_email', array(
			'single' => TRUE,
			'TeacherInfo' => $teacher_info[0],
			'DetailsResults' => $DetailsResults,
		), true);
		
		$filename = 'performance_'.$teacher_info[0]['email_address'].'_'.strftime('%Y-%m-%d',time()).'.xls';
		$this->load->helper('report_helper');
		write_file($this->report_Output_folder.$filename, $html);
		$subject = 'Pay Review Report - '.$teacher_info[0]['display_name'];
		$email = $teacher_info[0]['email_address'];
		$email = 'cjohns@middil.com';//debug
		if(!$skip_email) sendEmailReport($email, $subject, $html, array($this->report_Output_folder.$filename));
		if($skip_email) echo $html;//debug
		return $this->report_Output_folder.$filename;
	}

}