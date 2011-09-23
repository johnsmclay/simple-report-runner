<?php
class Common_model extends CI_Model {
	function __construct(){
		parent::__construct();
	}
	
	/**
	 * getSchoolList
	 * 
	 * Obtains a list of schools
	 * 
	 * @return array of schools and their id's
	 */
	public function getSchoolList() {
		$excludedDemoSchools = $this->excludeDemos();

		$query = $this->db->query(
			"SELECT concat(LEFT(description,35),' - ',school_name) as client, id 
			  FROM schools 
			  WHERE parent_id=1
			  AND display_sequence > 0 
			  AND id NOT IN($excludedDemoSchools)
			  ORDER BY client"
		);
		
		$result = $query->result_array();
		return $result;
	}
	
	/**
	 * getSubSchoolList
	 * 
	 * Obtains a list of subschools under a given parent ID
	 * 
	 * This is a "Ken" query and seems to work, but can be replaced with
	 * something better if it is found to be obsolete or bulky
	 * 
	 * @parent int ID number of parent school to get subschools for
	 * @return array of schools and their id's
	 */
	public function getSubSchoolList($parent) {
		$excldudedDemoSchools = $this->excludeDemos();
		
		$query = $this->db->query(
			"SELECT 
				sc5.id AS id,
			    CONCAT_WS('',IF(sc1.school_name<>'' AND sc2.school_name<>'','_',NULL),IF(sc2.school_name<>'' AND sc3.school_name<>'','_',NULL),IF(sc3.school_name<>'' AND sc4.school_name<>'','_',NULL),IF(sc4.school_name<>'' AND sc5.school_name<>'','_',NULL),CONCAT(LEFT(sc5.description,33),' - ',sc5.school_name)) AS school, 
			    CONCAT_WS(' * ',sc1.description,sc2.description,sc3.description,sc4.description,sc5.description) AS myorder 
			FROM schools AS sc5
			  LEFT JOIN schools AS sc4 ON sc4.parent_id > 1 AND sc5.parent_id = sc4.id
			  LEFT JOIN schools AS sc3 ON sc3.parent_id > 1 AND sc4.parent_id = sc3.id
			  LEFT JOIN schools AS sc2 ON sc2.parent_id > 1 AND sc3.parent_id = sc2.id
			  LEFT JOIN schools AS sc1 ON sc1.parent_id > 1 AND sc2.parent_id = sc1.id
			WHERE 1 AND sc5.parent_id > 1 AND sc5.display_sequence > 0 
			  AND sc5.id NOT IN($excldudedDemoSchools)
			  AND (
			    sc1.parent_id=$parent OR 
			    sc2.parent_id=$parent OR 
			    sc3.parent_id=$parent OR 
			    sc4.parent_id=$parent OR 
			    sc5.parent_id=$parent) 
			ORDER BY myorder"
		);

		if ($query->num_rows() > 0) {
			$result = $query->result_array();
			$query->free_result();
			return $result;
		}
			else {
				return null;
			}
	}
	
	/**
	 * excludeDemos
	 * 
	 * This is a "Ken Query" but it seems to work well enough
	 *
	 * Excludes schools that are demo's, release's, promo's, test's, etc...
	 * @return array of id's to exclude from school queries
	 * 
	 */
	private function excludeDemos() {
		$query = $this->db->query(
			"SELECT sc.id, sc.parent_id, sc.school_name, sc.description, sc.display_sequence 
		    FROM schools sc
		    WHERE 1 
		    AND (0 or sc.school_name REGEXP 'jensch' or (sc.school_name REGEXP 'demo' or sc.description REGEXP 'release|demo|quarantine|test|promo') 
				or sc.school_name REGEXP 'demo[_ -]{1,}|[_ -]{1,}demo|^demo$|test[_ -]{1,}|[_ -]{1,}test|^test$|qa[_ -]{1,}|[_ -]{1,}qa|^qa$|admin[_ -]{1,}|[_ -]{1,}admin|^admin$' 
				or sc.description REGEXP 'demo[_ -]{1,}|[_ -]{1,}demo|^demo$|test[_ -]{1,}|[_ -]{1,}test|^test$|qa[_ -]{1,}|[_ -]{1,}qa|^qa$|admin[_ -]{1,}|[_ -]{1,}admin|^admin$' 
		    )
		    ORDER BY sc.description"
		);
		
		foreach($query->result_array() AS $row) {
			$tempArray[] = $row['id'];
		}
		$query->free_result();
		
		$excludeList = implode(',',$tempArray);
		
		return $excludeList;
	}
}
?>