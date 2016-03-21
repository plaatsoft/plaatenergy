<?php

/* 
**  ===========
**  PlaatEnergy
**  ===========
**
**  Created by wplaat
**
**  For more information visit the following website.
**  Website : www.plaatsoft.nl 
**
**  Or send an email to the following address.
**  Email   : info@plaatsoft.nl
**
**  All copyrights reserved (c) 2008-2016 PlaatSoft
*/

/**
 * @file
 * @brief contain database logic
 */
 
/*
** ---------------------
** SETTINGS
** ---------------------
*/

define('DEBUG', 0);

/*
** ---------------------
** GENERAL
** ---------------------
*/

/**
 * connect to database
 * @param $dbhost database hostname
 * @param $dbuser database username
 * @param $dbpass database password
 * @param $dbname database name
 * @return connect result (true = successfull connected | false = connection failed)
 */
function plaatenergy_db_connect($dbhost, $dbuser, $dbpass, $dbname) {

	global $output;

	$handle = mysql_connect($dbhost, $dbuser, $dbpass);
	if(!$handle) {

		plaatenergy_db_error();
		return false;		
	}
	mysql_select_db($dbname);
	return true;
}

/**
 * Disconnect from database  
 * @return disconnect result
 */
function plaatenergy_db_close() {

	$result = mysql_close();
	return $result;
}

/**
 * Show SQL error 
 * @return HTML formatted SQL error
 */
function plaatenergy_db_error() {

	$error = mysql_error();
	
	if (DEBUG == 1) {
		echo $error.'<br/>';
	}
}

/**
 * Count queries 
 * @return queries count
 */
$query_count=0;
function plaatenergy_db_count() {

	global $query_count;
	return $query_count;
}

/**
 * Execute database multi query
 */
function plaatenergy_db_multi_query($queries) {

	$tokens = @preg_split("/;/", $queries);
	foreach ($tokens as $token) {
	
		$token=trim($token);
		if (strlen($token)>3) {
			plaatenergy_db_query($token);		
		}
	}
}

/**
 * Execute database query
 * @param $query SQL query with will be executed.
 * @return Database result
 */
function plaatenergy_db_query($query) {
			
	global $query_count;
	global $output;
	
	$query_count++;

	if (DEBUG == 1) {
		echo $query.'<br/>';
	}
		
	@$result = mysql_query($query);
		
	if (!$result) {
		plaatenergy_db_error();		
	}
	
	return $result;
}

/**
 * Fetch query result 
 * @return mysql data set if any
 */
function plaatenergy_db_fetch_object($result) {
	
	$data = @mysql_fetch_object($result);	
	return $data;
}

/**
 * Escape SQL value 
 * @return escaped mysql query
 */
function plaatenergy_db_escape($value) {
	
	$value = mysql_real_escape_string($value);	
	return $value;
}

/**
 * Return number of rows
 * @return number of row in dataset
 */
function plaatenergy_db_num_rows($value) {
	
	$value = mysql_num_rows($value);
	return $value;
}

/*
** ---------------------
** SPECIFIC
** ---------------------
*/

function startsWith($haystack, $needle){
    $length = strlen($needle);
    return (substr($haystack, 0, $length) === $needle);
}

/**
 * Execute SQL script
 * @param $version Version of sql patch file
 */
function plaatenergy_db_execute_sql_file($version) {

    $filename = 'database/patch-'.$version.'.sql';

    $commands = file_get_contents($filename);

    //delete comments
    $lines = explode("\n",$commands);
    $commands = '';
    foreach($lines as $line){
        $line = trim($line);
        if( $line && !startsWith($line,'--') ){
            $commands .= $line . "\n";
        }
    }

    //convert to array
    $commands = explode(";\n", $commands);

    //run commands
    $total = $success = 0;
    foreach($commands as $command){
        if(trim($command)){
	    if (DEBUG == 1) {
		echo $command.'<br/>';
	    }
            $success += (@plaatenergy_db_query($command)==false ? 0 : 1);
            $total += 1;
        }
    }

    //return number of successful queries and total number of queries found
    return array(
        "success" => $success,
        "total" => $total
    );
}

/**
 * Check db version and upgrade if needed!
 */
function plaatenergy_db_check_version() {

   // Execute SQL base sql script if needed!
   $sql = "select 1 FROM config limit 1" ;
   $result = plaatenergy_db_query($sql);
   if (!$result)  {
      plaatenergy_db_execute_sql_file("0.1");
   }

   // Execute SQL path script v0.5 if needed
   $sql = 'select value from config where token="database_version"';
   $result = plaatenergy_db_query($sql);
   $row = plaatenergy_db_fetch_object($result);
   if (!isset($row->value))  {
      plaatenergy_db_execute_sql_file("0.5");
   }
	
   // Execute SQL path script v0.6 if needed
   $sql = 'select value from config where token="database_version"';
   $result = plaatenergy_db_query($sql);
   $row = plaatenergy_db_fetch_object($result);
   if ($row->value=="0.5")  {
      plaatenergy_db_execute_sql_file("0.6");
   }
	
   // Execute SQL path script v0.7 if needed
   $sql = 'select value from config where token="database_version"';
   $result = plaatenergy_db_query($sql);
   $row = plaatenergy_db_fetch_object($result);
   if ($row->value=="0.6")  {
      plaatenergy_db_execute_sql_file("0.7");
   }

   // Execute SQL path script v0.8 if needed
   $sql = 'select value from config where token="database_version"';
   $result = plaatenergy_db_query($sql);
   $row = plaatenergy_db_fetch_object($result);
   if ($row->value=="0.7")  {
      plaatenergy_db_execute_sql_file("0.8");
   }
	
	// Execute SQL path script v0.9 if needed
   $sql = 'select value from config where token="database_version"';
   $result = plaatenergy_db_query($sql);
   $row = plaatenergy_db_fetch_object($result);
   if ($row->value=="0.8")  {
      plaatenergy_db_execute_sql_file("0.9");
   }
	
	// Execute SQL path script v1.0 if needed
   $sql = 'select value from config where token="database_version"';
   $result = plaatenergy_db_query($sql);
   $row = plaatenergy_db_fetch_object($result);
   if ($row->value=="0.9")  {
      plaatenergy_db_execute_sql_file("1.0");
   }
}

/**
 * Fetch config item from database
 * @param $key key name of setting stored in database
 * @return $value of key
 */
function plaatenergy_db_get_config_item($key) {

   $sql = 'select value from config where token="'.$key.'" order by date desc limit 0,1';
   $result = plaatenergy_db_query($sql);
   $data = plaatenergy_db_fetch_object($result);

   $value = "";
   if ( isset($data->value) ) {
	$value = $data->value;
   }
   return $value;
}

/**
 * Store config item if database
 * @param $token Key of setting item
 * @param $value Value of key
 */
function plaatenergy_db_set_config_item($token, $value) {
   $sql = 'update config set value="'.$value.'", date=SYSDATE() where token="'.$token.'"';
   plaatenergy_db_query($sql);
}

/**
 * Process raw data in database
 * @param $type (EVENT_PROCESS_TODAY process only today data, EVENT_PROCESS_ALL_DAYS process all data)
 */
function plaatenergy_db_process($type) {

	global $year;	
	global $month;
	global $day;

	$meter_reading_used_low = plaatenergy_db_get_config_item('meter_reading_used_low');
	$meter_reading_used_normal = plaatenergy_db_get_config_item('meter_reading_used_normal');
	$meter_reading_delivered_low = plaatenergy_db_get_config_item('meter_reading_delivered_low');
	$meter_reading_delivered_normal = plaatenergy_db_get_config_item('meter_reading_delivered_normal');
	$meter_reading_used_gas = plaatenergy_db_get_config_item('meter_reading_used_gas');

	$low_used = $meter_reading_used_low;
	$normal_used = $meter_reading_used_normal;
	$gas_used = $meter_reading_used_gas;
	$low_delivered = $meter_reading_delivered_low;
	$normal_delivered = $meter_reading_delivered_normal;
	$etotal = 0;
  
	$round = 3;

	if ($type==EVENT_PROCESS_ALL_DAYS) {

		$sql = 'truncate table energy_summary';
		plaatenergy_db_query($sql);

	} else {

		$prev_date = plaatenergy_prev_day(date("Y-m-d"));

		$sql2  = 'select low_used, normal_used, low_delivered, normal_delivered, gas_used from energy1 ';
		$sql2 .= 'where timestamp>="'.$prev_date.' 00:00:00" and timestamp<="'.$prev_date.' 23:59:59" ';
		$sql2 .= 'order by timestamp desc limit 0,1';

		$result2 = plaatenergy_db_query($sql2);
		$data2 = plaatenergy_db_fetch_object($result2);
		if (isset($data2->low_used)) {
			$low_used = $data2->low_used;
			$normal_used = $data2->normal_used;
			$low_delivered = $data2->low_delivered;
			$normal_delivered = $data2->normal_delivered;
			$gas_used = $data2->gas_used;
		}

		$sql3  = 'select etotal from solar ';
		$sql3 .= 'where timestamp>="'.$prev_date.' 00:00:00" and timestamp<="'.$prev_date.' 23:59:59" ';
		$sql3 .= 'order by timestamp desc limit 0,1';
    
		$result3 = plaatenergy_db_query($sql3);
		$data3 = plaatenergy_db_fetch_object($result3);
		if (isset($data3->etotal)) {
			$etotal = $data3->etotal;
		}
	}

	if ($type==EVENT_PROCESS_ALL_DAYS) {
	
		$sql1 = 'select cast(timestamp as date) as date from energy1 group by date';
		 
	} else {
		$sql1  = 'select cast(timestamp as date) as date from energy1 ';
		$sql1 .= 'where timestamp>"'.date("Y-m-d").' 00:00:00" and timestamp<"'.date("Y-m-d").' 23:59:59" limit 0,1';
	}
	$result1 = plaatenergy_db_query($sql1);

	while ($data1 = plaatenergy_db_fetch_object($result1)) {

		$timestamp1 = $data1->date.' 00:00:00';
		$timestamp2 = $data1->date.' 23:59:59';

		$sql2  = 'select low_used, normal_used, low_delivered, normal_delivered, gas_used  from energy1 ';
		$sql2 .= 'where timestamp>="'.$timestamp1.'" and timestamp<="'.$timestamp2.'" ';
		$sql2 .= 'order by timestamp desc limit 0,1';

		$result2 = plaatenergy_db_query($sql2);
		$data2 = plaatenergy_db_fetch_object($result2);
	
		$sql3  = 'select etotal from solar ';
		$sql3 .= 'where timestamp>="'.$timestamp1.'" and timestamp<="'.$timestamp2.'" ';
		$sql3 .= 'order by timestamp desc limit 0,1';
	
		$result3 = plaatenergy_db_query($sql3);
		$data3 = plaatenergy_db_fetch_object($result3);

		if ($low_used>$data2->low_used) {
			$low_used_diff = round($data2->low_used,$round);
		} else {
			$low_used_diff = round($data2->low_used-$low_used,$round);
		}
	
		if ($normal_used>$data2->normal_used) {
			$normal_used_diff = round($data2->normal_used,$round);
		} else {
			$normal_used_diff = round($data2->normal_used-$normal_used,$round);
		}
	
		if ($low_delivered>$data2->low_delivered) {
			$low_delivered_diff = round($data2->low_delivered,$round);
		} else {
			$low_delivered_diff = round($data2->low_delivered-$low_delivered,$round);
		}
	
		if ($normal_delivered>$data2->normal_delivered) {
			$normal_delivered_diff = round($data2->normal_delivered,$round);
		} else {
			$normal_delivered_diff = round($data2->normal_delivered-$normal_delivered,$round);
		}
	
		if ($gas_used>$data2->gas_used) {
			$gas_used_diff = round($data2->gas_used,$round);
		} else {
			$gas_used_diff = round($data2->gas_used-$gas_used,$round);
		}
	
		$solar_diff=0;
		if (isset($data3->etotal)) {
			if ($etotal>$data3->etotal) {
				$solar_diff = round($data3->etotal,$round);
			} else {
				$solar_diff = round($data3->etotal-$etotal,$round);
			}
		}

		$sql4 = 'select id from energy_summary where date="'.$data1->date.'"';
		$result4 = plaatenergy_db_query($sql4);
		$data4 = plaatenergy_db_fetch_object($result4);
	
		if ( isset($data4->id) ) {

			$sql3  = 'update energy_summary set low_used='.$low_used_diff.', normal_used='.$normal_used_diff.', ';
			$sql3 .= 'low_delivered='.$low_delivered_diff.', normal_delivered='.$normal_delivered_diff.', ';
			$sql3 .= 'solar_delivered='.$solar_diff.', gas_used='.$gas_used_diff.' where id='.$data4->id;
	
		} else {
	
			$sql3  = 'INSERT INTO energy_summary (`id`, `date`, `low_used`, `normal_used`, `low_delivered`, `normal_delivered`, `solar_delivered`, `gas_used`) ';
			$sql3 .= 'VALUES (NULL, "'.$data1->date.'", "'.$low_used_diff.'", "'.$normal_used_diff.'", "'.$low_delivered_diff.'", "';
			$sql3 .= $normal_delivered_diff.'", "'.$solar_diff.'","'.$gas_used_diff.'")';
		}

		plaatenergy_db_query($sql3);

		if ($data2->low_used>0) {
			$low_used = $data2->low_used;
		}

		if ($data2->normal_used>0) {
			$normal_used = $data2->normal_used;
		}

		if ($data2->low_delivered>0) {
			$low_delivered = $data2->low_delivered;
		}

		if ($data2->normal_delivered>0) {
			$normal_delivered = $data2->normal_delivered;
		}

		if ($data2->gas_used>0) {
			$gas_used = $data2->gas_used;
		}

		if (isset($data3->etotal) && ($data3->etotal>0)) {
			$etotal = $data3->etotal;
		}
	}
}


function plaatenergy_db_get_session($ip, $new=false) {

   $sql = 'select sid, session_id, requests from session where ip="'.$ip.'"';
   $result = plaatenergy_db_query($sql);
   $data = plaatenergy_db_fetch_object($result);

   $session_id = "";
   if ( isset($data->sid) ) {   
	
		$session_id = $data->session_id;
		$requests = $data->requests;
	
		if ($new==true) {		
			$session_id = md5(date('Y-m-d H:i:s'));
		}
			   
		$sql = 'update session set timestamp=SYSDATE(), session_id="'.$session_id.'", requests='.++$requests.' where sid="'.$data->sid.'"';
	   plaatenergy_db_query($sql);
	  
   } else {

		$sql = 'insert into session (timestamp, ip, requests, language, theme) value (SYSDATE(), "'.$ip.'", 1, "en", "light")';
		plaatenergy_db_query($sql);
	}

   return $session_id;
}

/*
** ---------------------
** THE END
** ---------------------
*/

?>
