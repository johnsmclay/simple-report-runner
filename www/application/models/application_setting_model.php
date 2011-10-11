<?php

/**
 *  Application_setting_model
 *
 * @package Scheduled_eports
 */
 
class Application_setting_model extends CI_Model
{
	
	/** CLASS VARS **/
	private $db_table = 'application_settings';
	/****************/
	
	/** External Methods **/
	/**
	 * Create method creates an instance of the model
	 * 
	 * Option: Values (value* = required)
	 * --------------
	 * name*
	 * value*
	 * type*
	 *
	 * Example:
	 * $this->{model name}->Create(
	 *   array(
	 * 		'name' => 'sleep.interval',
	 * 		'value' => '3',
	 * 		'type' => 'int',
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
		$req_vals = array('name','value','type');
		if(!fields_required($req_vals,$options))
		{
			log_message('error', __METHOD__.' options array missing required fields ');
			throw new Exception('missing required field');
			return false;
		}
		
		// insert the record
		log_message('debug', __METHOD__.' new report being scheduled: '.json_encode($options));
		$this->db->insert($this->db_table,$options);
		
		// retrieve ID and send it back
		$new_id = $this->db->insert_id();
		log_message('debug', __METHOD__.' new record created successfully.  ID = '.$new_id);
		
		return $this->GetScheduleByID($new_id);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Update method updates an instance of the model
	 * 
	 * Option: Values (value* = required)
	 * --------------
	 * id*
	 * name*
	 * value*
	 * type*
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
		$req_vals = array('name','value','type','id');
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
	 * GetByName method retreives an instance of the model by it's name
	 *
	 * @param string $name
	 * @result stdObject[] $object[]
	 */
	public function GetByName($name)
	{
		if(!isset($name)) return false;
		
		// read from database
		$result = $this->db->get_where($this->db_table,array('name'=>$name,),1)->result();
		log_message('debug', __METHOD__.' query result count '.count($result));
		
		// return object if there are any
		if(count($result) >= 1)
		{
			$object = $result[0];
			switch ($object->type) {
				case 'int':
					return intval($object->value);
					break;
				case 'string':
					return $object->value;
					break;
				case 'datetime':
					return date(strtotime($object->value));
					break;
				default:
					return $object;
			}
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