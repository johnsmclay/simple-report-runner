<?php
class Enrollments extends CI_Controller {
		
	function __construct() {
		parent::__construct();
		$this->load->database();
		$this->load->model('Enrollment_model');
	}
		
	public function index() {
		$this->load->view('enrollments_view');
	}
	
	public function getEnrollments() {
		$test = $this->Enrollment_model->retrieveEnrollments();
		$this->show($test,true);
	}
	
}
?>