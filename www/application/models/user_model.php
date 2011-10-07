<?php

/**
 *  User_Model
 *
 * @package Users
 */
 
class User_model extends CI_Model
{
	
	private $acc_salt = '34q34545ytwgqegujyj6';
	
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
	public AddUser($options = array())
	{
		// required values
		$req_vals = array('email_address','username','password');
		if(!$this->_required($req_vals,$options)) return false;
		
		// validate email
		if(!valid_email($options['email_address']))
		
		// add internal fields
		$options['created'] = mysql_date();
		
		// hash password
		$options['password'] = sha1($this->acc_salt.$options['password']);
		
		// insert the user
		$this->db->insert('user',$options);
		
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
	public UsernameExists($username)
	{
		// make sure a username came through
		if(!isset($username) || $username == '') return false;
		
		
	}
	
	/**
	 * ValidateLogin checks to see if supplied login info is correct
	 *
	 */
	public ValidateLogin($username,$password){}
	
	/**
	 * DisableUser method disables a user
	 *
	 */
	public DisableUser($user_id){}
	
	/**
	 * GetUserByID method retreives a user object from the database by ID
	 *
	 */
	public GetUserByID($user_id){}
	
	/**
	 * GetUserByID method retreives a user object from the database by email address
	 *
	 */
	public GetUserByEmail($user_email){}
	
}
?>