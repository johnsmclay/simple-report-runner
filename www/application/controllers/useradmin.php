<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Useradmin extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('User_model');
		$this->load->helper(array('form','url'));
		$this->load->library(array('form_validation','table','session'));
		
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
		//if($role != 'admin') $this->editaccount($this->useraccess->CurrentUserId());
		
		$this->listaccounts();

	}
	
	public function listaccounts()
	{
		//TODO: make sure they have the admin role
		
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
	}
	
	public function editaccount($user_id)
	{
		if(!isset($user_id)) $this->index();
		//if($role != 'admin' && $user_id != $this->useraccess->CurrentUserId()) $this->index();
		
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
		$this->form_validation->set_rules('user_id', 'User ID', 'min_length[1]|integer|xss_clean');
		$this->form_validation->set_rules('fname', 'First Name', 'required|min_length[1]|max_length[60]|xss_clean');
		$this->form_validation->set_rules('lname', 'Last Name', 'required|min_length[1]|max_length[60]|xss_clean');
		$this->form_validation->set_rules('username', 'Username', 'required|min_length[5]|max_length[60]|xss_clean');
		$this->form_validation->set_rules('password', 'Password', 'xss_clean');
		$this->form_validation->set_rules('confirm_password', 'Password Confirmation', 'matches[password]|xss_clean');
		$this->form_validation->set_rules('email_address', 'Email Address', 'required|min_length[5]|max_length[60]|valid_email|xss_clean');
		
		if ($this->form_validation->run() == FALSE)
		{
			if(!isset($_REQUEST['user_id']))
			{
				$this->listaccounts();
			}else{
				$this->editaccount();
			}
		}
		else
		{
			if(!isset($_REQUEST['user_id']))
			{
				$this->form_validation->set_rules('password', 'Password', 'required|xss_clean|min_length[8]|max_length[60]');
				$this->form_validation->set_rules('confirm_password', 'Password Confirmation', 'required|matches[password]|xss_clean');
				if (!$this->form_validation->run())
				{
					$this->listaccounts();
					return true;
				}
				
				// create user
				$user_array = array(
					'fname' => $_REQUEST['fname'],
					'lname' => $_REQUEST['lname'],
					'username' => $_REQUEST['username'],
					'password' => $_REQUEST['password'],
					'email_address' => $_REQUEST['email_address'],
				);
				try {
					$user = $this->User_model->AddUser($user_array);
					
					if($user)
					{
						$new_id = $user->id;
						log_message('debug', __METHOD__." new user # $new_id created");
					}
				} catch (Exception $e) {
					echo 'Caught exception: ',  $e->getMessage(), "\n";
					log_message('debug', __METHOD__.'Caught exception: '.$e->getMessage());
				}
			}else{
				// update user
				
			}
			
			$this->index();
		}
	}
	
	
}

/* End of file useradmin.php */
/* Location: ./application/controllers/useradmin.php */