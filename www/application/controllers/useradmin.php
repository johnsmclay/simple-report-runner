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
		//if(!$this->useraccess->HasRole(array('system admin','user admin',))) redirect('/', 'refresh');
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
		if(!$this->useraccess->HasRole(array('system admin','user admin',)))
		{
			$this->editaccount($this->useraccess->CurrentUserId());
		}else{
			$this->listaccounts();
		}
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
		//if(!$this->useraccess->HasRole(array('system admin','user admin',))) $this->index();
		
		$this->load->model('Role_model');
		$available_roles = $this->Role_model->GetNameIdArray();
		
		$this->load->model('User_role_model');
		$user_roles_objects = $this->User_role_model->GetByUserId($user_id);
		$user_roles = array();
		if($user_roles_objects)
		{
			foreach($user_roles_objects as $user_roles_object)
			{
				$temp_array = get_object_vars($user_roles_object);
				$user_roles[$temp_array['role_id']] = $temp_array['name'];
			}
		}
		
		$view_data = array(
			'user' => $this->User_model->GetByID($user_id),
			'hidden_fields' => array(
				'user_id' => $user_id,
			),
			'available_roles' => $available_roles,
			'user_roles' => $user_roles,
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
		
		log_message('info', __METHOD__.' called with: '.json_encode($_REQUEST));
		
		if ($this->form_validation->run() == FALSE)
		{
			log_message('error', __METHOD__.' validation failed.  Redirecting to Account list.');
			if(!isset($_REQUEST['user_id']))
			{
				$this->listaccounts();
			}else{
				$this->editaccount($_REQUEST['user_id']);
			}
		}
		else
		{
			log_message('debug', __METHOD__.' validation passed.');
			if(!isset($_REQUEST['user_id']))
			{
				// must be an admin to create users
				if(!$this->useraccess->HasRole(array('system admin','user admin',))) $this->index();
				log_message('debug', __METHOD__.' no user_id found, the account will need to be created.');
				$this->form_validation->set_rules('password', 'Password', 'required|xss_clean|min_length[8]|max_length[60]');
				$this->form_validation->set_rules('confirm_password', 'Password Confirmation', 'required|matches[password]|xss_clean');
				if (!$this->form_validation->run())
				{
					log_message('debug', __METHOD__.' password rule validation failed.  Redirecting to Account list.');
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
				// validate the user_id
				log_message('debug', __METHOD__.' user_id found, the account will need to be updated.');
				
				// only an admin or the actual user can edit a user's account
				if(!$this->useraccess->HasRole(array('system admin','user admin',)) && $this->useraccess->CurrentUserId() !=  $_REQUEST['user_id']) $this->index();
				
				$this->form_validation->set_rules('user_id', 'user id', 'required|min_length[1]|integer|xss_clean');
				if($_REQUEST['password'] != '')
				{
					// user has submitted a password so we will validate it as well
					$this->form_validation->set_rules('password', 'Password', 'required|xss_clean|min_length[8]|max_length[60]');
					$this->form_validation->set_rules('confirm_password', 'Password Confirmation', 'required|matches[password]|xss_clean');
				}
				
				if (!$this->form_validation->run())
				{
					log_message('debug', __METHOD__.' validation failed.  Returning to edit pane.');
					$this->editaccount($_REQUEST['user_id']);
					return true;
				}
				
				log_message('debug', __METHOD__.' validation passed.  Updating user.');
				
				$user_array = array(
					'id' => $_REQUEST['user_id'],
					'fname' => $_REQUEST['fname'],
					'lname' => $_REQUEST['lname'],
					'username' => $_REQUEST['username'],
					'email_address' => $_REQUEST['email_address'],
				);
				
				if($_REQUEST['password'] != '')
				{
					// user has submitted a password so we will update it as well
					$user_array['password'] = $_REQUEST['password'];
				}
				
				try {
					$user = $this->User_model->UpdateUser($user_array);
					
					if($user)
					{
						$id = $user->id;
						log_message('debug', __METHOD__." user # $id updated");
					}
				} catch (Exception $e) {
					echo 'Caught exception: ',  $e->getMessage(), "\n";
					log_message('debug', __METHOD__.'Caught exception: '.$e->getMessage());
				}
				
			}
			
			if(!isset($_REQUEST['user_id']))
			{
				$this->listaccounts();
			}else{
				$this->editaccount($_REQUEST['user_id']);
			}
		}
	}
	
	public function deleteaccount()
	{
		// only admins can delete accounts
		if(!$this->useraccess->HasRole(array('system admin','user admin',))) $this->index();
		
		// validate fields
		$this->form_validation->set_rules('user_id', 'User ID', 'required|min_length[1]|integer|xss_clean');
		if (!$this->form_validation->run())
		{
			log_message('debug', __METHOD__.' validation failed.  Returning to edit pane.');
			$this->editaccount($_REQUEST['user_id']);
			return true;
		}
		
		// disable the user
		$this->User_model->DisableUser($_REQUEST['user_id']);
		
		// take back to account list as the user is no longer here
		$this->listaccounts();
	}
	
	public function saveroles()
	{
		// only admins can edit roles
		if(!$this->useraccess->HasRole(array('system admin','user admin',))) $this->index();
		
		$this->form_validation->set_rules('user_id', 'User ID', 'min_length[1]|integer|xss_clean');
		$this->form_validation->set_rules('roles', 'Roles', 'required|is_array');
		
		log_message('info', __METHOD__.' called with: '.json_encode($_REQUEST));
		
		if (!$this->form_validation->run())
		{
			if(!isset($_REQUEST['user_id']))
			{
				$this->listaccounts();
			}else{
				$this->editaccount($_REQUEST['user_id']);
			}
		}
		
		// see what roles exist
		$this->load->model('Role_model');
		$available_roles = $this->Role_model->GetNameIdArray();
		
		// find out what the user and admin have currently
		$this->load->model('User_role_model');
		$role_objects = $this->User_role_model->GetByUserId($_REQUEST['user_id']);
		$user_roles = array();
		if($role_objects)
		{
			foreach($role_objects as $role_object)
			{
				$user_roles[] = $role_object->role_id;
			}
		}
		
		$roles_to_add = array();
		$roles_to_remove = array();
		
		foreach($available_roles as $available_role_id => $available_role_name)
		{
			if($this->useraccess->HasRole(array('system admin',$available_role_name)))
			{
				if(in_array($available_role_id,$_REQUEST['roles']))
				{
					if(!in_array($available_role_id,$user_roles)) $roles_to_add[] = $available_role_id;
				}else{
					if(in_array($available_role_id,$user_roles)) $roles_to_remove[] = $available_role_id;
				}
			}
		}
		log_message('debug', __METHOD__.' Roles to add: '.json_encode($roles_to_add).' Roles to remove: '.json_encode($roles_to_remove));
		
		foreach($roles_to_add as $role)
		{
			$this->User_role_model->Create(array(
				'role_id' => $role,
				'user_id' => $_REQUEST['user_id'],
			));
		}
		
		foreach($roles_to_remove as $role)
		{
			$this->User_role_model->Delete($_REQUEST['user_id'],$role);
		}
		
		redirect('useradmin/editaccount/'.$_REQUEST['user_id']);
	}
}

/* End of file useradmin.php */
/* Location: ./application/controllers/useradmin.php */