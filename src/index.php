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

/*
** ----------------------
** COOKIES
** ----------------------
*/

if (!isset($_COOKIE["theme"])) {
	$_COOKIE["theme"] = "light";
}

if (!isset($_COOKIE["lang"])) {
	if (substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) == "nl") {
		$_COOKIE["lang"] = "nl";
	} else {
		$_COOKIE["lang"] = "en";
	}
}

if (!isset($_COOKIE["allow_cookies"])) {
	$_COOKIE["allow_cookies"] = "no";
}

if (isset($_GET["theme"])) {
	if ($_GET["theme"] == "light") {
		set_cookie_and_refresh("theme", "light");
	} elseif ($_GET["theme"] == "dark") {
		set_cookie_and_refresh("theme", "dark");
	}
}

if (isset($_GET["lang"])) {
	if ($_GET["lang"] == "nl") {
		set_cookie_and_refresh("lang", "nl");
	} elseif ($_GET["lang"] == "en") {
		set_cookie_and_refresh("lang", "en");
	}
}

if (isset($_GET["allow_cookies"])) {
	if ($_GET["allow_cookies"] == "yes") {
		set_cookie_and_refresh("allow_cookies", "yes");
	}
}

// Load language resource based on browser language setting.
switch ($_COOKIE["lang"]) {
	case "nl":
		include("dutch.inc");
        break;        
		
   default:
      include("english.inc");
       break;
}

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
** --------------------------------------
** SECURITY (Very basic not really secure)
** --------------------------------------
*/

$pid = PAGE_HOME;

$home_password = plaatenergy_db_get_config_item('home_password');

if (strlen($home_password)>0) {
	$pid = PAGE_HOME_LOGIN;
}

/*
** ----------------------
** PARAMETERS
** ----------------------
*/

$eid = EVENT_NONE;
$date = date('Y-m-d');
$limit = 0;

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
		
	case PAGE_SETTING_LIST: 
	case PAGE_SETTING_EDIT: 
	case PAGE_SETTING_LOGIN: 
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

	echo $page;

	// Increase request counter with one!
	$counter = plaatenergy_db_get_config_item('request_counter');  
	plaatenergy_db_set_config_item('request_counter', ++$counter);  
	
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
