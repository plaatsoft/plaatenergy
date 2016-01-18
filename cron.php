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
include "database.inc";

/*
** ---------------------
** CRON
** ---------------------
*/

plaatenergy_db_connect($dbhost, $dbuser, $dbpass, $dbname);

$energy_meter_present = plaatenergy_db_get_config_item('energy_meter_present');

if ($energy_meter_present!="false") {
   exec('sudo python /var/www/html/plaatenergy/sensors/p1/p1.py');
}

$solar_meter_present = plaatenergy_db_get_config_item('solar_meter_present');
if ($solar_meter_present!="false") {
   exec('sudo python /var/www/html/plaatenergy/sensors/omnik/Omnik.py');
}

$weather_station_present = plaatenergy_db_get_config_item('weather_station_present');
if ($weather_station_present!="false") {
   exec('sudo python /var/www/html/plaatenergy/sensors/weather/weather.py');
}

plaatenergy_db_process();

/*
** ---------------------
** THE END
** ---------------------
*/

?> 

 