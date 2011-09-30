<?php
class Teachers extends CI_Controller {
	
	function __construct() {
		parent::__construct();
		$this->load->library('milteachers');
	}
	
	public function index() {
		// Allows you to name an individual JavaScript file to be loaded for this page.
		// Just provide the name of the file, without the .js extension. Then create the 
		// file in the 'assets/javascript' folder located in the root of the codeIgniter folder
		$view_data['javascript'] = 'teachers';
		
		$this->load->view('teachers_view',$view_data);
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