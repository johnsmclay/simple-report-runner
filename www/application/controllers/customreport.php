<?php
class Customreport extends CI_Controller {
		
	function __construct() {
		parent::__construct();
		$this->load->helper('form');
	}
	
	function index() {
		$report_vars = array();
		$report_vars[] = array(
			'variable_type' => 'INT(11)',
			'text_identifier' => 'test',
			'default_value' => 23,
			'display_name' => 'Testin this',
			'description' => 'testing button stuff',
			'options' => array(
				12 => 'option 1', 23 => 'option 2', 244 => 'option 3'
			)
		);
		$report_vars[] = array(
			'variable_type' => 'INT(10)',
			'text_identifier' => 'varcharacters',
			'default_value' => 'crapTAstic',
			'display_name' => 'Crappy Name',
			'description' => 'testing crappy stuff',
		);
		$report_vars[] = array(
			'variable_type' => 'VARCHAR(15)',
			'text_identifier' => 'varcharacters',
			'default_value' => 'WTF?',
			'display_name' => 'Deez Nutz',
			'description' => 'testing crappy stuff',
		);
		$report_vars[] = array(
			'variable_type' => 'DATETIME',
			'text_identifier' => 'dates[]',
			'default_value' => 'WTF?',
			'display_name' => 'Deez Nutz',
			'description' => 'testing crappy stuff',
		);
		
		
		$view_data['report_vars'] = $report_vars;
		
		// Allows you to name an individual JavaScript file to be loaded for this page.
		// Just provide the name of the file, without the .js extension. Then create the 
		// file in the 'assets/javascript' folder located in the root of the codeIgniter folder
		$view_data['javascript'] = 'customreport';
		$this->load->view('customreport_view',$view_data);
	}

	function test() {
		$this->show($_POST);
	}
	
	
}
?>