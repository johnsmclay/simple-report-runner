<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class UserAccess {
	
	///////////////////////
	/// CLASS VARS
	///////////////////////
	private $CI;

	///////////////////////
	/// PUBLIC FUNCTIONS
	///////////////////////
	
    /**
	 * UserAccess Constructor
	 *
	 * The constructor runs the session routines automatically
	 * whenever the class is instantiated.
	 */
	public function __construct($options = array())
	{
		log_message('debug', "UserAccess Class Initialized");

		// Set the super object to a local variable for use throughout the class
		$this->CI =& get_instance();
		
		// we are going to be using the session a lot in here
		$this->CI->load->library('session');

		log_message('debug', "Session routines successfully run");
	}

	// --------------------------------------------------------------------
	
	/**
	 * login method logs a user in
	 *
	 * Example:
	 * $this->UserAccess->login('cool_dude22','asdfasdf');
	 *
	 * @param string $username
	 * @param string $password
	 * @result bool $logged_in
	 */
	public function login($username,$password)
	{
		// ensure we recieved the credentials
		if(!isset($username)) return false;
		if(!isset($password)) return false;
		
		// retrieve the user
		$this->CI->load->model('User_model');
		$user = $this->CI->User_model->ValidateLogin($username,$password);
		if(!$user) return false;
		
		$this->_CreateUserSession($user);
		
		return true;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * _CreateUserSession Sets us a user's session
	 *
	 * Example:
	 * $this->_CreateUserSession($user_object);
	 *
	 * @param stdObject $user
	 * @result bool $session_created
	 */
	public function _CreateUserSession($user_object)
	{
		// ensure we recieved the credentials
		if(!is_object($user_object)) return false;
		
		$session_data = array(
			'user_id' => $user_object->id,
		);
		
		$this->CI->session->set_userdata($session_data);
		
		return true;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * TokenLogin method logs a user in via token (SSO, login link, etc.)
	 *
	 * Example:
	 * $this->UserAccess->TokenLogin($token);
	 *
	 * @param string $token
	 * @result bool $logged_in
	 */
	public function TokenLogin($token)
	{
		$nonce_type = 'login';
		$this->CI->load->library('nonce',array('nonce_types' => array($nonce_type)));
		$user_id = $this->CI->nonce->use_nonce($nonce_type,$token);
		if(!$user_id) return false;
		$this->CI->load->model('User_model');
		$user = $this->CI->User_model->GetUserByID($user_id);
		$this->_CreateUserSession($user);
		redirect('/', 'refresh');
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * logout method logs a user out
	 * If that user is logged in as someone else it reverts the session to the original account
	 *
	 * Example:
	 * $this->UserAccess->logout();
	 *
	 * @result bool $logged_out
	 */
	public function logout()
	{
		$this->CI->session->unset_userdata('user_id');
		$old_user_id = $this->CI->session->userdata('old_user_id');
		if(isset($old_user_id))
		{
			if(is_numeric($old_user_id))
			{
				$this->session->set_userdata('user_id', $old_user_id);
				redirect('/', 'refresh');
			}
		}
		
		return true;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * LoginAs method saves current user id and switches the session over to another account
	 *
	 * Example:
	 * $this->UserAccess->LoginAs($user_id);
	 *
	 * @param string $user_id
	 * @result bool $logged_in
	 */
	public function LoginAs($user_id)
	{
		$current_user_id = $this->CI->session->userdata('user_id');
		$this->CI->session->set_userdata('old_user_id', $current_user_id);
		$this->CI->load->model('User_model');
		$user = $this->CI->User_model->GetUserByID($user_id);
		$this->_CreateUserSession($user);
		redirect('/', 'refresh');
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * CurrentUserId method returns the id of the current user
	 *
	 * Example:
	 * $this->UserAccess->CurrentUserId();
	 *
	 * @result int $current_user_id
	 */
	public function CurrentUserId()
	{
		log_message('debug', __METHOD__.' called ');
		$current_user_id = $this->CI->session->userdata('user_id');
		log_message('debug', __METHOD__.' current_user_id = '.$current_user_id);
		
		return $current_user_id;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * LoginRequired method checks to see if the user is logged in and redirects if the user isn't
	 *
	 * Example:
	 * $this->UserAccess->LoginRequired();
	 *
	 * @result int $current_user_id
	 */
	public function LoginRequired()
	{
		log_message('debug', __METHOD__.' called ');
		
		$user_id = $this->CurrentUserId();
		if(!isset($user_id) || $user_id == '')
		{
			log_message('debug', __METHOD__.' no user is logged in. Redirecting to login.');
			$this->RedirectToLogin();
		}
		
		log_message('debug', __METHOD__.' user #'.$user_id.' is logged in');
		
		return $current_user_id;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * RedirectToLogin method referrs the user to the login page
	 *
	 * Example:
	 * $this->UserAccess->RedirectToLogin();
	 *
	 * @result bool $successful
	 */
	public function RedirectToLogin()
	{
		log_message('debug', __METHOD__.' called ');
		
		$this->CI->load->helper('url');
		$this->CI->session->set_flashdata('error', 'You must be logged in to view this page.');
		$this->CI->session->set_flashdata('last_page', uri_string());
		redirect('/login/', 'refresh');
	}
	
	// --------------------------------------------------------------------
}

/* End of file UserAccess.php */
