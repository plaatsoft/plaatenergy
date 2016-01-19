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
 * Connect to database 
 * @unitTest
 * @output:   1 = ok | 0 = failure
 */
function plaatenergy_db_connect($dbhost, $dbuser, $dbpass, $dbname) {

	global $output;

	$handle = mysql_connect($dbhost, $dbuser, $dbpass);
	mysql_select_db($dbname);
		
	if(!$handle) {
		
		plaatenergy_db_error();
		return 0;		
	}
	return 1;
}

/**
 * Disconnect from database 
 * @unitTest   
 */
function plaatenergy_db_close() {

	$result = mysql_close();
	return $result;
}

/**
 * Show SQL error 
 * @unitTest
 */
function plaatenergy_db_error() {

	$error = mysql_error();
	
	if (DEBUG == 1) {
		echo $error.'<br/>';
	}
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
		
	$result = mysql_query($query);
		
	if (!$result) {
		plaatenergy_db_error();		
	}
	
	return $result;
}

/**
 * Fetch query result 
 * @unitTest
 */
function plaatenergy_db_fetch_object($result) {
	
	$data = @mysql_fetch_object($result);	
	return $data;
}

/**
 * Escape SQL value 
 * @unitTest
 */
function plaatenergy_db_escape($value) {
	
	$value = mysql_real_escape_string($value);	
	return $value;
}

/**
 * Return number of rows
 * @unitTest
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

function plaatenergy_db_process($type) {

  global $year;	
  global $month;
  global $day;

  global $prev_year;
  global $prev_month;
  global $prev_day;

  $energy_meter_reading_low = plaatenergy_db_get_config_item('energy_meter_reading_low');
  $energy_meter_reading_normal = plaatenergy_db_get_config_item('energy_meter_reading_normal');
  $gas_meter_reading = plaatenergy_db_get_config_item('gas_meter_reading');

  $dal = $energy_meter_reading_low;
  $piek = $energy_meter_reading_normal;
  $gas = $gas_meter_reading;
  $dalterug = 0;
  $piekterug = 0;
  $etotal = 0;
  $round = 3;

  if ($type==EVENT_PROCESS_ALL_DAYS) {

    $sql = 'truncate table energy_day';
    plaatenergy_db_query($sql);

  } else {

    $prev_date = plaatenergy_prev_day(date("Y-m-d"));

    $sql2  = 'select dal, piek, dalterug, piekterug, gas from energy ';
    $sql2 .= 'where timestamp>="'.$prev_date.' 00:00:00" and timestamp<="'.$prev_date.' 23:59:59" ';
    $sql2 .= 'order by timestamp desc limit 0,1';

    $result2 = plaatenergy_db_query($sql2);
    $data2 = plaatenergy_db_fetch_object($result2);

    $dal = $data2->dal;
    $piek = $data2->piek;
    $dalterug = $data2->dalterug;
    $piekterug = $data2->piekterug;
    $gas = $data2->gas;

    $sql3  = 'select etotal from solar ';
    $sql3 .= 'where timestamp>="'.$prev_date.' 00:00:00" and timestamp<="'.$prev_date.' 23:59:59" ';
    $sql3 .= 'order by timestamp desc limit 0,1';

    $result3 = plaatenergy_db_query($sql3);
    $data3 = plaatenergy_db_fetch_object($result3);

    $etotal = $data3->etotal;
  }

  if ($type==EVENT_PROCESS_ALL_DAYS) {
	
     $sql1 = 'select cast(timestamp as date) as date from energy group by date';
		 
  } else {
     $timestamp1 = $year.'-'.$month.'-'.$day.' 00:00:00';
     $timestamp2 = $year.'-'.$month.'-'.$day.' 23:59:59';

     $sql1  = 'select cast(timestamp as date) as date from energy ';
     $sql1 .= 'where timestamp>"'.$timestamp1.'" and timestamp<"'.$timestamp2.'" limit 0,1';
  }
  $result1 = plaatenergy_db_query($sql1);

  while ($data1 = plaatenergy_db_fetch_object($result1)) {

    $timestamp1 = $data1->date.' 00:00:00';
    $timestamp2 = $data1->date.' 23:59:59';

    $sql2  = 'select dal, piek, dalterug, piekterug, gas from energy ';
    $sql2 .= 'where timestamp>="'.$timestamp1.'" and timestamp<="'.$timestamp2.'" ';
    $sql2 .= 'order by timestamp desc limit 0,1';

    $result2 = plaatenergy_db_query($sql2);
    $data2 = plaatenergy_db_fetch_object($result2);

    $sql3  = 'select etotal from solar ';
    $sql3 .= 'where timestamp>="'.$timestamp1.'" and timestamp<="'.$timestamp2.'" ';
    $sql3 .= 'order by timestamp desc limit 0,1';

    $result3 = plaatenergy_db_query($sql3);
    $data3 = plaatenergy_db_fetch_object($result3);

    if ($dal>$data2->dal) {
      $dal_diff = round($data2->dal,$round);
    } else {
      $dal_diff = round($data2->dal-$dal,$round);
    }

    if ($piek>$data2->piek) {
      $piek_diff = round($data2->piek,$round);
    } else {
      $piek_diff = round($data2->piek-$piek,$round);
    }

    if ($dalterug>$data2->dalterug) {
      $dalterug_diff = round($data2->dalterug,$round);
    } else {
      $dalterug_diff = round($data2->dalterug-$dalterug,$round);
    }

    if ($piekterug>$data2->piekterug) {
      $piekterug_diff = round($data2->piekterug,$round);
    } else {
      $piekterug_diff = round($data2->piekterug-$piekterug,$round);
    }

    if ($gas>$data2->gas) {
      $gas_diff = round($data2->gas,$round);
    } else {
      $gas_diff = round($data2->gas-$gas,$round);
    }

	$solar_diff=0;
   if (isset($data3->etotal)) {
		if ($etotal>$data3->etotal) {
			$solar_diff = round($data3->etotal,$round);
		} else {
			$solar_diff = round($data3->etotal-$etotal,$round);
		}
	}

    if ($type==EVENT_PROCESS_TODAY) {

     $sql4 = 'select id from energy_day where date="'.$data1->date.'"';

     $result4 = plaatenergy_db_query($sql4);
     $data4 = plaatenergy_db_fetch_object($result4);

     if ( isset($data4->id) ) {

      $sql3  = 'update energy_day set dal='.$dal_diff.', piek='.$piek_diff.', dalterug='.$dalterug_diff.', piekterug='.$piekterug_diff.',';
      $sql3 .= 'solar='.$solar_diff.', gas='.$gas_diff.' where id='.$data4->id;

     } else {

      $sql3  = 'INSERT INTO energy_day (`id`, `date`, `dal`, `piek`, `dalterug`, `piekterug`, `solar`, `gas`) ';
      $sql3 .= 'VALUES (NULL, "'.$data1->date.'", "'.$dal_diff.'", "'.$piek_diff.'", "'.$dalterug_diff.'", "';
      $sql3 .= $piekterug_diff.'", "'.$solar_diff.'","'.$gas_diff.'")';
     }
		
		} else {

			$sql3  = 'INSERT INTO energy_day (`id`, `date`, `dal`, `piek`, `dalterug`, `piekterug`, `solar`, `gas`) ';
			$sql3 .= 'VALUES (NULL, "'.$data1->date.'", "'.$dal_diff.'", "'.$piek_diff.'", "'.$dalterug_diff.'", "';
			$sql3 .= $piekterug_diff.'", "'.$solar_diff.'","'.$gas_diff.'")';
		}

		plaatenergy_db_query($sql3);

		if ($data2->dal>0) {
			$dal = $data2->dal;
		}

		if ($data2->piek>0) {
			$piek = $data2->piek;
		}

		if ($data2->dalterug>0) {
			$dalterug = $data2->dalterug;
		}

		if ($data2->piekterug>0) {
			$piekterug = $data2->piekterug;
		}

		if ($data2->gas>0) {
			$gas = $data2->gas;
		}

		if (isset($data3->etotal) && ($data3->etotal>0)) {
			$etotal = $data3->etotal;
		}
	}
}

/*
** ---------------------
** THE END
** ---------------------
*/

?>
