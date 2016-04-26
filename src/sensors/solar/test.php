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


// 31 Solar Converter 1
// 32 Solar Converter 2
// 33 Solar Converter 3

include '/var/www/html/plaatenergy/config.inc';
include '/var/www/html/plaatenergy/database.inc';
include '/var/www/html/plaatenergy/general.inc';
include 'inverter_hosola.php';

$instance = $argv[1];

plaatenergy_db_connect($dbhost, $dbuser, $dbpass, $dbname);

$ip = plaatenergy_db_get_config_item('solar_meter_ip', $instance);
$port = plaatenergy_db_get_config_item('solar_meter_port', $instance);
$sn = plaatenergy_db_get_config_item('solar_meter_serial_number', $instance);

echo $ip;

$inverter = new Inverter($ip,$port,$sn);				
if ($inverter->read()==false) {

	echo "$inverter->errorcode : $inverter->error";			
	
} else {
		
	$data = json_decode($inverter->power());

 	var_dump($data);	
}
?>
