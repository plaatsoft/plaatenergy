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
 * @brief contain home page
 */
 
 
/*
** ---------------------
** PARAMETERS
** ---------------------
*/

$solar_meter_present = plaatenergy_db_get_config_item('solar_meter_present', SOLAR_METER_1);
$weather_station_present = plaatenergy_db_get_config_item('weather_station_present', WEATHER_METER_1);
$energy_meter_present = plaatenergy_db_get_config_item('energy_meter_present', ENERGY_METER_1);
$energy_store_details = plaatenergy_db_get_config_item('energy_store_details', ENERGY_METER_1);
$gas_meter_present = plaatenergy_db_get_config_item('gas_meter_present', GAS_METER_1);
$name = plaatenergy_db_get_config_item('system_name', LOOK_AND_FEEL);
$version = plaatenergy_db_get_config_item('database_version');

$password = plaatenergy_post("password", "");
$username = plaatenergy_post("username", "");

if (isset($_SERVER['PHP_AUTH_USER'])) {
	$username = $_SERVER['PHP_AUTH_USER'];
	$eid = EVENT_LOGIN;
}

if (isset($_SERVER['PHP_AUTH_PW'])) {
	$password = $_SERVER['PHP_AUTH_PW'];
	$eid = EVENT_LOGIN;
}

/*
** ---------------------
** UTILS
** ---------------------
*/

/**
 * Check if solar meter is online.
 * @return HTML block with actual status of solar meter.
 */
function check_solar_converter($index) {

	$nr = SOLAR_METER_1;
	
	switch ($index) {
	
		case 2 : $nr = SOLAR_METER_2;
				   break;
		
		case 3 : $nr = SOLAR_METER_3;
				   break;
	}

	$solar_meter_present = plaatenergy_db_get_config_item('solar_meter_present', $nr);
	$solar_meter_vendor = plaatenergy_db_get_config_item('solar_meter_vendor', $nr);

	$page="";

	if (($solar_meter_present=="true") && ($solar_meter_vendor!="unknown")) {
  
		$timestamp = date("Y-m-d H:i:s", strtotime("-3 minutes"));
		$sql = 'select etotal from solar'.$index.' where timestamp >= "'.$timestamp.'"';	
		$result = plaatenergy_db_query($sql);
		$count = plaatenergy_db_num_rows($result);
	
		if ($count>0){

			$page  = '<div class="checker good">';
			$page .= t('SOLAR_METER_'.$index.'_CONNECTION_UP');
			$page .='</div>';

		} else {

			$page = '<div class="checker bad" title="'.t('NO_MEASUREMENT_ERROR').'">';
			$page .= t('SOLAR_METER_'.$index.'_CONNECTION_DOWN');	
			$page .='</div>';
		}
	}
	return $page;
}

/**
 * Check if energy converter is online.
 * @return HTML block with actual status of solar converter.
 */
function check_energy_meter($index) {
  
   global $energy_meter_present;

	$page = "";	
	
	if ($energy_meter_present=="true") {
	   
		$timestamp = date("Y-m-d H:i:s", strtotime("-3 minutes"));
		$sql = 'select low_used from energy'.$index.' where timestamp >= "'.$timestamp.'"';	
		$result = plaatenergy_db_query($sql);
		$count = plaatenergy_db_num_rows($result);
	
		 if ($count>0){
		 
			$page  = '<div class="checker good">';
			$page .= t('ENERGY_METER_'.$index.'_CONNECTION_UP');
			$page .= '</div>';
			
		} else {
		
			$page = '<div class="checker bad" title="'.t('NO_MEASUREMENT_ERROR').'">';
			$page .= t('ENERGY_METER_'.$index.'_CONNECTION_DOWN');
			$page .= '</div>';
		}
   }	
	return $page;
}

/**
 * Check if weather station is online.
 * @return HTML block with actual status of weather station.
 */
function check_weather_station() {

   global $weather_station_present;
    
   $page = "";
	
   if ($weather_station_present=="true") {
  
		$timestamp = date("Y-m-d H:i:s", strtotime("-3 minutes"));
		$sql = 'select humidity from weather where timestamp >= "'.$timestamp.'"';	
		$result = plaatenergy_db_query($sql);
		$count = plaatenergy_db_num_rows($result);
		
		if ($count>0){
		 
			$page  = '<div class="checker good">';
			$page .= t('WEATHER_METER_CONNECTION_UP');
			$page .= '</div>';
			
		} else {
		
			$page = '<div class="checker bad" title="'.t('NO_MEASUREMENT_ERROR').'">';
			$page .= t('WEATHER_METER_CONNECTION_DOWN');
			$page .= '</div>';
		}
	}
	return $page;
}


/*
** ---------------------
** EVENTS
** ---------------------
*/

function plaatenergy_home_login_event() {

	global $pid;
	global $session;
	global $password;
	global $username;
	global $ip;
		
	$home_password = plaatenergy_db_get_config_item('home_password',SECURITY);
	$home_username = plaatenergy_db_get_config_item('home_username',SECURITY);
	
	if ( ($home_password==md5($password)) && ($home_username==$username) ) {
	
		$session = plaatenergy_db_get_session($ip, true);
		$pid = PAGE_HOME;
	} 
}

/*
** ---------------------------------------------------------------- 
** PAGE
** ---------------------------------------------------------------- 
*/

function plaatenergy_home_login_page() {

   // input
   global $id;
	global $name;
	global $version;
			
	$page = '<h1>';
   $page .= t('TITLE').' ';
	$page .= '<span id="version">'.$version."</span>";
	if (strlen($name)>0) {
		$page .= ' ('.$name.') ';
	} 	
	$page .= '</h1>';
	
	$page .= '<fieldset>';
	
	$page .= '<br/>';
   $page .= '<label>'.t('LABEL_USERNAME').'</label>';
   $page .= '<input type="text" name="username" size="20" maxlength="20" autofocus/>';
   $page .= '<br/>';

   $page .= '<br/>';
   $page .= '<label>'.t('LABEL_PASSWORD').'</label>';
   $page .= '<input type="password" name="password" size="20"/>';
   $page .= '<br/>';
  
   $page .= '<div class="nav">';   
   $page .= '<input type="hidden" name="token" value="pid='.PAGE_HOME_LOGIN.'&eid='.EVENT_LOGIN.'"/>';
   $page .= '<input type="submit" name="Submit" id="normal_link" value="'.t('LINK_LOGIN').'"/>';
   $page .= '</div>';
	
	$page .= '</fieldset>';
	
   $page .= '<br/>';
	$page .= '<div class="upgrade" id="upgrade"></div>';
	$page .= '<script type="text/javascript" src="js/version1.js"></script>';
	
   return $page;
}

/**
 * Home Page
 * @return HTML block which contain home page.
 */
function plaatenergy_home_page() {

	// input	
	global $energy_meter_present;
	global $solar_meter_present;
	global $gas_meter_present;
	global $weather_station_present;
	global $energy_store_details;
	global $name;
	global $session;
	global $version;
	
	$page = '<h1>';
    $page .= t('TITLE').' ';
	$page .= '<span id="version">'.$version."</span>";
	if (strlen($name)>0) {
		$page .= ' ('.$name.') ';
	} 	
	$page .= '</h1>';
	$page .= '<div class="upgrade" id="upgrade"></div>';
	$page .= '<script type="text/javascript" src="js/version1.js"></script>';

	$page .= '<div class="home">';
	$page .= '<table>';

	$page .= '<tr>';
	$page .= '<th>'.t('YEARS_REPORT').'</th>';
	$page .= '<th>'.t('YEAR_REPORT').'</th>';
	$page .= '<th>'.t('MONTH_REPORT').'</th>';
	$page .= '<th>'.t('DAY_REPORT').'</th>';
	
	if ($weather_station_present=="true") { 	
		$page .= '<th>'.t('OTHER_REPORT').'</th>';   
	}
	$page .= '</tr>';
	
	if ($energy_meter_present=="true") { 	
		$page .= '<tr>';
		$page .= '<td>';
		$page .= plaatenergy_link('pid='.PAGE_YEARS_IN_ENERGY.'&eid='.EVENT_KWH, t('LINK_IN_ENERGY'));
		$page .= '</td>';
		$page .= '<td>';
		$page .= plaatenergy_link('pid='.PAGE_YEAR_IN_ENERGY.'&eid='.EVENT_KWH, t('LINK_IN_ENERGY'));
		$page .= '</td>';
		$page .= '<td>';
		$page .= plaatenergy_link('pid='.PAGE_MONTH_IN_ENERGY.'&eid='.EVENT_KWH, t('LINK_IN_ENERGY'));
		$page .= '</td>';
		$page .= '<td>';
		$page .= plaatenergy_link('pid='.PAGE_DAY_IN_ENERGY.'&eid='.EVENT_KWH, t('LINK_IN_ENERGY'));
		$page .= '</td>';
		if ($weather_station_present=="true") { 
			$page .= '<td>';
			$page .= plaatenergy_link('pid='.PAGE_DAY_PRESSURE, t('LINK_PRESSURE'));
			$page .= '</td>';
		}
		$page .= '</tr>';
		
		if ($energy_store_details=="true") { 	
		
			$page .= '<tr>';
			$page .= '<td>';
			$page .= '</td>';
			$page .= '<td>';
			$page .= '</td>';
			$page .= '<td>';
			$page .= '</td>';
			$page .= '<td>';
			$page .= plaatenergy_link('pid='.PAGE_DAY_IN_VOLTAGE, t('LINK_IN_VOLTAGE'));
			$page .= '</td>';
			$page .= '<td>';
			$page .= '</td>';
			$page .= '</tr>';		
			
			$page .= '<tr>';
			$page .= '<td>';
			$page .= '</td>';
			$page .= '<td>';
			$page .= '</td>';
			$page .= '<td>';
			$page .= '</td>';
			$page .= '<td>';
			$page .= plaatenergy_link('pid='.PAGE_DAY_IN_CURRENT, t('LINK_IN_CURRENT'));
			$page .= '</td>';
			$page .= '<td>';
			$page .= '</td>';
			$page .= '</tr>';		
			
			$page .= '<tr>';
			$page .= '<td>';
			$page .= '</td>';
			$page .= '<td>';
			$page .= '</td>';
			$page .= '<td>';
			$page .= '</td>';
			$page .= '<td>';
			$page .= plaatenergy_link('pid='.PAGE_DAY_IN_POWER, t('LINK_IN_POWER'));
			$page .= '</td>';
			$page .= '<td>';
			$page .= '</td>';
			$page .= '</tr>';		
		}
	}
	
	if ($solar_meter_present=="true") { 
		$page .= '<tr>';
		$page .= '<td>';
		$page .= plaatenergy_link('pid='.PAGE_YEARS_OUT_ENERGY.'&eid='.EVENT_KWH, t('LINK_OUT_ENERGY'));
		$page .= '</td>';
		$page .= '<td>';
		$page .= plaatenergy_link('pid='.PAGE_YEAR_OUT_ENERGY.'&eid='.EVENT_KWH, t('LINK_OUT_ENERGY'));
		$page .= '</td>';
		$page .= '<td>';
		$page .= plaatenergy_link('pid='.PAGE_MONTH_OUT_ENERGY.'&eid='.EVENT_KWH, t('LINK_OUT_ENERGY'));
		$page .= '</td>';
		$page .= '<td>';
		$page .= plaatenergy_link('pid='.PAGE_DAY_OUT_ENERGY.'&eid='.EVENT_KWH, t('LINK_OUT_ENERGY'));
		$page .= '</td>';
		if ($weather_station_present=="true") { 
			$page .= '<td>';
			$page .= plaatenergy_link('pid='.PAGE_DAY_TEMPERATURE, t('LINK_TEMPERATURE'));
			$page .= '</td>';
		}
		$page .= '</tr>';
	}

	if ($gas_meter_present=="true") { 
		$page .= '<tr>';
		$page .= '<td>';
		$page .= plaatenergy_link('pid='.PAGE_YEARS_IN_GAS.'&eid='.EVENT_M3, t('LINK_IN_GAS'));
		$page .= '</td>';
		$page .= '<td>';
		$page .= plaatenergy_link('pid='.PAGE_YEAR_IN_GAS.'&eid='.EVENT_M3, t('LINK_IN_GAS'));
		$page .= '</td>';
		$page .= '<td>';
		$page .= plaatenergy_link('pid='.PAGE_MONTH_IN_GAS.'&eid='.EVENT_M3, t('LINK_IN_GAS'));
		$page .= '</td>';
		$page .= '<td>';
		$page .= plaatenergy_link('pid='.PAGE_DAY_IN_GAS.'&eid='.EVENT_M3, t('LINK_IN_GAS'));
		$page .= '</td>';
		if ($weather_station_present=="true") { 
			$page .= '<td>';
			$page .= plaatenergy_link('pid='.PAGE_DAY_HUMIDITY, t('LINK_HUMIDITY'));
	   $page .= '</td>';
		}
		$page .= '</tr>';
	}
		
	$page .= '</table>';

	$page .= '<table>';
	$page .= '<tr>';
	$page .= '<th width="25%"></th>';
	$page .= '<th width="25%"></th>';
	$page .= '<th width="25%"></th>';
	$page .= '<th width="25%"></th>';
	$page .= '</tr>';
		
	$page .= '<tr>';
	$page .= '<td>';
	$page .= '&nbsp;';
	$page .= '</td>';				
	$page .= '<td>';
	$page .= plaatenergy_link('pid='.PAGE_REPORT, t('LINK_REPORT'));
	$page .= '</td>';		
	$page .= '<td>';
	$settings_password = plaatenergy_db_get_config_item('settings_password',SECURITY);		
	if (strlen($settings_password)>0) {
		$page .= plaatenergy_link('pid='.PAGE_SETTING_LOGIN, t('LINK_SETTINGS')); 
	} else {
		$page .= plaatenergy_link('pid='.PAGE_SETTING_CATEGORY, t('LINK_SETTINGS')); 
	}
	$page .= '</td>';		
	$page .= '<td>';
	$page .= '&nbsp;';
	$page .= '</td>';		
	$page .= '</tr>';

	$page .= '<tr>';
	$page .= '<td>';
	$page .= plaatenergy_link('pid='.PAGE_EXPORT_IMPORT, t('LINK_IMPORT_EXPORT'));
	$page .= '</td>';
	$page .= '<td>';
	$page .= plaatenergy_link('pid='.PAGE_DONATE, t('LINK_DONATE'));
	$page .= '</td>';
	$page .= '<td>';
	$page .= plaatenergy_link('pid='.PAGE_ABOUT, t('LINK_ABOUT'));				
	$page .= '</td>';
	$page .= '<td>';
	$page .= plaatenergy_link('pid='.PAGE_RELEASE_NOTES, t('LINK_RELEASE_NOTES'));		
	$page .= '</td>';
	$page .= '</tr>';
	
	$page .= '<tr>';
	$page .= '<td>';
	$page .= '</td>';
	$page .= '<td>';
	$page .= plaatenergy_link('pid='.PAGE_REALTIME, t('LINK_GUI'));
	$page .= '</td>';
	$page .= '<td>';
	$page .= plaatenergy_link('pid='.PAGE_SYSTEM, t('LINK_SYSTEM'));
	$page .= '</td>';
	$page .= '<td>';
	$page .= '</td>';
	$page .= '</tr>';

	$page .= '</table>';
	$page .= '</div>';

	$page .= '<br/><br/>';
	
	$page .= check_energy_meter(1);
	$page .= check_solar_converter(1); 
	$page .= check_solar_converter(2);
	$page .= check_solar_converter(3);
	$page .= check_weather_station();

	$page .= '<br/><br/>';

	return $page;
}

/*
** ---------------------
** HANDLER
** ---------------------
*/

/**
 * Home Page Handler
 * @return HTML block which contain home page.
 */
function plaatenergy_home() {

	/* input */
	global $pid;
	global $eid;
	
	/* Event handler */
	switch ($eid) {

		case EVENT_LOGIN:
			plaatenergy_home_login_event();
			break;		
   }
		
	/* Page handler */
	switch ($pid) {
		
		case PAGE_HOME_LOGIN:
			return plaatenergy_home_login_page();
			break;
			
		case PAGE_HOME:
			return plaatenergy_home_page();
			break;
	}
}

/*
** ---------------------
** THE END
** ---------------------
*/

?>
