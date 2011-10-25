<?php
	class Pglmsweeklies_model extends CI_Model {
		private $db2;
			
		function __construct()
		{
			parent::__construct();
			$this->db2 = $this->load->database('pglms',true);
		}
		
		public function getSchoolsListOptions()
		{
			$query = "
				SELECT 
					CONCAT(LEFT(sc.description,40),' -- ',sc.school_name) AS description,
					id 
				FROM 
					pglms2010.schools sc
				WHERE 
				sc.school_name NOT REGEXP 'jensch' AND sc.school_name NOT REGEXP 'demo|promo' AND sc.description NOT REGEXP 'release|demo|quarantine|test|promo'
					AND sc.school_name NOT REGEXP 'demo[_ -]{1,}|[_ -]{1,}demo|^demo$|test[_ -]{1,}|[_ -]{1,}test|^test$|qa[_ -]{1,}|[_ -]{1,}qa|^qa$|admin[_ -]{1,}|[_ -]{1,}admin|^admin$' 
					AND sc.description NOT REGEXP 'demo[_ -]{1,}|[_ -]{1,}demo|^demo$|test[_ -]{1,}|[_ -]{1,}test|^test$|qa[_ -]{1,}|[_ -]{1,}qa|^qa$|admin[_ -]{1,}|[_ -]{1,}admin|^admin$' 
				ORDER BY description
			";
			
			$result = $this->db2->query($query);
			return $result->result_array();
		}
	}
?>