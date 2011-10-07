<?php

/**
 *  User_Model
 *
 * @package Users
 */
 
class User_model extends CI_Model
{
	
	private $acc_salt = '34q34545ytwgqegujyj6';
	private $db_table = 'user';
	
	function __construct()
	{
		$this->load->helper('modelutils');
		$this->load->helper('email');
		$this->load->database('application');
		parent::__construct();
	}
	
	/** User Methods **/
	
	/**
	 * AddUser method creates a user
	 * 
	 * Option: Values (value* = required)
	 * --------------
	 * email_address*
	 * password*
	 * username*
	 * fname
	 * lname
	 * deleted
	 *
	 * Example:
	 * $this->User_model->AddUser(array(
	 * 		'email_address' => 'this@that.com',
	 * 		'username' => 'cool_dude22',
	 * 		'password' => 'asdfasdf',
	 * ));
	 *
	 * @param array $options
	 * @result int insert_id()
	 */
	public function AddUser($options = array())
	{
		log_message('debug', __METHOD__.' called with '.json_encode($options));
		
		// required values
		$req_vals = array('email_address','username','password');
		if(!fields_required($req_vals,$options))
		{
			log_message('error', __METHOD__.' options array missing required fields ');
			throw new Exception('missing required field');
			return false;
		}
		
		// make sure the username is not already in use
		if($this->UsernameExists($options['username']))
		{
			
			log_message('error', __METHOD__.' username "'.$options['username'].'" already in use');
			throw new Exception('username "'.$options['username'].'" already in use');
			return false;
		}
		
		// validate email
		if(!valid_email($options['email_address']))
		{
			log_message('error', __METHOD__.' email address "'.$options['email_address'].'" is invalid');
			throw new Exception('email address "'.$options['email_address'].'" is invalid');
			return false;
		}
		
		// add internal fields
		$options['created'] = mysql_date();
		
		// hash password
		$options['password'] = $this->_HashPassword($options['password']);
		
		// insert the user
		log_message('debug', __METHOD__.' new user being inserted: '.json_encode($options));
		$this->db->insert($this->db_table,$options);
		
		// retrieve ID and send it back
		$new_user_id = $this->db->insert_id();
		log_message('debug', __METHOD__.' new user created successfully.  ID = '.$new_user_id);
		return $new_user_id;
	}
	
	private function _HashPassword($password)
	{
		return sha1($this->acc_salt.$password);
	}
	
	/**
	 * UsernameExists checks to see if an account with that username exists
	 *
	 * Example:
	 * $this->User_model->UsernameExists('cool_dude22');
	 *
	 * @param string $username
	 * @result bool $user_exists
	 */
	public function UsernameExists($username)
	{
		log_message('debug', __METHOD__.' called with username "'.$username.'".');
		
		// make sure a username came through
		if(!isset($username) || $username == '') return false;
		
		// check the database
		$this->db->where(array('username'=>$username,));
		$this->db->from($this->db_table);
		$count = $this->db->count_all_results();
		log_message('debug', __METHOD__.' query result count '.$count);
		
		
		// return true if there are any
		if($count >= 1) return true;
		
		return false;
	}
	
	/**
	 * ValidateLogin checks to see if supplied login info is correct
	 * 
	 * Example:
	 * $this->User_model->ValidateLogin('cool_dude22','asdfasdf');
	 * 
	 * @param string $username
	 * @param string $password
	 * @result bool $account_validated
	 */
	public function ValidateLogin($username,$password)
	{
		log_message('debug', __METHOD__.' called with username "'.$username.'" and password "'.$password.'" whose hash would be "'.$this->_HashPassword($password).'".');
		
		// make sure both parameters came through
		if(!isset($username) || $username == '') return false;
		if(!isset($password) || $password == '') return false;
		
		// check the database
		$this->db->where(array(
			'username' => $username,
			'password' => $this->_HashPassword($password),
		));
		$this->db->from($this->db_table);
		$count = $this->db->count_all_results();
		log_message('debug', __METHOD__.' query result count '.$count);
		
		// return true if there are any
		if($count >= 1) return true;
		
		return false;
	}
	
	/**
	 * DisableUser method disables a user
	 *
	 */
	public function DisableUser($user_id){}
	
	/**
	 * DeleteUser method DELETES a user FOR REAL ***USE ONLY FOR TESTING***
	 *
	 */
	public function DeleteUser($user_id)
	{
		log_message('debug', __METHOD__.' called with user ID "'.$user_id.'" -- WARNING!!! DO NOT USE!!!.');
		
		if(isset($user_id))
		{
			$this->db->delete($this->db_table,array('id'=>$user_id,));
			log_message('debug', __METHOD__.' user with ID "'.$user_id.'" -- DELETED!!!.');
		}
	}
	
	/**
	 * GetUserByID method retreives a user object from the database by ID
	 *
	 */
	public function GetUserByID($user_id){}
	
	/**
	 * GetUserByID method retreives a user object from the database by email address
	 *
	 */
	public function GetUserByEmail($user_email){}
	
}
?>