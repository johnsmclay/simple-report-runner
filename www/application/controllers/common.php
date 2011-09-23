<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * The Common controller does not have a view that it calls,
 * it is a place for Ajax calls that multiple pages make use of
 * such as obtaining the list of schools and sub-schools
 */
class Common extends CI_Controller {
	function __construct() {
		parent::__construct();
		$this->load->database();
		$this->load->model('Common_model');
	}
	
	/**
	 * getSchoolList
	 * 
	 * Calls the query from the like named method in the Enrollment_model.
	 * 
	 * @ajax json encoded list of schools
	 */	
	function getSchoolList() {
		$schoolList = $this->Common_model->getSchoolList();
		
		foreach ($schoolList AS $school) {
			$schools[$school['client']] = $school['id'];
		}
		
		echo json_encode($schools);
		
		exit();
	}
	
	/**
	 * getSubSchoolList
	 * 
	 * Calls the query from the like named method in the Enrollment_model.
	 * 
	 * @ajax json encoded list of subschools
	 */	
	function getSubSchoolList() {
		$parent = $this->input->post('parent');
		$subSchools = $this->Common_model->getSubSchoolList($parent);
		
		if($subSchools == null) {
			echo json_encode(array('status'=>false));
			exit();
		}
			else {
				foreach ($subSchools AS $school) {
					$schools[$school['school']] = $school['id'];
				}
				echo json_encode($schools);
				
				exit();
			}
		
	}
}
?>