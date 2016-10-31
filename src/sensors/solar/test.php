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

// instance=31 [Solar Converter 1]
// instance=32 [Solar Converter 2]
// instance=33 [Solar Converter 3]

include '/var/www/html/plaatenergy/config.php';
include '/var/www/html/plaatenergy/database.php';
include '/var/www/html/plaatenergy/general.php';
include 'inverter_hosola.php';

plaatenergy_db_connect($dbhost, $dbuser, $dbpass, $dbname);

$instance = $argv[1];
$ip = plaatenergy_db_get_config_item('solar_meter_ip', $instance);
$port = plaatenergy_db_get_config_item('solar_meter_port', $instance);
$sn = plaatenergy_db_get_config_item('solar_meter_serial_number', $instance);

echo "instance=".$instance."\r\n";				
echo "ip=".$ip."\r\n";				
echo "port=".$port."\r\n";
echo "sn=".$sn."\r\n";

echo "\r\n";
echo "Start connection to Solar Converter\r\n";

$inverter = new Inverter($ip,$port,$sn);				
if ($inverter->read()==false) {

	echo "$inverter->errorcode : $inverter->error";			
	
} else {
		
	$data = json_decode($inverter->power());

 	var_dump($data);	
}

echo "\r\n";
echo "Test Ended\r\n";
echo "\r\n";
?>
