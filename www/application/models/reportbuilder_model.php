<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	class Reportbuilder_model extends CI_Model {
		
		function __construct()
		{
			parent::__construct();
			$this->load->database();
		}
		
		public function getConnections()
		{
			$query = "
				SELECT
					id,
					display_name
				FROM
					connection
			";
			
			$result = $this->db->query($query);
			
			return $result->result_array();
		}
		
		public function getCategories()
		{
			$query = "
				SELECT
					id,
					title
				FROM
					report_category
			";
			
			$result = $this->db->query($query);
			
			return $result->result_array();
		}
		
		public function insertConnection()
		{
			$connectionVars = array();
			
			foreach ($_POST AS $key => $val)
			{
				if (preg_match('/connection_/', $key) AND $val['value'] != '')
				{
					$connectionVars[preg_replace('/connection_/','',$key)] = $val['value']; 
				}
			}

			$this->db->insert('connection',$connectionVars);
			return $this->db->insert_id();
		}
		
		public function insertReport($connection)
		{
			$values = array(
				'type' => $_POST['type']['value'],
				'display_name' => $_POST['display_name']['value'],
				'connection_id' => $connection,
				'visibility' => $_POST['visibility']['value'],
				'creator_user_id' => $_POST['creator_user_id']['value'],
				'report_data' => $_POST['report_data']['value'],
				'description' => $_POST['description']['value'],
				'category_id' => $_POST['category_id']['value'],
				'created' => date('Y-m-d H:i:s')
			);
			
			$this->db->insert('report',$values);
			return $this->db->insert_id();
		}
		
		public function insertVariables($reportId)
		{
			$reportVariables = array();
			
			// Extract the report variable names that will be used for this report
			foreach(array_keys($_POST) AS $val)
			{
				if(preg_match("/[a-zA-Z_]*__/", $val))
				{
					$currentVal = preg_replace('/[a-zA-Z_]*__/', '', $val);
					if (!in_array($currentVal, $reportVariables))
					{
						$reportVariables[] = $currentVal;
					}
				}
			}
			
			// Loop through the report variables and insert them into the DB
			foreach ($reportVariables AS $variable)
			{
				// Set values from the form data if they exist, otherwise set an empty string
				$variableType = !empty($_POST['variable_type__' . $variable]['value']) ? $_POST['variable_type__' . $variable]['value'] : '';
				$defaultValue = !empty($_POST['default_value__' . $variable]['value']) ? $_POST['default_value__' . $variable]['value'] : '';
				$textIdentifier = !empty($_POST['text_identifier__' . $variable]['value']) ? $_POST['text_identifier__' . $variable]['value'] : '';
				$displayName = !empty($_POST['display_name__' . $variable]['value']) ? $_POST['display_name__' . $variable]['value'] : '';
				$description = !empty($_POST['description' . $variable]['value']) ? $_POST['description' . $variable]['value'] : '';
				$optionsQuery = !empty($_POST['options_query__' . $variable]['value']) ? $_POST['options_query__' . $variable]['value'] : '';
				
				$values = array(
					'report_id' => $reportId,
					'variable_type' => $variableType,
					'default_value' => $defaultValue,
					'text_identifier' => $textIdentifier,
					'display_name' => $displayName,
					'description' => $description,
					'options_query' => $optionsQuery
				);
				
				$this->db->insert('report_variable',$values);
			}

			return true;
		}
	}
?>