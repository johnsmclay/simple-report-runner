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
		$this->load->helper('modelutils','email');
		$this->load->database();
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
	 * ->AddUser(array(
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
		// required values
		$req_vals = array('email_address','username','password');
		if(!fields_required($req_vals,$options))
		{
			throw new Exception('missing required field');
			return false;
		}
		
		// make sure the username is not already in use
		if($this->UsernameExists($options['username']))
		{
			
			log_message('error', __METHOD__.' username "'.$options['username'].'" already in use');
			throw new Exception('username already in use');
			return false;
		}
		
		// validate email
		if(!valid_email($options['email_address']))
		{
			log_message('error', __METHOD__.' email address "'.$options['email_address'].'" is invalid');
			throw new Exception('email address invalid');
			return false;
		}
		
		// add internal fields
		$options['created'] = mysql_date();
		
		// hash password
		$options['password'] = sha1($this->acc_salt.$options['password']);
		
		// insert the user
		$this->db->insert($this->db_table,$options);
		
		return $this->db->insert_id();
	}
	
	/**
	 * UsernameExists checks to see if an account with that username exists
	 *
	 * Example:
	 * ->UsernameExists('cool_dude22');
	 *
	 * @param string $username
	 * @result bool user_exists
	 */
	public function UsernameExists($username)
	{
		log_message('debug', __METHOD__.' called with username "'.$username.'".');
		
		// make sure a username came through
		if(!isset($username) || $username == '') return false;
		
		// check the database
		$this->db->get_where($this->db_table, array('username'=>$username,));
		
		// count the results
		if($this->db->count_all_results() >= 1) return true;
	}
	
	/**
	 * ValidateLogin checks to see if supplied login info is correct
	 *
	 */
	public function ValidateLogin($username,$password){}
	
	/**
	 * DisableUser method disables a user
	 *
	 */
	public function DisableUser($user_id){}
	
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