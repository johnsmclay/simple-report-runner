<?php

/**
 *  User_Model
 *
 * @package Users
 */
 
class User_model extends CI_Model
{
	/** Utility Methods **/
	function _required($required,$data)
	{
		foreach($required as $field)
			if(!isset($data[$field])) return false;
		
		return true;
	}
	
	function __construct()
	{
		parent::__construct();
		$this->load->database('main');
	}
	
	/** User Methods **/
	
	/**
	 * AddUser method creates a user
	 * 
	 * Option: Values
	 * --------------
	 * email
	 * password
	 *
	 * @param array $options
	 * @result int insert_id()
	 */
	public AddUser($options = array())
	{
		// required values
		$req_vals = array('email_address','username','password');
		if(!$this->_required($req_vals,$options)) return false;
		
		// add internal fields
		$options['created'] = 
		
		$this->db->insert('user',$options);
		
		
	}
	
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