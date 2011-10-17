<?php

/**
 *  Scheduled_report_model
 *
 * @package Scheduled_eports
 */
 
class Role_model extends CI_Model
{
	
	/** CLASS VARS **/
	private $db_table = 'role';
	/****************/
	
	/** External Methods **/
	/**
	 * Create method creates an instance of the model
	 * 
	 * Option: Values (value* = required)
	 * --------------
	 * name*
	 * description
	 *
	 * Example:
	 * $this->{model name}->Create(
	 *   array(
	 * 		'name' => 'Administrator',
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
		$req_vals = array('name');
		if(!fields_required($req_vals,$options))
		{
			log_message('error', __METHOD__.' options array missing required fields ');
			throw new Exception('missing required field');
			return false;
		}
		
		// add internal fields
		$options['created'] = mysql_date();
		
		// insert the record
		log_message('debug', __METHOD__.' new object: '.json_encode($options));
		$this->db->insert($this->db_table,$options);
		
		// retrieve ID and send it back
		$new_id = $this->db->insert_id();
		log_message('debug', __METHOD__.' new record created successfully.  ID = '.$new_id);
		
		return $this->GetByID($new_id);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Update method updates an instance of the model
	 * 
	 * Option: Values (value* = required)
	 * --------------
	 * id*
	 * name*
	 * description
	 *
	 * Example:
	 * $this->{model name}->Update($object);
	 *
	 * @param stdObject $object
	 * @result stdObject $object
	 */
	public function Update($object)
	{
		log_message('debug', __METHOD__.' called for id# '.$object->id);
		
		$object_array = $object;
		// convert object to array if is object
		if(is_object($object)) $object_array = get_object_vars($object);
		
		// required values
		$req_vals = array('name','id');
		if(!fields_required($req_vals,$object_array))
		{
			log_message('error', __METHOD__.' user object missing required fields ');
			throw new Exception('missing required field');
			return false;
		}
		
		// pull out id
		$object_id = $object_array['id'];
		unset($object_array['id']);
		
		// update database
		$this->db->where('id', $object_id);
		$this->db->update($this->db_table, $object_array); 
		
		// re-read from database
		$object = $this->GetByID($object_id);
		
		// return object if there are any
		if(isset($object))
		{
			return $object;
		}else{
			return false;
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Delete method DELETES an instance of the model FOR REAL ***USE ONLY FOR TESTING***
	 *
	 * Example:
	 * $this->{model name}->Delete(22);
	 * 
	 * @param string $object_id
	 * @result bool $success
	 */
	public function Delete($object_id)
	{
		// this method was created for testing purposes
		// crippling this method when not in development mode since we don't normally want to delete accounts
		if(ENVIRONMENT != 'development') return true;
		
		log_message('debug', __METHOD__.' called with object ID "'.$object_id.'" -- WARNING!!! DO NOT USE!!!.');
		
		if(isset($object_id))
		{
			$this->db->delete($this->db_table,array('id'=>$object_id,));
			log_message('debug', __METHOD__.' object with ID "'.$object_id.'" -- DELETED!!!.');
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Disable method disables an instance of the model
	 *
	 */
	public function Disable($object_id)
	{
		log_message('debug', __METHOD__.' called with object ID "'.$object_id.'".');
		
		if(isset($object_id))
		{
			$data = array('deleted'=>mysql_date(),);
			$this->db->where('id', $object_id);
			$this->db->update($this->db_table, $data); 
			log_message('debug', __METHOD__.' object with ID "'.$object_id.'" disabled.');
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * GetByID method retreives a user object from the database by ID
	 *
	 * @param int $object_id
	 * @result stdObject $object
	 */
	public function GetByID($object_id)
	{
		if(!isset($object_id)) return false;
		
		// read from database
		$result = $this->db->get_where($this->db_table,array('id'=>$object_id,),1)->result();
		log_message('debug', __METHOD__.' query result count '.count($result));
		
		// return object if there are any
		if(count($result) >= 1)
		{
			$object = $result[0];
			
			return $object;
		}else{
			return false;
		}
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
		$query = $this->db->where('deleted',null);
		$query = $this->db->or_where('deleted >',mysql_date());
		$query = $this->db->get($this->db_table);
		$result = $query->result();
		log_message('debug', __METHOD__.' query result count '.count($result));
		
		// return object if there are any
		if(count($result) >= 1)
		{
			return $result;
		}else{
			return false;
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * GetNameIdArray method retreives objects from the database
	 *
	 * @result array(stdObject) $active_objects
	 */
	public function GetNameIdArray()
	{
		// read from database
		$query = $this->db->select(array('id','name'));
		$query = $this->db->where('deleted',null);
		$query = $this->db->or_where('deleted >',mysql_date());
		$query = $this->db->get($this->db_table);
		$result = $query->result();
		log_message('debug', __METHOD__.' query result count '.count($result));
		
		// return object if there are any
		if(count($result) >= 1)
		{
			$array = array();
			foreach($result as $single_result)
			{
				$array[$single_result->id] = $single_result->name;
			}
			return $array;
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
	
	// --------------------------------------------------------------------
	/**********************/
	
}
?>