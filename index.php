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
** ---------------------------------------------------------------- 
** POST
** ---------------------------------------------------------------- 
*/
$token = plaatenergy_post("token", "");

if (strlen($token)>0) {
	
	/* Decode token */
	$token = gzinflate(base64_decode($token));	
	$tokens = @preg_split("/&/", $token);
	
	foreach ($tokens as $item) {
		$items = preg_split ("/=/", $item);				
		$$items[0] = $items[1];	
	}
}

/*
** ---------------------------------------------------------------- 
** Database
** ---------------------------------------------------------------- 
*/

plaatenergy_db_connect($dbhost, $dbuser, $dbpass, $dbname);
plaatenergy_db_check_version($version);

$solar_meter_ip_address = plaatenergy_db_get_config_item('solar_meter_ip_address');
/*
** ---------------------------------------------------------------- 
** OUTPUT
** ---------------------------------------------------------------- 
*/

general_header();

include "home.inc";
echo home_page();

general_footer();

?>
