<?php
class Enrollment_model extends CI_Model {
	function __construct() {
		parent::__construct();
	}
	
	/**
	 * This is the report created for Michelle...
	 * Its purpose is to aid in invoicing for students.
	 */
	
	function retrieveEnrollments(/*$dateFrom,$dateTo,$shoolIds*/) {
		$query = $this->db->query(
					"SELECT 
					  schools.id,
					  CONCAT('K12 OLS (K-8) _ ',schools.description) AS client_location,
					  SUM(CASE 
					      WHEN (sections.segments = 1 AND enrollments.role = 'student' AND enrollments.added BETWEEN '2011-08-00' AND '2011-09-00') THEN 2 
					      WHEN (sections.segments = 2 AND enrollments.role = 'student' AND enrollments.added BETWEEN '2011-08-00' AND '2011-09-00') THEN 1 
					      ELSE 0 END) AS semesters_enrolled,
					  SUM(CASE
					      WHEN (sections.segments = 1 AND enrollments.role = 'student' AND enrollments.dropped <= DATE_ADD(enrollments.added, INTERVAL 30 DAY) AND enrollments.added BETWEEN '2011-08-00' AND '2011-09-00') THEN 2
					      WHEN (sections.segments = 2 AND enrollments.role = 'student' AND enrollments.dropped <= DATE_ADD(enrollments.added, INTERVAL 30 DAY) AND enrollments.added BETWEEN '2011-08-00' AND '2011-09-00') THEN 1
					      ELSE 0 END) AS semesters_dropped,
					  #(CASE sections.segments WHEN 1 THEN 'Full Year' WHEN 2 THEN 'Semester' WHEN 3 THEN 'Trimester' WHEN 4 THEN 'Quarter' WHEN 0 THEN 'Unknown' ELSE 'Other' END) AS something,
					  SUM(IF(
					        enrollments.added BETWEEN '2011-08-00' AND '2011-09-00' 
					        AND enrollments.role = 'student'
					    ,1,0)) AS total_students_enrolled,
					  SUM(IF(
					        enrollments.dropped <= DATE_ADD(enrollments.added, INTERVAL 30 DAY) 
					        AND enrollments.added BETWEEN '2011-08-00' AND '2011-09-00' 
					        AND enrollments.role = 'student'
					    ,1,0)) AS total_students_dropped,
					  SUM(IF(
					        enrollments.added BETWEEN '2011-08-00' AND '2011-09-00' 
					        AND enrollments.user_id IN(29017,210797,166064,166058,166052,164444,164436,109006,108639,82158,172174,99467,209953,172173,90526,43570,99466,205440,205114,172172,107647,146645,29024,177333,80337,179256,160447,209270,130858,70460,29378,176236,213063,214813,177437,178580,29020)
					    ,1,0)) AS teacher_support,
					  SUM(CASE
					    WHEN 
					      enrollments.role = 'teacher' 
					      AND sections.segments = 1 
					      AND enrollments.added BETWEEN '2011-08-00' AND '2011-09-00' 
					      AND enrollments.user_id IN(29017,210797,166064,166058,166052,164444,164436,109006,108639,82158,172174,99467,209953,172173,90526,43570,99466,205440,205114,172172,107647,146645,29024,177333,80337,179256,160447,209270,130858,70460,29378,176236,213063,214813,177437,178580,29020)
					    THEN 2
					    WHEN 
					      enrollments.role = 'teacher' 
					      AND sections.segments = 2 
					      AND enrollments.added BETWEEN '2011-08-00' AND '2011-09-00' 
					      AND enrollments.user_id IN(29017,210797,166064,166058,166052,164444,164436,109006,108639,82158,172174,99467,209953,172173,90526,43570,99466,205440,205114,172172,107647,146645,29024,177333,80337,179256,160447,209270,130858,70460,29378,176236,213063,214813,177437,178580,29020)
					    THEN 1
					    ELSE 0
					    END) AS teacher_semesters
					FROM schools
					  JOIN sections ON sections.school_id = schools.id
					  JOIN sections_users AS enrollments ON enrollments.section_id = sections.id
					WHERE schools.parent_id = 142
					  AND sections.deleted = '0000-00-00 00:00:00'
					  AND (0 or schools.description NOT REGEXP 'demo' OR schools.school_name NOT REGEXP 'demo')
					GROUP BY schools.id
					HAVING (total_students_enrolled > 0)
					ORDER BY schools.description");
					
		foreach ($query->result('array') as $row) {
			$return[] = $row;
		}
		$this->show($return,true);			  
		$query->free_result();
		return $return;
	}
}
?>