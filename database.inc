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

/*
** ---------------------
** GENERAL
** ---------------------
*/

/**
 * Connect to database 
 * @unitTest
 * @output:   1 = ok | 0 = failure
 */
function plaatenergy_db_connect($dbhost, $dbuser, $dbpass, $dbname) {

	global $output;

	if (MYSQL == 1) {
	
		$handle = mysql_connect($dbhost, $dbuser, $dbpass);
		mysql_select_db($dbname);
		
	} else {
	
		$handle = pg_connect('host='.$dbhost.' dbname='.$dbname.' user='.$dbuser.' password='.$dbpass);
	}
	
	if(!$handle) {
		
		$message = plaatenergy_db_error();
				
		return 0;		
	}
	return 1;
}

/**
 * Disconnect from database 
 * @unitTest   
 */
function plaatenergy_db_close() {

	if (MYSQL == 1) {

		$result = mysql_close();

	} else {
	
		$result = pg_close();
	}
	
	return $result;
}

/**
 * Show SQL error 
 * @unitTest
 */
function plaatenergy_db_error() {

	if (MYSQL == 1) {
	
		$error = mysql_error();
		
	} else {
	
		$error = pg_last_error();
	}
	
	echo $error;
}

/**
 * Count queries 
 * @output:   queries count
 */
$query_count=0;
function plaatenergy_db_count() {

	global $query_count;

	return $query_count;
}

/**
 * Execute database multi query
 * @unitTest
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
 * @unitTest
 * @output:   Database result
 */
function plaatenergy_db_query($query) {
			
	global $query_count;
	global $output;
	
	$query_count++;

        if (DEBUG == 1) {
          echo $query.'<br/>';
        }
		
	if (MYSQL == 1) {
	
		$result = mysql_query($query);
		
	} else {
	
		$query = str_replace('"', '\'', $query); 
		$result = pg_query($query);
	}
		
	if (!$result) {

		$message = plaatenergy_db_error().' - '.$query;
		
	}
	
	return $result;
}

/**
 * Fetch query result 
 * @unitTest
 */
function plaatenergy_db_fetch_object($result) {
	
	if (MYSQL == 1) {
	
		$data = @mysql_fetch_object($result);	
		
	} else {
	
		$data = @pg_fetch_object($result);
	}
	
	return $data;
}

/**
 * Escape SQL value 
 * @unitTest
 */
function plaatenergy_db_escape($value) {
	
	if (MYSQL == 1) {
	
		$value = mysql_real_escape_string($value);
		
	} else {
	
		$value = pg_escape_string($value);
	}
	
	return $value;
}

/**
 * Return number of rows
 * @unitTest
 */
function plaatenergy_db_num_rows($value) {
	
	if (MYSQL == 1) {
	
		$value = mysql_num_rows($value);
		
	} else {
	
		$value = pg_num_rows($value);
	}
	
	return $value;
}

/*
** ---------------------
** SPECIFIC
** ---------------------
*/

// Here's a startsWith function
function startsWith($haystack, $needle){
    $length = strlen($needle);
    return (substr($haystack, 0, $length) === $needle);
}

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
    $commands = explode(";", $commands);

    //run commands
    $total = $success = 0;
    foreach($commands as $command){
        if(trim($command)){
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
   $sql = "select 1 FROM energy limit 1" ;
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
}

/**
 * Get config item (Default select newest entry found)
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
 * Set config item 
 */
function plaatenergy_db_set_config_item($token, $value) {
   $sql = 'update config set value="'.$value.'" where token="'.$token.'"';
   plaatenergy_db_query($sql);
}

/*
** ---------------------
** THE END
** ---------------------
*/

?>
