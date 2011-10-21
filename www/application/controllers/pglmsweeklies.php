<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pglmsweeklies extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		
		//----- This page requires login-----
		$this->load->library('UserAccess');
		$this->useraccess->LoginRequired();
		if(!$this->useraccess->HasRole(array('system admin','internal',))) redirect('/', 'refresh');
		//-----------------------------------
	}
	
	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/pglmsweeklies
	 *	- or -  
	 * 		http://example.com/index.php/pglmsweeklies/index
	 */
	public function index()
	{
		$this->load->helper('form');
		$view_data = array(
			
		);
		$this->load->view('pglmsweeklies/form', $view_data);
	}
	
	public function requestReport()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('school_id', 'School ID', 'required|integer');
		$this->form_validation->set_rules('from_date', 'From Date', 'required|strtotime');
		$this->form_validation->set_rules('to_date', 'To Date', 'required|strtotime');
		
		log_message('info', __METHOD__.' called with: '.json_encode($_REQUEST));
		
		if ($this->form_validation->run() == FALSE)
		{
			log_message('error', __METHOD__.' validation failed.');
			$this->index();
		}
		
		$school_id = $_REQUEST['school_id'];
		$from_date = strtotime($_REQUEST['from_date']);
		$to_date = strtotime($_REQUEST['to_date']);
		
		$this->createReport($school_id,$from_date,$to_date);

	}
	
	private function createReport($school_id,$from_date,$to_date)
	{
		// prepare the file and it's glossary first
		
			$glossary_template_path = './assets/templates/excel/weekly_report_glossary_template.xlsx';
			
			// Get PHPExcel plugin
			require_once 'PHPExcel.php';
			
			// Create new PHPExcel object
			$objPHPExcel = new PHPExcel();

			$objPHPExcel->getProperties()->setCreator("PowerSpeak Languages")
										 ->setLastModifiedBy("PowerSpeak Languages")
										 ->setTitle("PowerSpeak Weekly Reports")
										 ->setSubject("PowerSpeak Weekly Reports")
										 ->setDescription("PowerSpeak Weekly Reports");

			$objPHPExcel->setActiveSheetIndex(0);

			$objReader = new PHPExcel_Reader_Excel2007();
			$objPHPExcelTemp = $objReader->load($glossary_template_path);
			$objPHPExcelTemp->setActiveSheetIndex(0);
			$glossary_sheet = $objPHPExcelTemp->getActiveSheet();
			$objPHPExcel->addExternalSheet($glossary_sheet,0);

			$current_sheet_index = 0;
		
		
		
		// loop through the report list and add the other tabs
		
			$this->load->helper('modelutils');
			$this->load->model('custom_report_model');
			
			$reports_used = array(
				11 => 'School Detail Report',
				12 => 'Dropped Report',
				13 => 'Enrollment by Language',
				14 => 'Enrollment by Classroom',
			);
			
			$report_vars = array(
				'parent_school_id' => $school_id,
				'start_date' => mysql_date($from_date),
				'end_date' => mysql_date($to_date),
			);
			
			foreach($reports_used as $report_id => $title)
			{
				// run the report
				$resultsArray = $this->custom_report_model->runReport($report_id,$report_vars);
				// write out the report to a temporary file
				$this->load->helper('report_helper');
				$headers = array_keys($resultsArray[0]);
				$filename = outputCSV($resultsArray,$headers);
				
				// import the data
				$current_sheet_index += 1;
				$objReader = PHPExcel_IOFactory::createReader('CSV')
					->setDelimiter(',')
					->setEnclosure('')
					->setLineEnding("\r\n")
					->setSheetIndex($current_sheet_index)
					->loadIntoExisting($filename, $objPHPExcel);
				$objPHPExcel->setActiveSheetIndex($current_sheet_index);
				$objPHPExcel->getActiveSheet()->setTitle($title);
				
				// delete the temporary file
				unlink($filename);
				
				//style the page
				$phpexcel_templates_folder = './assets/templates/phpexcel/';
				$templatePath = $phpexcel_templates_folder.'report_'.$report_id.'.php';
				if(file_exists($templatePath))
				{
					include $templatePath;
					$style_function = 'styleReport_'.$report_id;
					$style_function($objPHPExcel->getActiveSheet(),$report_name,$report_vars);
				}
				
			}
			
			// set the glossart as the default tab
			$objPHPExcel->setActiveSheetIndex(0);
			
			// output the file
			$file = 'weekly-reports_'.$school_id.'_week-'.date('W',$from_date).'_'.date('m-d-y').'.xlsx';
			$folder = "report_holder/";
			$objWriter2007 = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
			$objWriter2007->save($folder.$file);
			
			return $folder.$file;
	}
	
	public function test()
	{
		$this->load->helper('url');
		$file_path = $this->createReport(1,strtotime('10/2/2011'),strtotime('10/8/2011'));
		redirect($file_path, 'refresh');
	}
	
	public function cron_send_report($school_id,$from_date,$to_date)
	{
		if ( ! $this->input->is_cli_request()) exit('This method is only meant to be called via the CLI in a cron job.');
		
		$file_path = $this->createReport($school_id,$from_date,$to_date);
	}
	
	public function downloadReport()
	{
		
	}
	
}

/* End of file pglmsweeklies.php */
/* Location: ./application/controllers/pglmsweeklies.php */