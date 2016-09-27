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
    
	$timestamp1 = date("Y-m-d H:i:00", time()-60);
	$timestamp2 = date("Y-m-d H:i:00");
	    
	$solar_meter_present_1 = plaatenergy_db_get_config_item('solar_meter_present', SOLAR_METER_1);
	$solar_meter_present_2 = plaatenergy_db_get_config_item('solar_meter_present', SOLAR_METER_2);
	$solar_meter_present_3 = plaatenergy_db_get_config_item('solar_meter_present', SOLAR_METER_3);	
	$energy_meter_present = plaatenergy_db_get_config_item('energy_meter_present', ENERGY_METER_1);
	$gas_meter_present = plaatenergy_db_get_config_item('gas_meter_present', GAS_METER_1);
	$weather_station_present = plaatenergy_db_get_config_item('weather_station_present', WEATHER_METER_1);
	
	$sql1  = 'select pac, etoday from solar1 where ';
	$sql1 .= 'timestamp>="'.$timestamp1.'" and timestamp<="'.$timestamp2.'" order by timestamp desc limit 0,1';
	$result1 = plaatenergy_db_query($sql1);
	$data1 = plaatenergy_db_fetch_object($result1);
		
	$pac1 = 0;
	$etoday1 = 0;
	if ( isset($data1->pac) ) {
		$pac1 = $data1->pac;
		$etoday1 = $data1->etoday;
	}
		
	$sql2  = 'select pac, etoday from solar2 where ';
	$sql2 .= 'timestamp>="'.$timestamp1.'" and timestamp<="'.$timestamp2.'" order by timestamp desc limit 0,1';
	$result2 = plaatenergy_db_query($sql2);
	$data2 = plaatenergy_db_fetch_object($result2);
	
	$pac2 = 0;
	$etoday2 = 0;
	if ( isset($data2->pac) ) {
		$pac2 = $data2->pac;
		$etoday2 = $data2->etoday;
	}
	
	$sql3  = 'select pac, etoday from solar3 where ';
	$sql3 .= 'timestamp>="'.$timestamp1.'" and timestamp<="'.$timestamp2.'" order by timestamp desc limit 0,1';
	$result3 = plaatenergy_db_query($sql3);
	$data3 = plaatenergy_db_fetch_object($result3);
	
	$pac3 = 0;
	$etoday3 = 0;
	if ( isset($data3->pac) ) {
		$pac3 = $data3->pac;
		$etoday3 = $data3->etoday;
	}
	
	$pac = $pac1 + $pac2 + $pac3;
			
	$sql4  = 'select power, gas_used from energy1 where ';
	$sql4 .= 'timestamp>="'.$timestamp1.'" and timestamp<="'.$timestamp2.'" order by timestamp desc limit 0,1';
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
	
	$sql5  = 'select gas_used from energy_summary where date="'.date("Y-m-d").'"';
	$result5 = plaatenergy_db_query($sql5);
	$data5 = plaatenergy_db_fetch_object($result5);
	
	$gas_used = 0;
	if (isset($data5->gas_used)) {
		$gas_used = $data5->gas_used;
	}
	
	$sql6  = 'select humidity, pressure, temperature from weather where ';
	$sql6 .= 'timestamp>="'.$timestamp1.'" and timestamp<="'.$timestamp2.'" order by timestamp desc limit 0,1';
	$result6 = plaatenergy_db_query($sql6);
	$data6 = plaatenergy_db_fetch_object($result6);
	
	$humidity = 0;
	$pressure = 0;
	$temperature = 0;
	if (isset($data6->temperature)) {
		
		$humidity = $data6->humidity;
		$pressure = $data6->pressure;
		$temperature = $data6->temperature;
	}
	
		
	$page  = '<h1>'.t('SYSTEM_TITLE').'</h1>';
	
	$page .= '<table>';
	$page .= '<tr>';
	
	if ($solar_meter_present_1=="true") {
		$page .= '<td>';
		$page .= '<img src="images/solarpanel-'.$theme.'.png" height="80" width="120">';
		$page .= '<br/>';
		$page .= $pac1.' '.t('WATT').'<br/>'.$etoday1.' '.t('KWH');
		$page .= '</td>';
	}
	
	if ($solar_meter_present_2=="true") {
		$page .= '<td>';
		$page .= '<img src="images/solarpanel-'.$theme.'.png" height="80" width="120">';
		$page .= '<br/>';
		$page .= $pac2.' '.t('WATT').'<br/>'.$etoday2.' '.t('KWH');;
		$page .= '</td>';
	}
	
	if ($solar_meter_present_3=="true") {
		$page .= '<td>';
		$page .= '<img src="images/solarpanel-'.$theme.'.png" height="80" width="120">';
		$page .= '<br/>';
		$page .= $pac3.' '.t('WATT').'<br/>'.$etoday3.' '.t('KWH');;
		$page .= '</td>';		
	}
	
	$page .= '</tr>';
	$page .= '</table>';
	
	$page .= '<br/>';	
	
	$page .= '<table>';	
	$page .= '<tr>';	
	
	if ($gas_meter_present =="true") {
	
		$page .= '<td>';
		$page .= '<img src="images/gas-'.$theme.'.png" height="80" width="90">';
		$page .= '<br/>';		
		$page .= round($gas_used,2).' '.t('M3');
		$page .= '</td>';
	}
	
	if ($energy_meter_present =="true") {
	
		$page .= '<td>';
		$page .= '<img src="images/lamp-'.$theme.'.png" height="80" width="90">';
		$page .= '<br/>';		
		$page .= $used.' '.t('WATT');
		$page .= '</td>';
	}
	
	$page .= '<td>';
	$page .= '<img src="images/powerline-'.$theme.'.png" height="80" width="90">';
	$page .= '<br/>';
	$page .= $delivered.' '.t('WATT');
	$page .= '</td>';

	$page .= '</tr>';	
	$page .= '</table>';
	
	if ($weather_station_present =="true") {
	
		$page .= '<br/>';
		
		$page .= '<table>';	
		$page .= '<tr>';	

		$page .= '<td>';
		$page .= '<img src="images/temperature-'.$theme.'.png" height="80" width="90">';
		$page .= '<br/>';		
		$page .= round($temperature,2).' &deg;C';
		$page .= '</td>';
	
		if ($humidity>0) {
	
			$page .= '<td>';
			$page .= '<img src="images/humidity-'.$theme.'.png" height="80" width="90">';
			$page .= '<br/>';		
			$page .= round($humidity,2).' %';
			$page .= '</td>';
		}
		
		if ($pressure>0) {
			$page .= '<td>';
			$page .= '<img src="images/pressure-'.$theme.'.png" height="80" width="90">';
			$page .= '<br/>';
			$page .= round($pressure,2).' hPa';
			$page .= '</td>';
		}
	
		$page .= '</tr>';	
		$page .= '</table>';
	}
	
	$page .= '<br/>';

	$page .= '<div class="nav">';
	$page .= plaatenergy_link('pid='.PAGE_HOME, t('LINK_HOME'));
	$page .=  '</div>';
	
	$page .= '<script>setTimeout(link,10000,\'pid='.$pid.'\');</script>';
	
	
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
