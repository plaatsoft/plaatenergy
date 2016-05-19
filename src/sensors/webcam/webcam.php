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
 * @brief webcam script
 */
 
// instance=61 [Webcam 1]
// instance=62 [Webcam 2]
// instance=63 [Webcam 3]

include '/var/www/html/plaatenergy/config.inc';
include '/var/www/html/plaatenergy/database.inc';
include '/var/www/html/plaatenergy/general.inc';

$index = $argv[1];

plaatenergy_db_connect($dbhost, $dbuser, $dbpass, $dbname);

while (true) {

	global $index;
	
	$instance = 1; 
	switch ($index) {
		case 1: 	$instance=61;
					break;
		case 2: 	$instance=62;
					break;
		case 3: 	$instance=63;
					break;
	}
    
	$name = plaatenergy_db_get_config_item('webcam_name', $instance);
	$resolution = plaatenergy_db_get_config_item('webcam_resolution', $instance);
	$device = plaatenergy_db_get_config_item('webcam_device', $instance);
	 
	$command = 'fswebcam -q --device '.$device.' --timestamp "%Y-%m-%d %H:%M:%S" -r '.$resolution. ' --title '.$name.' -S 2 '.BASE_DIR.'/webcam/image'.$index.'.jpg';
    
	exec ($command);

	// sleep 1 second
	sleep(0.5);
}

?>
