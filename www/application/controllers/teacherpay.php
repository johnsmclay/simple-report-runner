<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Teacherpay extends CI_Controller {

	private $report_Output_folder = './report_holder/';
	private $db_connection_pglms = NULL;
	private $testing_mode = TRUE;
	private $override_email = array('fmaldonado@middil.com');
	private $note_to_teachers = '';

	public function index()
	{
		// Do nothing.  This will not be called outside of the CLI.
	}
	
	function __construct()
	{
		parent::__construct();
		//if ( ! $this->input->is_cli_request()) exit('This controller is only meant to be called via the CLI in a cron job.');
		//----- This page requires login-----
		$this->load->library('UserAccess');
		$this->useraccess->LoginRequired();
		if(!$this->useraccess->HasRole(array('system admin',))) redirect('/', 'refresh');
		//-----------------------------------
		$this->db_connection_pglms = $this->load->database('pglms', TRUE);
	}


	/**
	 * sendpayreports()
	 * 
	 * Called from the CLI to create and send out teacher pay reports
	 * 
	 * 
	 */
	public function sendsummaries($month_id)
	{
		$this->sendall($month_id,TRUE);
	}

	

	public function sendall($month_id,$skip_email=FALSE)
	{
		echo "Sending pay reports for month $month_id"."<br/>\n";

		$teachers = $this->db_connection_pglms->get_where('warehouse.mil_teachers', array('deleted' => '0000-00-00 00:00:00'))->result_array();
		//print_r($teachers);//debug

		$file_list = array();

		foreach($teachers AS $teacher)
		{
			$file_list[] = $this->singleteacher($teacher['id'],$month_id,$skip_email);
		}

		$this->load->dbutil();

		$SummaryQuery = "SELECT 
				mo.month, 
				mo.month_name,
				mt.display_name AS 'Teacher', 
				sum.qty_before, 
				sum.qty_added, 
				sum.qty_dropped, 
				sum.proj_semester_pay, 
				sum.pay_so_far, 
				sum.pay_this_month, 
				sum.periods_remaining
			FROM 
				teacher_pay.monthly_pay_summary sum 
				LEFT JOIN teacher_pay.month mo ON sum.month_id = mo.id
				LEFT JOIN warehouse.mil_teachers mt ON sum.teacher_id = mt.id
			WHERE mo.id = $month_id
		";
		$squery = $this->db_connection_pglms->query($SummaryQuery);
		$summary_csv_data = $this->dbutil->csv_from_result($squery);


		$DetailsQuery = "SELECT
				teacher_name AS Teacher,
				system AS System,
				student_name AS Student, 
				school_name AS School, 
				school_system_identifier AS SchoolID, 
				classroom_name AS Classroom, 
				DATE_FORMAT(effective_start_date,'%m/%d/%Y') AS `Effective Start Date`, 
				IF(drop_date=0,'',DATE_FORMAT(drop_date,'%m/%d/%Y')) AS `Student Drop Date`, 
				IF(dropped_within_30_days=1,'YES','NO') AS `Dropped Within 30 Days`, 
				IF(started_this_semester=1,'YES','NO') AS `Started This Semester`, 
				IF(last_semester_full_year=1,'YES','NO') AS `Prev. Semester Full Year`, 
				IF(payable_this_semester=1,'YES','NO') AS Payable, 
				IF(pay_subtotal=0,'',CONCAT('$',pay_subtotal)) AS Pay, 
				IF(adjustment=0,'',CONCAT('$',adjustment)) AS Adjustment,
				IF((pay_subtotal + adjustment)=0,'',CONCAT('$',(pay_subtotal + adjustment))) AS `Adjusted Pay`
			FROM teacher_pay.monthly_pay_details
			WHERE
				month_id = $month_id
		";
		$dquery = $this->db_connection_pglms->query($DetailsQuery);
		$detailed_csv_data = $this->dbutil->csv_from_result($dquery);

		$this->load->helper('file');

		$filename = 'summary_COMPLETE_'.strftime('%Y-%m-%d',time()).'.csv';
		write_file($this->report_Output_folder.$filename, $summary_csv_data);
		$file_list[] = $this->report_Output_folder.$filename;

		$filename = 'detail_COMPLETE_'.strftime('%Y-%m-%d',time()).'.csv';
		write_file($this->report_Output_folder.$filename, $detailed_csv_data);
		$file_list[] = $this->report_Output_folder.$filename;

		$subject = 'Pay Review Report - Complete';
		//$subject .= ' - FIXED';//debug
		$email = array('mblake@middil.com','bgaunce@middil.com');
		if($this->testing_mode) $email = $this->override_email;
		sendEmailReport($email, $subject, 'Here is your copy.', $file_list);
	}

	public function singleteacher($teacher_id,$month_id,$skip_email=FALSE)
	{

		echo "Sending pay report to teacher # $teacher_id"."<br/>\n";
		//$teacher_pay_db = $this->load->database('teacher_pay');
		
		$teacher_info = $this->db_connection_pglms->get_where('warehouse.mil_teachers', array('id' => $teacher_id), 1)->result_array();
		echo 'Got teacher info:'.json_encode($teacher_info)."<br/>\n";

		$SummaryQuery = "SELECT 
				mo.month, 
				mo.month_name, 
				sum.qty_before, 
				sum.qty_added, 
				sum.qty_dropped, 
				sum.proj_semester_pay, 
				sum.pay_so_far, 
				sum.pay_this_month, 
				sum.periods_remaining
			FROM 
				teacher_pay.monthly_pay_summary sum 
				LEFT JOIN teacher_pay.month mo ON sum.month_id = mo.id
			WHERE sum.teacher_id = $teacher_id
		";
		$squery = $this->db_connection_pglms->query($SummaryQuery);
		$SummaryResult = $squery->result_array();
		if(sizeof($SummaryResult) == 0)
		{
			echo "No data was found for ".$teacher_info[0]['display_name']."<br/>\n";
			return TRUE;
		}

		$DetailsQuery = "SELECT
				system AS System,
				student_name AS Student, 
				school_name AS School, 
				school_system_identifier AS SchoolID, 
				classroom_name AS Classroom, 
				DATE_FORMAT(effective_start_date,'%m/%d/%Y') AS `Effective Start Date`, 
				IF(drop_date=0,'',DATE_FORMAT(drop_date,'%m/%d/%Y')) AS `Student Drop Date`, 
				IF(dropped_within_30_days=1,'YES','NO') AS `Dropped Within 30 Days`, 
				IF(started_this_semester=1,'YES','NO') AS `Started This Semester`, 
				IF(last_semester_full_year=1,'YES','NO') AS `Prev. Semester Full Year`, 
				IF(payable_this_semester=1,'YES','NO') AS Payable, 
				IF(pay_subtotal=0,'',CONCAT('$',pay_subtotal)) AS Pay, 
				IF(adjustment=0,'',CONCAT('$',adjustment)) AS Adjustment,
				IF((pay_subtotal + adjustment)=0,'',CONCAT('$',(pay_subtotal + adjustment))) AS `Adjusted Pay`
			FROM teacher_pay.monthly_pay_details
			WHERE
				mil_teacher_id = $teacher_id
				AND month_id = $month_id
		";
		$dquery = $this->db_connection_pglms->query($DetailsQuery);
		$DetailsResult = $dquery->result_array();

		$html = $this->load->view('email_templates/teacher_pay_report_email', array(
			'single' => TRUE,
			'TeacherInfo' => $teacher_info[0],
			'DetailsResults' => $DetailsResult,
			'SummaryResults' => $SummaryResult,
		), true);
		
		$filename = 'pay_'.$teacher_info[0]['email_address'].'_'.strftime('%Y-%m-%d',time()).'.xls';
		$this->load->helper('report_helper');
		$this->load->helper('file');
		write_file($this->report_Output_folder.$filename, $html);
		$subject = 'Pay Review Report - '.$teacher_info[0]['display_name'];
		//$subject .= ' - FIXED';//debug
		$email = array($teacher_info[0]['email_address'],'bgaunce@middil.com');
		if($this->testing_mode) $email = $this->override_email;
		$this->load->helper('file');
		if(!$skip_email)
		{
			if($teacher_info[0]['skip_pay_report'] == 0)
			{
				sendEmailReport($email, $subject, $html, array($this->report_Output_folder.$filename));
			}
		}
		return $this->report_Output_folder.$filename;
	}

}