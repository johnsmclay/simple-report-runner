<?php

/**
 *  User_Model
 *
 * @package Users
 */
 
class User_model extends CI_Model
{
	
	/** CLASS VARS **/
	private $salt = '';
	private $db_table = 'user';
	/****************/
	
	/** External Methods **/
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
	 * parent_user_id
	 *
	 * Example:
	 * $this->User_model->AddUser(array(
	 * 		'email_address' => 'this@that.com',
	 * 		'username' => 'cool_dude22',
	 * 		'password' => 'asdfasdf',
	 * ));
	 *
	 * @param array $options
	 * @result stdObject $user
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
		
		return $this->GetUserByID($new_user_id);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * UpdateUser method updates an existing user
	 * 
	 * Option: Values (value* = required)
	 * --------------
	 * id
	 * email_address*
	 * password
	 * username*
	 * fname
	 * lname
	 * deleted
	 * parent_user_id
	 *
	 * Example:
	 * $this->User_model->UpdateUser($user_object);
	 *
	 * @param stdObject $user
	 * @result stdObject $user
	 */
	public function UpdateUser($user)
	{
		log_message('debug', __METHOD__.' called for user '.$user->username);
		
		$user_array = $user;
		// convert object to array if is object
		if(is_object($user)) $user_array = get_object_vars($user_array);
		
		// required values
		$req_vals = array('email_address','username','id');
		if(!fields_required($req_vals,$user_array))
		{
			log_message('error', __METHOD__.' user object missing required fields ');
			throw new Exception('missing required field');
			return false;
		}
		
		// make sure the username is not already in use
		if($this->UsernameExists($user_array['username'],$user_array['id']))
		{
			
			log_message('error', __METHOD__.' username "'.$user_array['username'].'" already in use');
			throw new Exception('username "'.$user_array['username'].'" already in use');
			return false;
		}
		
		// pull out id
		$user_id = $user_array['id'];
		unset($user_array['id']);
		
		// take care of password
		if(isset($user_array['password']))
		{
			$user_array['password'] = $this->_HashPassword($user_array['password']);
		}else{
			unset($user_array['password']);
		}
		
		// update database
		$this->db->where('id', $user_id);
		$this->db->update($this->db_table, $user_array); 
		
		// re-read from database
		$user = $this->GetUserByID($user_id);
		
		// return object if there are any
		if(isset($user))
		{
			unset($user->password);
			return $user;
		}else{
			return false;
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * UsernameExists checks to see if an account with that username exists
	 *
	 * Example:
	 * $this->User_model->UsernameExists('cool_dude22');
	 *
	 * @param string $username
	 * @result bool $user_exists
	 */
	public function UsernameExists($username,$exclude_id=0)
	{
		log_message('debug', __METHOD__.' called with username "'.$username.'".');
		
		// make sure a username came through
		if(!isset($username) || $username == '') return false;
		
		// check the database
		if($exclude_id != 0) $this->db->where_not_in('id',array($exclude_id));
		$this->db->where(array('username'=>$username,));
		$this->db->from($this->db_table);
		$count = $this->db->count_all_results();
		log_message('debug', __METHOD__.' query result count '.$count);
		
		
		// return true if there are any
		if($count >= 1) return true;
		
		return false;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * ValidateLogin checks to see if supplied login info is correct
	 * 
	 * Example:
	 * $this->User_model->ValidateLogin('cool_dude22','asdfasdf');
	 * 
	 * @param string $username
	 * @param string $password
	 * @result stdObject $user
	 */
	public function ValidateLogin($username,$password)
	{
		log_message('debug', __METHOD__.' called with username "'.$username.'" and password "'.$password.'" whose hash would be "'.$this->_HashPassword($password).'".');
		
		// make sure both parameters came through
		if(!isset($username) || $username == '') return false;
		if(!isset($password) || $password == '') return false;
		
		// check the database
		$where_data = array(
			'username' => $username,
			'password' => $this->_HashPassword($password),
			'deleted' => NULL,
		);
		$result = $this->db->get_where($this->db_table,$where_data,1)->result();
		log_message('debug', __METHOD__.' query result count '.count($result));
		
		// return true if there are any
		if(count($result) >= 1)
		{
			$user = $this->GetUserByID($result[0]->id);
			unset($user->password);
			return $user;
		}else{
			return false;
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * DisableUser method disables a user
	 *
	 */
	public function DisableUser($user_id)
	{
		log_message('debug', __METHOD__.' called with user ID "'.$user_id.'".');
		
		if(isset($user_id))
		{
			$data = array('deleted'=>mysql_date(),);
			$this->db->where('id', $user_id);
			$this->db->update($this->db_table, $data); 
			log_message('debug', __METHOD__.' user with ID "'.$user_id.'" disabled.');
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * DeleteUser method DELETES a user FOR REAL ***USE ONLY FOR TESTING***
	 *
	 * Example:
	 * $this->User_model->DeleteUser(22);
	 * 
	 * @param string $user_id
	 * @result bool $account_deleted
	 */
	public function DeleteUser($user_id)
	{
		// this method was created for testing purposes
		// crippling this method when not in development mode since we don't normally want to delete accounts
		if(ENVIRONMENT != 'development') return true;
		
		log_message('debug', __METHOD__.' called with user ID "'.$user_id.'" -- WARNING!!! DO NOT USE!!!.');
		
		if(isset($user_id))
		{
			$this->db->delete($this->db_table,array('id'=>$user_id,));
			log_message('debug', __METHOD__.' user with ID "'.$user_id.'" -- DELETED!!!.');
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * GetUserByID method retreives a user object from the database by ID
	 *
	 * @param int $user_id
	 * @result stdObject $user
	 */
	public function GetUserByID($user_id)
	{
		if(!isset($user_id)) return false;
		
		// read from database
		$result = $this->db->get_where($this->db_table,array('id'=>$user_id,),1)->result();
		log_message('debug', __METHOD__.' query result count '.count($result));
		
		// return object if there are any
		if(count($result) >= 1)
		{
			$user = $result[0];
			unset($user->password);
			return $user;
		}else{
			return false;
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * GetUserByUsername method retreives a user object from the database by username
	 *
	 * @param string $username
	 * @result stdObject $user
	 */
	public function GetUserByUsername($username)
	{
		if(!isset($username)) return false;
		
		// read from database
		$result = $this->db->get_where($this->db_table,array('username'=>$username,),1)->result();
		log_message('debug', __METHOD__.' query result count '.count($result));
		
		// return object if there are any
		if(count($result) >= 1)
		{
			$user = $result[0];
			unset($user->password);
			return $user;
		}else{
			return false;
		}
	}
	/**********************/
	
	/** Internal Methods **/
	function __construct()
	{
		$this->load->helper('modelutils');
		$this->load->helper('email');
		$this->load->database('application');
		$this->salt = UA_PASSWORD_SALT;
		parent::__construct();
	}	
	
	// --------------------------------------------------------------------
	
	/**
	 * _HashPassword encrypts a user's password
	 *
	 * Example:
	 * $this->_HashPassword('asdfasdf');
	 *
	 * @param string $password
	 * @result string $encrypted_password
	 */
	private function _HashPassword($password)
	{
		return sha1($this->salt.$password);
	}
	/**********************/
	
}
?>