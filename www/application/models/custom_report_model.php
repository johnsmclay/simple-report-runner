<?php
	class Custom_report_model extends CI_Model {
		
		/** CLASS VARS **/
		private $db_table = 'report';
		private $db1 = null; // Database connection 1
		private $db2 = null; // Database connection 2
		/****************/
		
		function __construct()
		{
			parent::__construct();
			$this->load->model('connection_model','connection');
			$this->db1 = $this->load->database('application',TRUE);
			// $this->db2 = $this->load->database('pglms', TRUE);
		}
		
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
		
		/**
		 * getReportList
		 * 
		 * Retrieves a list of all the reports of type MySQL and their categories
		 * 
		 * @return array List of all reports available in the database of type MySQL
		 */
		public function getReportList()
		{
			$reportListQuery = '
				SELECT
					id,
					display_name,
					(SELECT rc.title FROM report_category rc WHERE rc.id = report.category_id) AS category,
					description
				FROM 
					report
				WHERE
					type = "mysql"
				';
			$reportListResult = $this->db1->query($reportListQuery);
			$reportList = array();
			foreach ($reportListResult->result_array() AS $row)
			{
				$reportList[$row['category']][] = array(
					'id' => $row['id'],
					'display_name' => $row['display_name'],
					'description' => $row['description']
				);
			}
			$reportListResult->free_result();
			
			return $reportList;
		}
		
		/**
		 * getReportVars
		 * 
		 * Retrieve the variables for the chosen report. Optionally to save processing
		 * time, when the variables are needed but not the options, you can keep the options
		 * query from being run by passing FALSE as the second parameter.
		 * 
		 * @param int $reportId The report ID being requested
		 * @param bool $runOptions Whether or not the options query (if not empty) should be run by the MySQL conneciton
		 * @return array All report variables for the requested report id
		 */
		public function getReportVars($reportId,$runOptions=true)
		{
			// Load secondary database connection	
			$this->_loadReportDB($reportId);
			
			$reportVarsQuery = "
				SELECT
					*
				FROM
					report_variable
				WHERE
					report_id = {$reportId}
				";
	
			$reportVarsResult = $this->db1->query($reportVarsQuery);
			foreach ($reportVarsResult->result_array() AS $row)
			{
				if (!empty($row['options_query']) && $runOptions == true)
				{
					$optionsResult = $this->db2->query($row['options_query']);
					
					foreach ($optionsResult->result_array() AS $optRow)
					{
						$options[$optRow['id']] = $optRow['description'];
					}
					$optionsResult->free_result();
				}
					else
					{
						$options = NULL;
					}
				$report_vars[] = array(
					'text_identifier' => $row['text_identifier'],
					'variable_type' => $row['variable_type'],
					'default_value' => $row['default_value'],
					'display_name' => $row['display_name'],
					'description' => $row['description'],
					'options' => $options
				);
			}
			$reportVarsResult->free_result();
			
			return $report_vars;
		}
		
		public function getReportDescription($reportId)
		{
			$reportQuery = "
				SELECT
					description
				FROM
					report
				WHERE
					id = {$reportId}
			";
			
			$reportResult = $this->db1->query($reportQuery);
			
			$result = $reportResult->result_array();
			
			return $result[0]['description'];
		}
		
		/**
		 * getReportData
		 * 
		 * @param int $reportId The id of the report being requested
		 * @return string The pre-built query for the requested report
		 */
		public function getReportData($reportId) 
		{
			$getReportDataQuery = "
				SELECT
					report_data,
					connection_id
				FROM
					report
				WHERE
					id = " . $reportId;
	
			$getReportResult = $this->db1->query($getReportDataQuery);
			$reportData = $getReportResult->row_array();
			$getReportResult->free_result();
			
			return $reportData;
		}
		
		/**
		 * runReportQuery
		 * 
		 * Runs the query that has been retrieved from the database and prepped by the controller
		 * so that all variables needed in the query have been replaced by the values retrieved
		 * from the UI form data
		 * 
		 * @param int $reportId The ID number of the requested report
		 * @param 
		 * @return array The data returned from the query after having the header row attached as a new array element
		 */
		public function runReport($reportId,$reportValues=array())
		{
			$this->_loadReportDB($reportId);
			// $tempConnect = $this->load->database($connection,true);
			
			// Get the query to be run
			$reportData = $this->getReportData($reportId);
			
			// Query to be prepped for running
			$reportQuery = $reportData['report_data'];
			
			$reportVars = $this->getReportVars($reportId,false);

			// Loop through the report variables and replace the matching
			// string in the report query with the appropriate $_POST variable value.
			foreach ($reportVars AS $var)
			{
				if ($var['text_identifier'] == 'date_range')
				{
					$reportQuery = preg_replace("/~date_range~/", '"' . date('Y-m-d H:i:s', strtotime($reportValues['start_date'])) . '" AND "' . date('Y-m-d H:i:s', strtotime($_POST['end_date'])) . '"', $reportQuery);
				}
					elseif ($var['variable_type'] == 'string')
					{
						$reportQuery = preg_replace("/~" . $var['text_identifier'] . "~/i", "'" . $reportValues[$var['text_identifier']] . "'", $reportQuery);
					}
						else
						{
							$reportQuery = preg_replace("/~" . $var['text_identifier'] . "~/i", $reportValues[$var['text_identifier']], $reportQuery);
						}
			}
			
			$result = $this->db2->query($reportQuery);
			$resultsArray = $result->result_array();
			$resultCheck = $result->result();
			if (empty($resultCheck))
			{
				return false;
			}
			$result->free_result();
			
			return $resultsArray;
		}
		
		private function _loadReportDB($reportId)
		{
			// Load the needed connection.
			$connection = $this->connection->getConnection($reportId);
			$this->db2 = $this->load->database($connection,true);
		}
	}
?>