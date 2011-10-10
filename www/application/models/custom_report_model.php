<?php
class Custom_report_model extends CI_Model {
	
	var $db1 = null; // Database connection 1
	var $db2 = null; // Database connection 2
		
	function __construct()
	{
		parent::__construct();
		$this->db1 = $this->load->database('application',TRUE);
		$this->db2 = $this->load->database('pglms', TRUE);
	}
	
	/**
	 * getReportList
	 * 
	 * Retrieves a list of all the reports of type MySQL and their categories
	 * 
	 * @return array List of all reports available in the database of type MySQL
	 */
	function getReportList()
	{
		$reportListQuery = '
			SELECT
				report.id as id,
				report.display_name,
				(SELECT rc.title FROM report_category rc WHERE rc.id = report.category_id) AS category
			FROM 
				report
			WHERE
				type = "mysql"
			';
		$reportListResult = $this->db1->query($reportListQuery);
		foreach ($reportListResult->result_array() AS $row)
		{
			$reportList[$row['category']][] = array(
				'id' => $row['id'],
				'display_name' => $row['display_name']
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
	function getReportVars($reportId,$runOptions=true)
	{
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
	
	/**
	 * getReportData
	 * 
	 * @param int $reportId The id of the report being requested
	 * @return string The pre-built query for the requested report
	 */
	function getReportData($reportId) 
	{
		$getReportDataQuery = "
			SELECT
				report_data
			FROM
				report
			WHERE
				id = " . $reportId;

		$getReportResult = $this->db1->query($getReportDataQuery);
		foreach ($getReportResult->result_array() AS $row)
		{
			$reportQuery = $row['report_data'];
		}
		$getReportResult->free_result();
		
		return $reportQuery;
	}
	
	/**
	 * runReportQuery
	 * 
	 * Runs the query that has been retrieved from the database and prepped by the controller
	 * so that all variables needed in the query have been replaced by the values retrieved
	 * from the UI form data
	 * 
	 * @param string $query The query to be run
	 * @return array The data returned from the query after having the header row attached as a new array element
	 */
	function runReportQuery($query)
	{
		$result = $this->db2->query($query);
		$resultsArray = $result->result_array();
		$result->free_result();
		
		// Get the database fields from the array keys whcih are used for the
		// CSV files column headers row.
		$headerRow = array_keys($resultsArray[0]);
		
		array_unshift($resultsArray, $headerRow);
		
		return $resultsArray;
	}
	
}
?>