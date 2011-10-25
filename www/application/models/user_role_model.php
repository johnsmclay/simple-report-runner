<?php

/**
 *  Scheduled_report_model
 *
 * @package Scheduled_eports
 */
 
class User_role_model extends CI_Model
{
	
	/** CLASS VARS **/
	private $db_table = 'user_role';
	private $roles;
	/****************/
	
	/** External Methods **/
	/**
	 * Create method creates an instance of the model
	 * 
	 * Option: Values (value* = required)
	 * --------------
	 * user_id*
	 * role_id*
	 *
	 * Example:
	 * $this->{model name}->Create(
	 *   array(
	 * 		'role_id' => '1',
	 * 		'user_id' => '3',
	 *   )
	 * );
	 *
	 * @param array $options
	 * @result stdObject $object
	 */
	public function Create($options = array())
	{
		log_message('debug', __METHOD__.' called with '.json_encode($options));
		
		// required values
		$req_vals = array('role_id','user_id');
		if(!fields_required($req_vals,$options))
		{
			log_message('error', __METHOD__.' options array missing required fields ');
			throw new Exception('missing required field');
			return false;
		}
		
		// add internal fields
		$options['created'] = mysql_date();
		$this->load->library('UserAccess');
		$options['created_by_user_id'] = $this->useraccess->CurrentUserId();
		
		// insert the record
		log_message('debug', __METHOD__.' new report being scheduled: '.json_encode($options));
		$this->db->insert($this->db_table,$options);
		
		log_message('debug', __METHOD__.' new record created successfully.');
		
		return true;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * GetAllActive method retreives a user object from the database
	 *
	 * @result array(stdObject) $active_objects
	 */
	public function GetAllActive()
	{
		// read from database
		//$query = $this->db->where('deleted',null);
		//$query = $this->db->or_where('deleted >',mysql_date());
		$query = $this->db->get($this->db_table);
		$result = $query->result();
		log_message('debug', __METHOD__.' query result count '.count($result));
		
		// return object if there are any
		if(count($result) >= 1)
		{
			$objects = array();
			foreach($result as $object)
			{
				// prep the object
				$this->_PrepObject($object);
				
				$objects[] = $object;
			}
			return $objects;
		}else{
			return false;
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Update method updates an instance of the model
	 * 
	 * Option: Values (value* = required)
	 * --------------
	 * id*
	 * role_id*
	 * user_id*
	 *
	 * Example:
	 * $this->{model name}->Update($object);
	 *
	 * @param stdObject $object
	 * @result stdObject $object
	 */
	// public function Update($object)
	// {
		// log_message('debug', __METHOD__.' called for id# '.$object->id);
		
		// $object_array = $object;
		// // convert object to array if is object
		// if(is_object($object)) $object_array = get_object_vars($object);
		
		// // required values
		// $req_vals = array('role_id','user_id','id');
		// if(!fields_required($req_vals,$object_array))
		// {
			// log_message('error', __METHOD__.' user object missing required fields ');
			// throw new Exception('missing required field');
			// return false;
		// }
		
		// // pull out id
		// $object_id = $object_array['id'];
		// unset($object_array['id']);
		
		// // update database
		// $this->db->where('id', $object_id);
		// $this->db->update($this->db_table, $object_array); 
		
		// // re-read from database
		// $object = $this->GetByID($object_id);
		
		// // return object if there are any
		// if(isset($object))
		// {
			// return $object;
		// }else{
			// return false;
		// }
	// }
	
	// --------------------------------------------------------------------
	
	/**
	 * Delete method DELETES an instance of the model FOR REAL
	 *
	 * Example:
	 * $this->{model name}->Delete(22);
	 * 
	 * @param string $object_id
	 * @result bool $success
	 */
	public function Delete($user_id,$role_id)
	{
		log_message('debug', __METHOD__.' called with user ID "'.$user_id.'" and role ID "'.$role_id.'" -- WARNING!!!.');
		
		if(isset($user_id) && isset($role_id))
		{
			$this->db->delete($this->db_table,array('user_id'=>$user_id,'role_id'=>$role_id,));
			log_message('debug', __METHOD__.' object with ID "'.$object_id.'" -- DELETED!!!.');
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Disable method disables an instance of the model
	 *
	 */
	public function Disable($user_id,$role_id)
	{
		log_message('debug', __METHOD__.' called with object ID "'.$object_id.'".');
		
		// if(isset($object_id))
		// {
			// $data = array('deleted'=>mysql_date(),);
			// $this->db->where('id', $object_id);
			// $this->db->update($this->db_table, $data); 
			// log_message('debug', __METHOD__.' object with ID "'.$object_id.'" disabled.');
		// }
		
		// There is no deleted property on this object so we are going to actually delete stuff
		$this->Delete($user_id,$role_id);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * GetByID method retreives a user object from the database by ID
	 *
	 * @param int $object_id
	 * @result stdObject $object
	 */
	// public function GetByID($object_id)
	// {
		// if(!isset($object_id)) return false;
		
		// // read from database
		// $result = $this->db->get_where($this->db_table,array('id'=>$object_id,),1)->result();
		// log_message('debug', __METHOD__.' query result count '.count($result));
		
		// // return object if there are any
		// if(count($result) >= 1)
		// {
			// $object = $result[0];
			
			// // decode the variables field
			// $object->variables = json_decode($object->variables);
			
			// return $this->_PrepObject($object);
		// }else{
			// return false;
		// }
	// }
	
	// --------------------------------------------------------------------
	
	/**
	 * GetByUserId method retreives instances of the model by the user to whom they belong
	 *
	 * @param string $user_id
	 * @result stdObject[] $object[]
	 */
	public function GetByUserId($user_id)
	{
		if(!isset($user_id)) return false;
		
		// read from database
		$result = $this->db->get_where($this->db_table,array('user_id'=>$user_id,))->result();
		log_message('debug', __METHOD__.' query result count '.count($result));
		
		// return object if there are any
		if(count($result) >= 1)
		{
			$objects = array();
			foreach($result as $object)
			{
				// prep the object
				$this->_PrepObject($object);
				
				$objects[] = $object;
			}
			return $objects;
		}else{
			return false;
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * GetByRoleId method retreives instances of the model by the role to whom they belong
	 *
	 * @param string $user_id
	 * @result stdObject[] $object[]
	 */
	public function GetByRoleId($role_id)
	{
		if(!isset($role_id)) return false;
		
		// read from database
		$result = $this->db->get_where($this->db_table,array('role_id'=>$role_id,))->result();
		log_message('debug', __METHOD__.' query result count '.count($result));
		
		// return object if there are any
		if(count($result) >= 1)
		{
			$objects = array();
			foreach($result as $object)
			{
				// prep the object
				$this->_PrepObject($object);
				
				$objects[] = $object;
			}
			
			return $objects;
		}else{
			return false;
		}
	}
	
	// --------------------------------------------------------------------
	/**********************/
	
	/** Internal Methods **/
	function __construct()
	{
		$this->load->helper('modelutils');
		$this->load->database('application');
		parent::__construct();
	}
	
	private function _PrepObject($object)
	{
		if(!isset($this->roles))
		{
			$this->load->model('Role_model');
			$this->roles = $this->Role_model->GetNameIdArray();
		}
		
		$object->name = $this->roles[$object->role_id];
		
		return $object;
	}	
	
	// --------------------------------------------------------------------
	/**********************/
	
}
?>