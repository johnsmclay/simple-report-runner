<?php
class Teachers extends CI_Controller {
	
	function __construct() {
		parent::__construct();
		$this->load->library('milteachers');
	}
	
	public function index() {
		$this->load->view('teachers_view');
	}
	
	public function getTeachers() {
		$teachers = $this->milteachers->milTeachers;
		
		foreach ($teachers AS $name => $ids) {
			$return[$name] = $name;
		}
		echo json_encode(array('success' => true,'teachers' =>$return));
		exit;
	}
}
?>