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
 * @brief contain cron page
 */
  
$time_start = microtime(true);

include "config.inc";
include "general.inc";
include "database.inc";

/*
** ---------------------
** CRON
** ---------------------
*/

plaatenergy_db_connect($dbhost, $dbuser, $dbpass, $dbname);

$solar_meter_present1 = plaatenergy_db_get_config_item('solar_meter_present', SOLAR_METER_1);
if ($solar_meter_present1=="true") {
	$solar_meter_vendor1 = plaatenergy_db_get_config_item('solar_meter_vendor', SOLAR_METER_1);
	if ($solar_meter_vendor1!="unknown") {
	
		exec('php /var/www/html/plaatenergy/sensors/solar/'.$solar_meter_vendor1.'.php 1 '.SOLAR_METER_1);
	}
}

$solar_meter_present2 = plaatenergy_db_get_config_item('solar_meter_present', SOLAR_METER_2);
if ($solar_meter_present2=="true") {
	$solar_meter_vendor2 = plaatenergy_db_get_config_item('solar_meter_vendor', SOLAR_METER_2);
	if ($solar_meter_vendor2!="unknown") {
	
		exec('php /var/www/html/plaatenergy/sensors/solar/'.$solar_meter_vendor2.'.php 2 '.SOLAR_METER_2);
	}
}

$solar_meter_present3 = plaatenergy_db_get_config_item('solar_meter_present', SOLAR_METER_3);
if ($solar_meter_present3 =="true") {
	$solar_meter_vendor3 = plaatenergy_db_get_config_item('solar_meter_vendor', SOLAR_METER_3);
	if ($solar_meter_vendor3!="unknown") {
		exec('php /var/www/html/plaatenergy/sensors/solar/'.$solar_meter_vendor3.'.php 3 '.SOLAR_METER_3);
	}
}

$weather_station_present = plaatenergy_db_get_config_item('weather_station_present', WEATHER_METER_1);
if ($weather_station_present=="true") {
   $weather_station_vendor = plaatenergy_db_get_config_item('weather_station_vendor', WEATHER_METER_1);
   exec('sudo python /var/www/html/plaatenergy/sensors/weather/'.$weather_station_vendor.'.py');
}

$energy_meter_present = plaatenergy_db_get_config_item('energy_meter_present', ENERGY_METER_1);
if ($energy_meter_present=="true") {
   $energy_meter_vendor = plaatenergy_db_get_config_item('energy_meter_vendor', ENERGY_METER_1);
   exec('python /var/www/html/plaatenergy/sensors/p1/'.$energy_meter_vendor.'.py');
}

if ($weather_station_present=="true") {
   exec('python /var/www/html/plaatenergy/sensors/display/display.py');
}

plaatenergy_db_process(EVENT_PROCESS_TODAY);

plaatenergy_db_close();

// Calculate to page render time
$time_end = microtime(true);
$time = $time_end - $time_start;

echo "cron took ".round($time,2)." secs";


/*
** ---------------------
** THE END
** ---------------------
*/

?> 

 
