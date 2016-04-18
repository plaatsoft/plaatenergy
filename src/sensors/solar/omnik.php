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

include '/var/www/html/plaatenergy/config.inc';
include '/var/www/html/plaatenergy/database.inc';
include '/var/www/html/plaatenergy/general.inc';
include 'inverter_omnik.php';

$index = $argv[1];
$instance = $argv[2];

plaatenergy_db_connect($dbhost, $dbuser, $dbpass, $dbname);

$ip = plaatenergy_db_get_config_item('solar_meter_ip', $instance);
$port = plaatenergy_db_get_config_item('solar_meter_port', $instance);
$sn = plaatenergy_db_get_config_item('solar_meter_serial_number', $instance);

$inverter = new Inverter($ip,$port,$sn);				

if ($inverter->read()==false) {

	echo "$inverter->errorcode : $inverter->error";		
	
} else {
		
	$data = json_decode($inverter->power());

	$timestamp = date('Y-m-d H:i:00');

	if (isset($data->pac1) && ($data->pac1>0)) 
	{
		$sql1 = 'select id from solar'.$index.' where timestamp="'.$timestamp.'"';
		$result1 = plaatenergy_db_query($sql1);
		$data1 = plaatenergy_db_fetch_object($result1);
		
		if ( isset($data1->id) ) {
	
			$sql  = 'update solar'.$index.' set temp='.$data->temperature.', vdc1='.$data->vdc1.', ';
			$sql .= 'vdc2='.$data->vdc2.', idc1='.$data->idc1.', ';
			$sql .= 'idc2='.$data->idc2.', iac='.$data->iac1.', ';
			$sql .= 'vac='.$data->vac2.', fac='.$data->fac1.', ';
			$sql .= 'pac='.$data->pac1.', etoday='.$data->etoday.', ';
			$sql .= 'etotal='.$data->etotal.' where id='.$data1->id;
			
		} else {
		
			$sql  = 'insert into solar'.$index.' ( timestamp, temp,vdc1,vdc2,idc1,idc2,iac,vac,fac,pac,etoday,etotal) ';
			$sql .= 'values ("'.$timestamp.'",'.$data->temperature.','.$data->vdc1.','.$data->vdc2.','.$data->idc1.','.
			$data->idc2.','.$data->iac1.','.$data->vac1.','.$data->fac1.','.$data->pac1.','.$data->etoday.','.$data->etotal.')';
		}
			
		plaatenergy_db_query($sql);
	}
}
	
?>