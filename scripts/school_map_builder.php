<?php

if(strstr($_SERVER['PHP_SELF'], 'gale')){
		$host = '10.64.3.83';
		$database = 'speakez';
	}
		else {
			$host = '10.64.9.71';
			$database = 'pglms2010';
		}
	
	// #--------------------------------------------------------------------------------------------------
	// #database connect
	 $db = mysql_connect($host, 'reporter', 'pr8tREdUcez2wAyA8PA5');
	 mysql_select_db($database, $db);
	// #--------------------------------------------------------------------------------------------------

// Create the temp table
$query = file_get_contents('./school_ancestry_temp_recreation.sql');
mysql_query($query);

getChildren(1);

// Create the temp table
$query = file_get_contents('./in-place_rename.sql');
mysql_query($query);
  
function getChildren($parent,$ancestors=array()) {
	// show(__METHOD__ .' called with ' . $parent . ' and ' . json_encode($ancestors));
	$query = "SELECT id FROM pglms2010.schools WHERE parent_id = $parent";
	$result = mysql_query($query);
	$ancestors[] = $parent;
	
	if (mysql_num_rows($result) > 0) {
		
		while($row = mysql_fetch_assoc($result)) {
			$children[] = $row['id'];
		}
		
		// show($parent . ' has ' . mysql_num_rows($result) . ' children' );
		foreach ($children AS $key => $child) {
			// show('here is the current child ' . $child);
			foreach ($ancestors AS $ancestor) {
				$query = "INSERT INTO warehouse.school_ancestry_new (parent_id,child_id,level) VALUES($ancestor,$child," . count($ancestors) . ")";
				mysql_query($query);
			}
			getChildren($child,$ancestors);
		}
		
	}
		else {
			// show($parent . ' has no children');
		}
 }
  
 # Debugging function
	function show($var,$exit=null,$vardump=null) {
		echo "<pre>";
			if ($vardump) var_dump($var);
			else print_r($var);
		echo "</pre>";
		if ($exit) exit();
	}
?>
