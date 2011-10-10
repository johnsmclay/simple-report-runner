<?php
	class Connection_model extends CI_Model {
		
		function __construct()
		{
			parent::__construct();
			$this->load->database();
		}
		
		function getConnection($reportId)
		{
			$query ="
			SELECT 
				connection.*
			FROM
				mil_bi.report
				LEFT JOIN mil_bi.connection ON connection.id = report.connection_id 
			WHERE
				report.id = {$reportId}
			";

			$connectionResult = $this->db->query($query);
			$connectionData = $connectionResult->row_array();
			
			$type = preg_replace('/\s/', '', $connectionData['type']);
			
			// Concatenate a string that is used for calling the appropriate method
			$typeMethod = 'get' . $type . 'Connection';
			
			// Call the variable function defined by the connection type
			$connection = $this->$typeMethod($connectionData);
			
			
			return $connection;
		}

		function getMySQLConnection($connectionData)
		{
			$connection['hostname'] = $connectionData['hostname'];
			$connection['username'] = $connectionData['username'];
			$connection['password'] = $connectionData['password'];
			$connection['database'] = $connectionData['database'];
			$connection['dbdriver'] = strtolower($connectionData['type']);
			$connection['dbprefix'] = $connectionData['dbprefix'];
			$connection['pconnect'] = $connectionData['pconnect'];
			$connection['db_debug'] = $connectionData['db_debug'];
			$connection['cache_on'] = $connectionData['cache_on'];
			$connection['cachedir'] = $connectionData['cachedir'];
			$connection['char_set'] = $connectionData['char_set'];
			$connection['dbcollat'] = $connectionData['dbcollat'];
			$connection['swap_pre'] = $connectionData['swap_pre'];
			$connection['autoinit'] = $connectionData['autoinit'];
			$connection['port'] = $connectionData['port'];
			
			return $connection;
		}
		
		function getMSSQLConnection($connectionData)
		{
			// Create array for connection data here
		}
		
		function getBrainHoneyConnection($connectionData)
		{
			// Create Brain Honey connection here
		}
	}
?>