<?php
class Select_report extends CI_Controller {
	function __construct()
	{
		parent::__construct();
	}
	
	function index() 
	{
		// Allows you to name an individual JavaScript file to be loaded for this page.
		// Just provide the name of the file, without the .js extension. Then create the 
		// file in the 'assets/javascript' folder located in the root of the codeIgniter folder
		$view_data['javascript'] = 'select_report';
		
		$this->load->view('select_report_view',$view_data);
	}
}
?>