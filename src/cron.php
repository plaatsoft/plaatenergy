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

$solar_meter_present = plaatenergy_db_get_config_item('solar_meter_present');
if ($solar_meter_present=="true") {
   exec('sudo python /var/www/html/plaatenergy/sensors/omnik/Omnik.py');
}

$weather_station_present = plaatenergy_db_get_config_item('weather_station_present');
if ($weather_station_present=="true") {
   exec('sudo python /var/www/html/plaatenergy/sensors/weather/weather.py');
}

$energy_meter_present = plaatenergy_db_get_config_item('energy_meter_present');
if ($energy_meter_present=="true") {
   $energy_meter_vendor = plaatenergy_db_get_config_item('energy_meter_vendor');
   exec('python /var/www/html/plaatenergy/sensors/p1/'.$energy_meter_vendor.'.py');
}

if ($weather_station_present=="true") {
   exec('sudo python /var/www/html/plaatenergy/sensors/display/display.py');
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

 
