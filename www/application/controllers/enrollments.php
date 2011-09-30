<?php
class Enrollments extends CI_Controller {
		
	function __construct() {
		parent::__construct();
		$this->load->database();
		$this->load->model('Enrollment_model');
	}
		
	public function index() {
		// Allows you to name an individual JavaScript file to be loaded for this page.
		// Just provide the name of the file, without the .js extension. Then create the 
		// file in the 'assets/javascript' folder located in the root of the codeIgniter folder
		$view_data['javascript'] = 'enrollments';
		
		$this->load->view('enrollments_view',$view_data);
	}
	
	public function getEnrollments() {
		$test = $this->Enrollment_model->retrieveEnrollments();
		$this->show($test,true);
	}
	
}
?>