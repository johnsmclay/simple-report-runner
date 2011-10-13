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
			$this->load->helper('url');
			$this->load->helper('form');
			$this->load->library('table');
			$tmpl = array (
                    'table_open'          => '<table border="0" cellpadding="4" cellspacing="0">',

                    'heading_row_start'   => '<tr>',
                    'heading_row_end'     => '</tr>',
                    'heading_cell_start'  => '<th>',
                    'heading_cell_end'    => '</th>',

                    'row_start'           => '<tr>',
                    'row_end'             => '</tr>',
                    'cell_start'          => '<td>',
                    'cell_end'            => '</td>',

                    'row_alt_start'       => '<tr>',
                    'row_alt_end'         => '</tr>',
                    'cell_alt_start'      => '<td>',
                    'cell_alt_end'        => '</td>',

                    'table_close'         => '</table>'
              );
			$this->table->set_template($tmpl);
			$user_list[] = array(
				'User',
				'Login',
				'Email Address',
				'Action',
			);
			foreach($this->User_model->GetAllActive() as $active_user)
			{
				$row = array(
					$active_user->lname . ', ' . $active_user->fname,
					$active_user->username,
					$active_user->email_address,
					'<a href="'.base_url().'useradmin/editaccount/'.$active_user->id.'" >Edit</a>',
				);
				$user_list[] = $row;
			}
			
			
			$view_data = array(
				'user_list' => $user_list,
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
		
		$this->load->helper('form');
		
		$view_data = array(
			'user' => $this->User_model->GetUserByID($user_id),
			'hidden_fields' => array(
				'user_id' => $user_id,
			),
		);
		$this->load->view('useradmin/editaccount',$view_data);
	}
	
	public function saveaccount()
	{
		
	}
	
	
}

/* End of file useradmin.php */
/* Location: ./application/controllers/useradmin.php */