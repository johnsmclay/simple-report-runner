<?php
	class Reportbuilder extends CI_Controller {
		function __construct()
		{
			parent::__construct();
			$this->load->model('reportbuilder_model','report');
		}
		
		function index()
		{
			// Allows you to name an individual JavaScript file to be loaded for this page.
			// Just provide the name of the file, without the .js extension. Then create the
			// file in the 'assets/javascript' folder located in the root of the codeIgniter folder
			$data['javascript'] = 'reportbuilder';
			
			$this->load->view('reportbuilder_view',$data);
		}
	}
?>