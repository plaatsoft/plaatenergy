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

require_once('inverter_omnik.php');
	
$ip='192.168.2.102';	// your ip-address
$port=8899;				// do not change, unless you have other info about the port
$sn=1606789503;			// your serial-number
	
$inverter	=	new Inverter($ip,$port,$sn);				
if ($inverter->read()===false) {
	echo "$inverter->errorcode : $inverter->error";			
}
		
echo $inverter->displaybuffer();		
	
echo '<br/>';
	
$p=$inverter->power();
echo $p;
	
?>