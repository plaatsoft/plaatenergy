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
 * @brief contain year in gas report
 */
 
/*
** ---------------------
** PAGES
** ---------------------
*/

function plaatenergy_system_page() {

	// input
	global $pid;
	global $ip;

	$extra ="";
	$sql  = 'select theme from session where ip="'.$ip.'"';
	$result = plaatenergy_db_query($sql);
	$row = plaatenergy_db_fetch_object($result);
	$theme = $row->theme;
    
	$timestamp = date("Y-m-d H:i:00");

	$solar_meter_present_1 = plaatenergy_db_get_config_item('solar_meter_present', SOLAR_METER_1);
	$solar_meter_present_2 = plaatenergy_db_get_config_item('solar_meter_present', SOLAR_METER_2);
	$solar_meter_present_3 = plaatenergy_db_get_config_item('solar_meter_present', SOLAR_METER_3);
	
	$energy_meter_present = plaatenergy_db_get_config_item('energy_meter_present', ENERGY_METER_1);
	
	$sql1  = 'select pac from solar1 where timestamp="'.$timestamp.'"';
	$result1 = plaatenergy_db_query($sql1);
	$data1 = plaatenergy_db_fetch_object($result1);
		
	$pac1 = 0;
	if ( isset($data1->pac) ) {
		$pac1 = $data1->pac;
	}
		
	$sql2  = 'select pac from solar2 where timestamp="'.$timestamp.'"';
	$result2 = plaatenergy_db_query($sql2);
	$data2 = plaatenergy_db_fetch_object($result2);
	
	$pac2 = 0;
	if ( isset($data2->pac) ) {
		$pac2 = $data2->pac;
	}
	
	$sql3  = 'select pac from solar3 where timestamp="'.$timestamp.'"';
	$result3 = plaatenergy_db_query($sql3);
	$data3 = plaatenergy_db_fetch_object($result3);
	
	$pac3 = 0;
	if ( isset($data3->pac) ) {
		$pac3 = $data3->pac;
	}
	
	$pac = $pac1 + $pac2 + $pac3;
	
	
	$sql4  = 'select power from energy1 where timestamp="'.$timestamp.'"';
	$result4 = plaatenergy_db_query($sql4);
	$data4 = plaatenergy_db_fetch_object($result4);
	$power = 0;
	if ( isset($data4->power) ) {
		$power = $data4->power;
	}
	
	$used = $pac - ($power*-1);
	if ($used<0) {
		$used = 0;
	}
		
	$delivered = $pac - $used;
	
	$page  = '<h1>'.t('SYSTEM_TITLE').'</h1>';

	$page .= '<table>';

	$page .= '<tr>';
	
	if ($solar_meter_present_1=="true") {
		$page .= '<td>';
		$page .= '<img src="images/solarpanel-'.$theme.'.png" height="120" width="150">';
		$page .= '<br/>';
		$page .= $pac1.' Watt';
		$page .= '</td>';
	}
	
	if ($solar_meter_present_2=="true") {
		$page .= '<td>';
		$page .= '<img src="images/solarpanel-'.$theme.'.png" height="120" width="150">';
		$page .= '<br/>';
		$page .= $pac2.' Watt';
		$page .= '</td>';
	}
	
	if ($solar_meter_present_3=="true") {
		$page .= '<td>';
		$page .= '<img src="images/solarpanel-'.$theme.'.png" height="120" width="150">';
		$page .= '<br/>';
		$page .= $pac3.' Watt';
		$page .= '</td>';		
	}
	
	$page .= '</tr>';
	$page .= '</table>';
	
	$page .= '<br/>';	
	$page .= '<br/>';	
	
	$page .= '<table>';	
	$page .= '<tr>';	
		
	if ($energy_meter_present =="true") {
	
		$page .= '<td>';
		$page .= '<img src="images/lamp-'.$theme.'.png" height="120" width="120">';
		$page .= '<br/>';		
		$page .= $used.' Watt';
		$page .= '</td>';
	}
	
	$page .= '<td>';
	$page .= '<img src="images/powerline-'.$theme.'.png" height="120" width="120">';
	$page .= '<br/>';
	$page .= $delivered.' Watt';
	$page .= '</td>';

	$page .= '</tr>';	
	$page .= '</table>';
		
	$page .= '<br/>';

	$page .= '<div class="nav">';
	$page .= plaatenergy_link('pid='.PAGE_HOME, t('LINK_HOME'), 'home');
	$page .=  '</div>';
	
	$page .= '<script>setTimeout(link,5000,\'pid='.$pid.'\');</script>';
	
	
	return $page;
}

/*
** ---------------------
** HANDLER
** ---------------------
*/

function plaatenergy_system() {

  /* input */
  global $pid;
	
	/* Page handler */
	switch ($pid) {

		case PAGE_SYSTEM:
			return plaatenergy_system_page();
			break;
	}
}

/*
** ---------------------
** THE END
** ---------------------
*/


?>
