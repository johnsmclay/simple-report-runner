<?php
class Customers extends CI_Controller {
	
	function __construct() {
		parent::__construct();
		$this->load->helper(array('url','html'));
	}
	
	public function index() {
		$this->load->view('customers_view');
	}
}
?>