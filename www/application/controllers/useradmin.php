<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Useradmin extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('User_model');
		//----- This page requires login-----
		$this->load->library('UserAccess');
		$this->useraccess->LoginRequired();
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
		//if($role = 'admin')
		//{
			$view_data = array(
				'active_users' =>  $this->User_model->GetAllActive(),
			);
			$this->load->view('useradmin/admin',$view_data);
		//}else{
		//	$this->editaccount($this->useraccess->CurrentUserId());
		//}
	}
	
	public function editaccount($user_id)
	{
		if(!isset($user_id)) $this->index();
		//if($role != 'admin' && $user_id != $this->useraccess->CurrentUserId()) $this->index();
		
		$view_data = array(
			'user_id' => $user_id,
		);
		$this->load->view('useradmin/editaccount',$view_data);
	}
	
	
}

/* End of file useradmin.php */
/* Location: ./application/controllers/useradmin.php */