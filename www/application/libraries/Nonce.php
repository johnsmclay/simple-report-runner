<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
/**
*  Nonce Library
*    Description:  creates and manage nonces (arbitrary random identifiers with limited use)
*    Usage:
*     $this->load->library('nonce',array('nonce_types' => array('test_type')));
*     $this->nonce->create_nonce('test_type',1,10);
*     $this->nonce->use_nonce('test_type',$nonce);
*     $this->nonce->invalidate_nonce('test_type',$nonce);
*/
class Nonce {

	///////////////////////
	/// CLASS VARS
	///////////////////////
	
	/**
    * The CodeIgniter Object
    * 
    * @var object
    */
	protected $ci;
	
	/**
	* Possible nonce types
	* 
	* @var array
	*/
	protected $nonce_types;
	
	///////////////////////
	/// PUBLIC FUNCTIONS
	///////////////////////
	
	function __construct($options=array())
	{	
		// Load the Codeigniter object for use in the class
		$this->ci =& get_instance();
		
		//Load the library config file if no config was sent
		if(count($options) == 0){
			log_message('debug', __METHOD__.' no options passed, reading from config file...');
			$this->ci->config->load('nonce', true);
			$options = $this->config->item('nonce');
		}else{
			log_message('debug', __METHOD__.' options passed: '.json_encode($options));
		}
		
        // You can specifically overide any of the default configuration options setup above
        if (count($options)>0){
            if (array_key_exists("nonce_types",$options)){ $this->nonce_types=$options["nonce_types"]; }
        }
		
    }
	// --------------------------------------------------------------------
	
	public function create_nonce($type,  $max_uses = 1, $data = null)
	{
		log_message('debug', __METHOD__.' called with type : '.$type.' and max_uses : '.$max_uses);
		
		// will hold the new nonce
		$return = FALSE;
		
		// make sure the type is valid
		if(in_array($type, $this->nonce_types))
		{
			// create the nonce in the DB
			$insert_id = $this->insert_nonce($type,$max_uses,$data);
			log_message('debug', __METHOD__.' nonce created. insert_id : '.$insert_id);
			
			// pull the new record from the DB
			$result = $this->ci->db->get_where('nonce',array('id'=>$insert_id))->result();
			$nonce = $result[0];
			
			// locate the hash
			$return = $nonce->hash;
		}else
		{
			// not a valid type
			log_message('error', __METHOD__.' Invalid nonce type : '.$type);
		}
		
		// return false if we were unable to create a nonce
		return $return;
	}
	// --------------------------------------------------------------------
	
	public function use_nonce($type, $hash)
	{
		// we are going to default to rejecting the request
		$response = FALSE;
		
		// make sure the type is valid
		if(in_array($type, $this->nonce_types))
		{
			// returns a nonce record array if found, FALSE if not found
			$nonce = $this->get_nonce_by_hash($type, $hash);
			
			// if a record was found
			if($nonce)
			{
				// make sure it isn't already used up
				if($nonce['use_count'] < $nonce['max_uses'])
				{
					// Yay! That means we can use it
					$response = TRUE;
					
					if(isset($nonce['data']))
						if($nonce['data'] != '') $response = $nonce['data'];
				}else
				{
					// the nonce is already used up :-(
					log_message('error', __METHOD__.' Nonce already used. Uses : '.$nonce['use_count'].' Max : '.$nonce['max_uses']);
				}
				
				// we need to record the try, even if it failed
				$this->increment_usage($nonce,$response);
			}else
			{
				// nonce not found
				log_message('error', __METHOD__.' Nonce not found. Type : '.$type.' Hash : '.$hash);
			}
			
		}else
		{
			// not a valid type
			log_message('error', __METHOD__.' Invalid nonce type : '.$type);
		}
		
		return $response;
	}
	// --------------------------------------------------------------------
	
	public function invalidate_nonce($type, $hash)
	{
		// we are going to default to rejecting the request
		$response = FALSE;
		
		// make sure the type is valid
		if(in_array($type, $this->nonce_types))
		{
			$response = $this->mark_nonce_as_deleted($type, $hash);
		}else
		{
			// not a valid type
			log_message('error', __METHOD__.' -- Invalid nonce type : '.$type);
		}
		
		return $response;
	}
	// --------------------------------------------------------------------
	
	///////////////////////
	/// PRIVATE FUNCTIONS
	///////////////////////
	
	private function mark_nonce_as_deleted($type, $hash)
	{
		$response = TRUE;
		
		log_message('debug', __METHOD__.' -- called. Type : '.$type.' Hash : '.$hash);
		
		// returns a nonce record array if found, FALSE if not found
		$nonce = $this->get_nonce_by_hash($type, $hash, FALSE);
		
		// if a record was found
		if($nonce)
		{
			$data = array(
			   'deleted' => date("Y-m-d H:i:s"),
			);

			$this->ci->db->where('id', $nonce['id']);
			$this->ci->db->update('nonce', $data);
		}else
		{
			// nonce not found
			$response = FALSE;
			log_message('error', __METHOD__.' -- Nonce not found. Type : '.$type.' Hash : '.$hash);
		}
		
		return $response;
	}
	// --------------------------------------------------------------------
		
	private function increment_usage($nonce,$success)
	{
		log_message('debug', __METHOD__.' -- incrementing usage for nonce: '.json_encode($nonce));
		
		// assume it is uses
		$incremented_column = 'use_count';
		
		// see if we are incrementing the failures or successes
		if(!$success)
		{
			$incremented_column = 'failed_attempts';
		}
		
		$data = array(
		   $incremented_column => $nonce[$incremented_column]+1,
		);

		$this->ci->db->where('id', $nonce['id']);
		$this->ci->db->update('nonce', $data);
		log_message('debug', __METHOD__.' -- update statement: '.$this->ci->db->last_query());
	}
	// --------------------------------------------------------------------
	
	private function insert_nonce($type, $max_uses = 1, $data = null)
	{
		$nonce_data = array(
			'type' => $type,
			'hash' => $this->gen_hash(),
			'max_uses' => $max_uses,
			'created' => date("Y-m-d H:i:s"),
		);
		if(!is_null($data)) $nonce_data['data'] = $data;
		
		$this->ci->db->insert('nonce', $nonce_data);
		
		return $this->ci->db->insert_id();
	}
	// --------------------------------------------------------------------
	
	private function gen_hash()
	{
		$date = date("Y-m-d H:i:s");
		return sha1($date.rand());
	}
	// --------------------------------------------------------------------
	
	private function get_nonce_by_hash($type, $hash, $skip_deleted = TRUE)
	{
		// pull the new record from the DB
		$data = array(
			'hash'=>$hash,
			'type'=>$type,
		);
		
		if($skip_deleted){$data['deleted'] = 0;}
		
		$result =$this->ci->db->get_where('nonce',$data)->result_array();
		
		log_message('debug', __METHOD__.' -- select statement: '.$this->ci->db->last_query());
		
		if(count($result) > 0)
		{
			log_message('debug', __METHOD__.' -- found nonce: '.json_encode($result[0]));
			return $result[0];
		}else
		{
			// nonce not found
			log_message('error', __METHOD__.' -- Nonce not found. Type : '.$type.' Hash : '.$hash);
		}
		
		// return false if we were unable to find the nonce
		return FALSE;
	}
	// --------------------------------------------------------------------
}
/*
Table Creation:

CREATE TABLE `nonce` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` enum('upload') NOT NULL,
  `hash` varchar(60) NOT NULL,
  `use_count` int(11) NOT NULL DEFAULT '0',
  `max_uses` int(11) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL,
  `last_used` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `failed_attempts` int(11) NOT NULL DEFAULT '0',
  `deleted` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `IDX_unique` (`type`,`hash`)
) DEFAULT CHARSET=latin1;

*/
/* End of file Nonce.php */