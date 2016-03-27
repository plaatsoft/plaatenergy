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
 * @brief contain general page and event handler
 */
 
$time_start = microtime(true);
  
@include "config.inc";
include "general.inc";
include "database.inc";
include "english.inc";

/*
** -------------------- 
** DATABASE
** -------------------- 
*/

if ( @plaatenergy_db_connect($dbhost, $dbuser, $dbpass, $dbname) == false) {

	echo general_header();

	echo '<h1>ERROR</h1>';
	echo '<br/>';
	echo t('DATABASE_CONNECTION_FAILED');
	echo '<br/>';

	$time_end = microtime(true);
	$time = $time_end - $time_start;
	
	echo general_footer($time);

	exit;
}

@plaatenergy_db_check_version($version);

/*
** ----------------------
** PARAMETERS
** ----------------------
*/

$ip = $_SERVER['REMOTE_ADDR'];

$eid = EVENT_NONE;
$sid = EVENT_NONE;
$pid = PAGE_HOME;

$date = date('Y-m-d');
$limit = 0;
$cat=0;

$session = plaatenergy_post('session', '');
$token = plaatenergy_post("token", "");

if (strlen($token)>0) {
	
  /* Decode token to php parameters */
  $token =  plaatenergy_token_decode($token);	  
  $tokens = @preg_split("/&/", $token);
	
  foreach ($tokens as $item) {
     $items = preg_split ("/=/", $item);				
     $$items[0] = $items[1];	
     //echo '>'.$items[0].'='.$items[1].'<br/>';
  }
}

/*
** --------------------------------------
** SECURITY (Very basic not really secure)
** --------------------------------------
*/

$home_password = plaatenergy_db_get_config_item('home_password');

// Create for each visitor an account (without session_id)
$session_id = plaatenergy_db_get_session($ip);

if (strlen($home_password)>0) {
	if ((strlen($session_id)==0) || ($session!=$session_id)) {
		// User not login, Redirect to login page
		$pid = PAGE_HOME_LOGIN;
	}
}

/*
** -------------------
** ACTIONS
** -------------------
*/

function plaatenergy_scheme_action() {

	global $ip;
		
	$sql  = 'select theme from session where ip="'.$ip.'"';
	$result = plaatenergy_db_query($sql);
	$row = plaatenergy_db_fetch_object($result);

	if ($row->theme=="light") {
		$theme = "dark";
	} else {
		$theme = "light" ;
	}
	
	$sql = 'update session set theme="'.$theme.'" where ip="'.$ip.'"';
	plaatenergy_db_query($sql);
}

function plaatenergy_language_action() {
	
	global $ip;
	
	$sql  = 'select language from session where ip="'.$ip.'"';
	$result = plaatenergy_db_query($sql);
	$row = plaatenergy_db_fetch_object($result);

	if ($row->language=="en") {
		$language = "nl";
	} else {
		$language = "en";
	}
	
	$sql = 'update session set language="'.$language.'" where ip="'.$ip.'"';
	plaatenergy_db_query($sql);
}

/*
** ---------------------
** SPECIAL EVENT MACHINE
** ---------------------
*/

switch ($sid) {

	case EVENT_SCHEME: 
			plaatenergy_scheme_action();
			break;
			
	case EVENT_LANGUAGE:
			plaatenergy_language_action();
			break;
}


/*
** -------------------
** LANGUAGE
** -------------------
*/

$sql  = 'select language from session where ip="'.$ip.'"';
$result = plaatenergy_db_query($sql);
$row = plaatenergy_db_fetch_object($result);

if ($row->language=="nl") {

	include("dutch.inc");
}	

/*
** -------------------
** STATE MACHINE
** -------------------
*/

$page = "";

switch ($pid) {

	// ---------------------------------
		
	case PAGE_HOME: 
	case PAGE_HOME_LOGIN: 
		include "home.php";
		$page = plaatenergy_home();
		break;

	case PAGE_ABOUT: 
		include "about.php";
		$page = plaatenergy_about();
		break;

	case PAGE_DONATE: 
		include "donate.php";
		$page = plaatenergy_donate();
		break;

	case PAGE_RELEASE_NOTES: 
		include "release_notes.php";
		$page = plaatenergy_release_notes();
		break;
	 
	case PAGE_REPORT: 
		include "report.php";
		$page = plaatenergy_report();
		break;
		
	case PAGE_REALTIME:
		include "realtime.php";
		$page = plaatenergy_realtime();
		break;
		
	case PAGE_SETTING_LIST: 
	case PAGE_SETTING_EDIT: 
	case PAGE_SETTING_LOGIN: 
	case PAGE_SETTING_CATEGORY:
		include "settings.php";
		$page = plaatenergy_settings();
		break;
		
	// ---------------------------------
	
	case PAGE_DAY_IN_ENERGY: 
		include "day_in_kwh_edit.php";
		include "day_in_kwh.php";
		$page = plaatenergy_day_in_energy();
		break;
		
	case PAGE_DAY_IN_KWH_EDIT: 
		include "day_in_kwh_edit.php";
		$page = plaatenergy_day_in_edit();
		break;
		
	case PAGE_DAY_OUT_ENERGY: 
		include "day_out_kwh_edit.php";
		include "day_out_kwh.php";
		$page = plaatenergy_day_out_energy();
		break;
		
	case PAGE_DAY_OUT_KWH_EDIT: 
		include "day_out_kwh_edit.php";
		$page = plaatenergy_day_out_edit();
		break;
		
	case PAGE_DAY_IN_GAS: 
		include "day_in_gas_edit.php";	
		include "day_in_gas.php";
		$page = plaatenergy_day_in_gas();
		break;
				
	case PAGE_DAY_IN_GAS_EDIT: 
		include "day_in_gas_edit.php";
		$page = plaatenergy_day_in_gas_edit();
		break;

	case PAGE_DAY_PRESSURE: 
		include "day_pressure.php";
		$page = plaatenergy_day_pressure();
		break;
		
	case PAGE_DAY_TEMPERATURE: 
		include "day_temperature.php";
		$page = plaatenergy_day_temperature();
		break;
		
	case PAGE_DAY_HUMIDITY: 
		include "day_humidity.php";
		$page = plaatenergy_day_humidity();
		break;
	
	// ---------------------------------
		
	case PAGE_MONTH_IN_ENERGY:
		include "month_in_kwh.php";
		$page = plaatenergy_month_in_energy();
		break;
		
	case PAGE_MONTH_OUT_ENERGY:
		include "month_out_kwh.php";
		$page = plaatenergy_month_out_energy();
		break;
		
	case PAGE_MONTH_IN_GAS:
		include "month_in_gas.php";
		$page = plaatenergy_month_in_gas();
		break;
			
	// ---------------------------------
	
	case PAGE_YEAR_IN_ENERGY:
		include "year_in_kwh.php";
		$page = plaatenergy_year_in_energy();
		break;
		
	case PAGE_YEAR_OUT_ENERGY:
		include "year_out_kwh.php";
		$page = plaatenergy_year_out_energy();
		break;
		
	case PAGE_YEAR_IN_GAS:
		include "year_in_gas.php";
		$page = plaatenergy_year_in_gas();
		break;
		
	// ---------------------------------

	case PAGE_YEARS_IN_ENERGY:
		include "years_in_kwh.php";
		$page = plaatenergy_years_in_energy();
		break;
		
	case PAGE_YEARS_OUT_ENERGY:
		include "years_out_kwh.php";
		$page = plaatenergy_years_out_energy();
		break;

	case PAGE_YEARS_IN_GAS:
		include "years_in_gas.php";
		$page = plaatenergy_years_in_gas();
		break;
		
	// ---------------------------------
}


if ($eid != EVENT_EXPORT) {
	
	// Normal page
	echo general_header();

	echo "<!-- content-start -->";

	echo $page;
	
	echo "<!-- content-end -->";
	
	// Calculate to page render time 
	$time_end = microtime(true);
	$time = $time_end - $time_start;
	
	echo general_footer($time);
}

plaatenergy_db_close();

/*
** -------------------
** THE END
** -------------------
*/

?>
