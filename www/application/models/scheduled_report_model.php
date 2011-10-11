<?php

/**
 *  Scheduled_report_model
 *
 * @package Scheduled_eports
 */
 
class Scheduled_report_model extends CI_Model
{
	
	/** CLASS VARS **/
	private $db_table = 'scheduled_report';
	/****************/
	
	/** External Methods **/
	/**
	 * Create method creates an instance of the model
	 * 
	 * Option: Values (value* = required)
	 * --------------
	 * report_id*
	 * user_id*
	 * variables[]*
	 * day_of_month
	 * month_of_year
	 * day_of_week
	 * hour_of_day
	 * email_template
	 *
	 * Example:
	 * $this->{model name}->Create(
	 *   array(
	 * 		'report_id' => '1',
	 * 		'user_id' => '3',
	 * 		'variables' => array(),
	 * 		'day_of_week' => '1',
	 * 		'hour_of_day' => '2',
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
		$req_vals = array('report_id','user_id','variables');
		if(!fields_required($req_vals,$options))
		{
			log_message('error', __METHOD__.' options array missing required fields ');
			throw new Exception('missing required field');
			return false;
		}
		
		// encode the variables field
		$options['variables'] = json_encode($options['variables']);
		
		// add internal fields
		$options['created'] = mysql_date();
		$this->load->library('UserAccess');
		$options['created_by_user_id'] = $this->useraccess->CurrentUserId();
		
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
	 * report_id*
	 * user_id*
	 * variables[]*
	 * day_of_month
	 * month_of_year
	 * day_of_week
	 * hour_of_day
	 * email_template
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
		$req_vals = array('report_id','user_id','variables','id');
		if(!fields_required($req_vals,$object_array))
		{
			log_message('error', __METHOD__.' user object missing required fields ');
			throw new Exception('missing required field');
			return false;
		}
		
		// encode the variables field
		$object_array['variables'] = json_encode($object_array['variables']);
		
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
			
			// decode the variables field
			$object->variables = json_decode($object->variables);
			
			return $object;
		}else{
			return false;
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * GetByUserId method retreives instances of the model by the user to whom they will be sent
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
				// decode the variables field
				$object->variables = json_decode($object->variables);
				
				$objects[] = $object;
			}
			
			return $objects;
		}else{
			return false;
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * GetByUserId method retreives instances of the model by the user to whom they will be sent
	 *
	 * @param string $user_id
	 * @result stdObject[] $object[]
	 */
	public function GetReportsDue()
	{
		// read application setting for last run
		//$this->load->model('Application_setting_model');
		//$last_run = $this->Application_setting_model->GetByName('cron.report_processor.last_run');
		
		// if there was no application setting default to an hour ago
		//(!$last_run) $last_run = date(strtotime("one hour ago",time()));
		
		
		// read from database
		$query = 'SELECT * FROM '.$this->db_table.' WHERE ';
		$query .= "(day_of_month IN ('*','".date('d')."','".date('j')."') ";
		$query .= "AND (month_of_year IN ('*','".date('F')."','".date('m')."','".date('M')."','".date('n')."') ";
		$query .= "AND (day_of_week IN ('*','".date('D')."','".date('l')."','".date('N')."','".date('W')."') ";
		$query .= "AND (hour_of_day IN ('*','".date('G')."','".date('H')."') ";
		log_message('debug', __METHOD__.' query for reports due: '.$query);
		$result = $this->db->query($query)->result();
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