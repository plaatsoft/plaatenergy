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
 
include '/var/www/html/plaatenergy/config.inc';
include '/var/www/html/plaatenergy/database.inc';
include '/var/www/html/plaatenergy/general.inc';

plaatenergy_db_connect($dbhost, $dbuser, $dbpass, $dbname);

$name = plaatenergy_db_get_config_item('webcam_name', 61);
$resolution = plaatenergy_db_get_config_item('webcam_resolution', 61);

while (true) {

   global $name;
	global $resolution;
	 
	$command = 'fswebcam -r '.$resolution. '--title '.$name.' -S 2 '.BASE_DIR.'/webcam/image.jpg';

	exec($command);

	// sleep for 1 second
	sleep(1);
}

?>