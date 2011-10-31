<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sysadmin extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('User_model');
		$this->load->helper(array('form','url'));
		$this->load->library(array('form_validation','table','session'));
		
		//----- This page requires login and the role sysadmin-----
		$this->load->library('UserAccess');
		$this->useraccess->LoginRequired();
		if(!$this->useraccess->HasRole(array('system admin'))) redirect('/', 'refresh');
		//-----------------------------------
	}

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/useradmin
	 *	- or -  
	 * 		http://example.com/index.php/useradmin/index
	 */
	public function index()
	{
		//if($role != 'admin') $this->editaccount($this->useraccess->CurrentUserId());
		$view_data = array();
		
		$this->load->view('systemadmin/system_admin',$view_data);

	}
	
	
	
}

/* End of file useradmin.php */
/* Location: ./application/controllers/useradmin.php */