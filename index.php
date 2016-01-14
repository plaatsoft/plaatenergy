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

include "config.inc";
include "general.inc";
include "constants.php";
include "database.inc";

/*
** ----------------------
** Parameters
** ----------------------
*/

$pid = PAGE_HOME;

$token = plaatenergy_post("token", "");

if (strlen($token)>0) {
	
  /* Decode token to php parameters */
  $token = gzinflate(base64_decode($token));	
  $tokens = @preg_split("/&/", $token);
	
  foreach ($tokens as $item) {
     $items = preg_split ("/=/", $item);				
     $$items[0] = $items[1];	
  }
}

/*
** -------------------- 
** Database
** -------------------- 
*/

plaatenergy_db_connect($dbhost, $dbuser, $dbpass, $dbname);
plaatenergy_db_check_version($version);

/*
** -------------------
** Main State Machine
** -------------------
*/

general_header();

switch ($pid) {

	case PAGE_HOME: 
		include "home.php";
		plaatenergy_home();
		break;

	case PAGE_ABOUT: 
		include "about.php";
		plaatenergy_about();
		break;

	case PAGE_DONATE: 
		include "donate.php";
		plaatenergy_donate();
		break;

	case PAGE_RELEASE_NOTES: 
		include "release_notes.php";
		plaatenergy_release_notes();
		break;
	 
	case PAGE_REPORT: 
		include "report.php";
		plaatenergy_report();
		break;
		
	case PAGE_DAY_IN_KWH_EDIT: 
		include "day_in_kwh_edit.php";
		plaatenergy_day_in_edit();
		break;
		
	case PAGE_DAY_OUT_KWH_EDIT: 
		include "day_out_kwh_edit.php";
		plaatenergy_day_out_edit();
		break;
		
	case PAGE_DAY_PRESSURE: 
		include "day_pressure.php";
		plaatenergy_day_pressure();
		break;
		
	case PAGE_DAY_TEMPERATURE: 
		include "day_temperature.php";
		plaatenergy_day_temperature();
		break;
		
	case PAGE_DAY_HUMIDITY: 
		include "day_humidity.php";
		plaatenergy_day_humidity();
		break;
				
	case PAGE_YEARS_IN_GAS_EURO:
	case PAGE_YEARS_IN_GAS_M3:
		include "years_in_gas.php";
		plaatenergy_years_in_gas();
		break;
		
	case PAGE_MONTH_OUT_ENERGY_MAX:
		include "month_out_max.php";
		plaatenergy_month_out_energy_max();
		break;
}

general_footer();

plaatenergy_db_close();

/*
** -------------------
** The End
** -------------------
*/

?>
