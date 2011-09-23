<?php
class Gale_model extends CI_Model{
	function __construct(){
		parent::__construct();
	}
	
	function checkLanguagesTable() {
		$checkLanguageTableQuery = 'SELECT begin_time FROM irs.sessions_language LIMIT 1';
		$checkLanguageResult = $this->db->query($checkLanguageTableQuery);
		if ($checkLanguageResult->num_rows > 0) {
				$checkLanguage = $checkLanguageResult->row();
			return $checkLanguage->begin_time;
		}
			else {
				return false;
			}
	}
	
	function updateSessionsLanguageTable() {
		$truncateQuery = 'TRUNCATE irs.sessions_language';
		$this->db->query($truncateQuery);
		
		$sessionsLanguageQuery = 
			"
				INSERT IGNORE INTO irs.sessions_language (sessions_id,begin_time,title)
				SELECT
					s.id
					,l.created_time
					,l.lang_g
				
				FROM speakez.sessions s
					LEFT JOIN (
						SELECT
							substring_index(l.notes,' ',1) as sid
							,l.id
							,IFNULL(l.user_id, l.provider_id+1000000000) as userid
							,DATE_FORMAT(l.created,'%Y-%m-%d %H:%i:%s') as created_time
							,l.ip_address
							,l.provider_id
							,con.title AS lang_g
						FROM speakez.`logs` l
							LEFT JOIN speakez.courses con
								ON if(l.action=9,SUBSTR(l.notes,LOCATE(' ',l.notes)+1),NULL)=con.id
						WHERE
							l.`action`=9
							AND l.created BETWEEN @beg AND @endd
							AND l.provider_id IS NOT NULL
						ORDER BY userid, created_time
				
					) l ON s.id = l.sid AND l.created_time BETWEEN s.logged_in AND s.last_seen
				
				WHERE
					s.logged_in BETWEEN @beg AND @endd
					AND l.created_time IS NOT NULL
				ORDER BY s.id, l.created_time
			";
			$this->db->query($sessionsLanguageQuery);
		
		return;
	}
	
	function getProviders() {
		$providers = array();
		$providersQuery = "
			SELECT  
				pr.title
			FROM 
				sessions ss
				LEFT JOIN providers pr ON pr.id = ss.provider_id
			WHERE 
				ss.logged_in BETWEEN @beg AND @endd
				AND ss.logged_in < ss.last_seen
			GROUP BY pr.title
			ORDER BY pr.title  ASC;
		";
		
		$result = $this->db->query($providersQuery);
		if($result->num_rows > 0) {
			foreach ($result->result() AS $title) {
				$providers[] = $title->title;
			}
		}
		$result->free_result();
		return $providers;
	}
	
	function getGaleSummary() {
		$summaryReportQuery = "
			SELECT
			    ses.provider,
			    DATE(ses.logged_in) AS Sessions_Date,
			    COUNT(ses.sess_dur) AS Number_of_Sessions,
			    SUM(ses.sess_dur) AS Total_Session_Minutes,
			    SUM(IF(lan.title LIKE 'ESL Spanish',1,0)) AS ESL_Spanish_Accessed, #ESL SPANISH
			    SUM(IF(lan.title LIKE 'ESL Spanish',lan.lang_dur,0)) AS ESL_Spanish_Minutes,
			    SUM(IF(lan.title LIKE 'Spanish',1,0)) AS Spanish_Accessed, #SPANISH
			    SUM(IF(lan.title LIKE 'Spanish',lan.lang_dur,0)) AS Spanish_Minutes,
			    SUM(IF(lan.title LIKE 'French',1,0)) AS French_Accessed, #FRENCH
			    SUM(IF(lan.title LIKE 'French',lan.lang_dur,0)) AS French_Minutes,
			    SUM(IF(lan.title LIKE 'German',1,0)) AS German_Accessed, #GERMAN
			    SUM(IF(lan.title LIKE 'German',lan.lang_dur,0)) AS German_Minutes,
			    SUM(IF(lan.title LIKE 'Inglés (ESL)',1,0)) AS Ingles_ESL_Accessed, #INGLES ESL
			    SUM(IF(lan.title LIKE 'Inglés (ESL)',lan.lang_dur,0)) AS Ingles_ESL_Minutes,
			    SUM(IF(lan.title LIKE 'Italian',1,0)) AS Italian_Accessed, #ITALIAN
			    SUM(IF(lan.title LIKE 'Italian',lan.lang_dur,0)) AS Italian_Minutes,
			    SUM(IF(lan.title LIKE 'Japanese',1,0)) AS Japanese_Accessed, #JAPANESE
			    SUM(IF(lan.title LIKE 'Japanese',lan.lang_dur,0)) AS Japanese_Minutes,
			    SUM(IF(lan.title LIKE 'Korean',1,0)) AS Korean_Accessed, #KOREAN
			    SUM(IF(lan.title LIKE 'Korean',lan.lang_dur,0)) AS Korean_Minutes,
			    SUM(IF(lan.title LIKE 'Mandarin',1,0)) AS Mandarin_Accessed, #MANDARIN
			    SUM(IF(lan.title LIKE 'Mandarin',lan.lang_dur,0)) AS Mandarin_Minutes,
			    SUM(IF(lan.title LIKE 'Russian',1,0)) AS Russian_Accessed, #RUSSIAN
			    SUM(IF(lan.title LIKE 'Russian',lan.lang_dur,0)) AS Russian_Minutes
			FROM (
			    # ses (All Session Data)
			    SELECT
			        ss.id AS sid,
			        ss.logged_in,
			        p.title AS provider,
			        COUNT(DISTINCT ss.id) AS sess_cnt,
			        SUM(DISTINCT if(
			            -- avg time per activity > 29
			            TIMESTAMPDIFF(MINUTE,ss.logged_in,date_add(ss.last_seen,INTERVAL 5 minute))/if(ss.activities=0,1,ss.activities) > 29
			            OR        -- session time > 500
			            TIMESTAMPDIFF(MINUTE,ss.logged_in,date_add(ss.last_seen,INTERVAL 5 minute)) > 500,
			            -- then estimate based ON a reasonable time per activity
			            least((ss.activities + 1)*15,TIMESTAMPDIFF(MINUTE,ss.logged_in,date_add(ss.last_seen,INTERVAL 5 minute))),
			            TIMESTAMPDIFF(MINUTE,ss.logged_in,date_add(ss.last_seen,INTERVAL 5 minute)))
			        ) AS sess_dur
			
			    FROM sessions ss
			        LEFT JOIN users u ON ss.user_id = u.id
			        LEFT JOIN providers p ON ss.provider_id = p.id
			
			    WHERE (u.identifier NOT REGEXP '@k12|@powerspeak|@power-glide|@1' OR u.identifier IS NULL)
			        AND ss.logged_in BETWEEN @beg AND @endd
			        AND ss.logged_in < ss.last_seen
			    GROUP BY p.id ,ss.id
			    # ses END
			) ses
			    LEFT JOIN (
			        # lan (Language Associated Session Data)
			        SELECT
			        ss.id AS lid,
			        sl.begin_time,
			        p.title AS prov,
			        COALESCE(sl.title,c.title) AS title,
			
			        COUNT(DISTINCT sl.id) AS lang_cnt,
			
			        SUM(DISTINCT IF(
			            TIMESTAMPDIFF(MINUTE,sl.begin_time,IFNULL(sln.begin_time,ss.last_seen))/IF(ss.activities=0,1,ss.activities) > 29 -- avg time per activity > 29
			            OR
			            TIMESTAMPDIFF(MINUTE,sl.begin_time,IFNULL(sln.begin_time,ss.last_seen)) > 500, -- session time > 500
			            
			            least((ss.activities + 1)*15,TIMESTAMPDIFF(MINUTE,sl.begin_time,IFNULL(sln.begin_time,ss.last_seen))), -- then estimate based ON a reasonable time per activity
			            TIMESTAMPDIFF(MINUTE,sl.begin_time,IFNULL(sln.begin_time,ss.last_seen))
			            )
			        ) AS lang_dur
			        FROM irs.sessions_language sl
			            LEFT JOIN (
			                SELECT
			                    slt.id AS nxtid ,slt.sessions_id,slt.title,slt.begin_time
			                FROM irs.sessions_language slt
			            ) sln ON sl.sessions_id=sln.sessions_id AND sl.begin_time < sln.begin_time AND sl.title <> sln.title
			            LEFT JOIN sessions ss ON sl.sessions_id=ss.id
			            LEFT JOIN users u ON ss.user_id=u.id
			            LEFT JOIN providers p ON ss.provider_id=p.id
			            LEFT JOIN courses c ON ss.last_course=c.id
			
			        WHERE
			            (u.identifier NOT REGEXP '@k12|@powerspeak|@power-glide|@1' OR u.identifier IS NULL)
			            AND ss.logged_in BETWEEN @beg AND @endd
			        GROUP BY p.id, sl.id
			        # lan END
			    ) lan ON ses.sid = lan.lid
			
			GROUP BY ses.provider, DAYOFMONTH(Sessions_Date)
			ORDER BY ses.provider, Sessions_Date
			";
		
		$resultSummary = $this->db->query($summaryReportQuery);
		if ($resultSummary->num_rows > 0) {
			$return = array();
			foreach ($resultSummary->result_array() AS $row) {
				$return[] = $row;
			}
			return $return;
		}
			else return false;
	}
	
	function getMonthSummary() {
		$monthSummaryQuery = 
		"
			SELECT
			    ses.provider,
			    DATE_FORMAT(ses.logged_in,'%M %Y') AS 'Month',
			    COUNT(ses.sess_dur) AS Number_of_Sessions,
			    SUM(ses.sess_dur) AS Total_Session_Minutes,
			    SUM(IF(lan.title LIKE 'ESL Spanish',1,0)) AS ESL_Spanish_Accessed, #ESL SPANISH
			    SUM(IF(lan.title LIKE 'ESL Spanish',lan.lang_dur,0)) AS ESL_Spanish_Minutes,
			    SUM(IF(lan.title LIKE 'Spanish',1,0)) AS Spanish_Accessed, #SPANISH
			    SUM(IF(lan.title LIKE 'Spanish',lan.lang_dur,0)) AS Spanish_Minutes,
			    SUM(IF(lan.title LIKE 'French',1,0)) AS French_Accessed, #FRENCH
			    SUM(IF(lan.title LIKE 'French',lan.lang_dur,0)) AS French_Minutes,
			    SUM(IF(lan.title LIKE 'German',1,0)) AS German_Accessed, #GERMAN
			    SUM(IF(lan.title LIKE 'German',lan.lang_dur,0)) AS German_Minutes,
			    SUM(IF(lan.title LIKE 'Inglés (ESL)',1,0)) AS Ingles_ESL_Accessed, #INGLES ESL
			    SUM(IF(lan.title LIKE 'Inglés (ESL)',lan.lang_dur,0)) AS Ingles_ESL_Minutes,
			    SUM(IF(lan.title LIKE 'Italian',1,0)) AS Italian_Accessed, #ITALIAN
			    SUM(IF(lan.title LIKE 'Italian',lan.lang_dur,0)) AS Italian_Minutes,
			    SUM(IF(lan.title LIKE 'Japanese',1,0)) AS Japanese_Accessed, #JAPANESE
			    SUM(IF(lan.title LIKE 'Japanese',lan.lang_dur,0)) AS Japanese_Minutes,
			    SUM(IF(lan.title LIKE 'Korean',1,0)) AS Korean_Accessed, #KOREAN
			    SUM(IF(lan.title LIKE 'Korean',lan.lang_dur,0)) AS Korean_Minutes,
			    SUM(IF(lan.title LIKE 'Mandarin',1,0)) AS Mandarin_Accessed, #MANDARIN
			    SUM(IF(lan.title LIKE 'Mandarin',lan.lang_dur,0)) AS Mandarin_Minutes,
			    SUM(IF(lan.title LIKE 'Russian',1,0)) AS Russian_Accessed, #RUSSIAN
			    SUM(IF(lan.title LIKE 'Russian',lan.lang_dur,0)) AS Russian_Minutes
			FROM (
			    # ses (All Session Data)
			    SELECT
			        ss.id AS sid,
			        ss.logged_in,
			        p.title AS provider,
			        COUNT(DISTINCT ss.id) AS sess_cnt,
			        SUM(DISTINCT if(
			            -- avg time per activity > 29
			            TIMESTAMPDIFF(MINUTE,ss.logged_in,date_add(ss.last_seen,INTERVAL 5 minute))/if(ss.activities=0,1,ss.activities) > 29
			            OR        -- session time > 500
			            TIMESTAMPDIFF(MINUTE,ss.logged_in,date_add(ss.last_seen,INTERVAL 5 minute)) > 500,
			            -- then estimate based ON a reasonable time per activity
			            least((ss.activities + 1)*15,TIMESTAMPDIFF(MINUTE,ss.logged_in,date_add(ss.last_seen,INTERVAL 5 minute))),
			            TIMESTAMPDIFF(MINUTE,ss.logged_in,date_add(ss.last_seen,INTERVAL 5 minute)))
			        ) AS sess_dur
			
			    FROM sessions ss
			        LEFT JOIN users u ON ss.user_id = u.id
			        LEFT JOIN providers p ON ss.provider_id = p.id
			
			    WHERE (u.identifier NOT REGEXP '@k12|@powerspeak|@power-glide|@1' OR u.identifier IS NULL)
			        AND ss.logged_in BETWEEN @beg AND @endd
			        AND ss.logged_in < ss.last_seen
			    GROUP BY p.id ,ss.id
			    # ses END
			) ses
			    LEFT JOIN (
			        # lan (Language Associated Session Data)
			        SELECT
			        ss.id AS lid,
			        sl.begin_time,
			        p.title AS prov,
			        COALESCE(sl.title,c.title) AS title,
			
			        COUNT(DISTINCT sl.id) AS lang_cnt,
			
			        SUM(DISTINCT IF(
			            TIMESTAMPDIFF(MINUTE,sl.begin_time,IFNULL(sln.begin_time,ss.last_seen))/IF(ss.activities=0,1,ss.activities) > 29 -- avg time per activity > 29
			            OR
			            TIMESTAMPDIFF(MINUTE,sl.begin_time,IFNULL(sln.begin_time,ss.last_seen)) > 500, -- session time > 500
			            
			            least((ss.activities + 1)*15,TIMESTAMPDIFF(MINUTE,sl.begin_time,IFNULL(sln.begin_time,ss.last_seen))), -- then estimate based ON a reasonable time per activity
			            TIMESTAMPDIFF(MINUTE,sl.begin_time,IFNULL(sln.begin_time,ss.last_seen))
			            )
			        ) AS lang_dur
			        FROM irs.sessions_language sl
			            LEFT JOIN (
			                SELECT
			                    slt.id AS nxtid ,slt.sessions_id,slt.title,slt.begin_time
			                FROM irs.sessions_language slt
			            ) sln ON sl.sessions_id=sln.sessions_id AND sl.begin_time < sln.begin_time AND sl.title <> sln.title
			            LEFT JOIN sessions ss ON sl.sessions_id=ss.id
			            LEFT JOIN users u ON ss.user_id=u.id
			            LEFT JOIN providers p ON ss.provider_id=p.id
			            LEFT JOIN courses c ON ss.last_course=c.id
			
			        WHERE
			            (u.identifier NOT REGEXP '@k12|@powerspeak|@power-glide|@1' OR u.identifier IS NULL)
			            AND ss.logged_in BETWEEN @beg AND @endd
			        GROUP BY p.id, sl.id
			        # lan END
			    ) lan ON ses.sid = lan.lid
			
			GROUP BY ses.provider
		";
		
		$resultsMonthSummary = $this->db->query($monthSummaryQuery);
		if ($resultsMonthSummary->num_rows > 0) {
			$monthSummaryData = array();
			foreach ($resultsMonthSummary->result_array() AS $row) {
				$monthSummaryData[] = $row;
			}
			return $monthSummaryData;
		}
			else return false;
	}
	
}
?>