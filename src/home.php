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

$solar_meter_present = plaatenergy_db_get_config_item('solar_meter_present');
$weather_station_present = plaatenergy_db_get_config_item('weather_station_present');
$energy_meter_present = plaatenergy_db_get_config_item('energy_meter_present');

$password = plaatenergy_post("password", "");

/*
** ---------------------
** UTILS
** ---------------------
*/

/**
 * Check if solar meter is online.
 * @return HTML block with actual status of solar meter.
 */
function check_solar_meter() {

  global $solar_meter_present;

  $page="";

  if ($solar_meter_present=="true") {
  
	$timestamp = date("Y-m-d H:i:s", strtotime("-30 minutes"));
   $sql = 'select etotal from solar where timestamp >= "'.$timestamp.'"';	
   $result = plaatenergy_db_query($sql);
	$count = plaatenergy_db_num_rows($result);
	
   if ($count>0){

		$page  = '<div class="checker good">';
      $page .= t('SOLAR_METER_CONNECTION_UP');
      $page .='</div>';

    } else {

      $page = '<div class="checker bad">';
      $page .= t('SOLAR_METER_CONNECTION_DOWN');	
      $page .='</div>';
    }
  }
  return $page;
}

/**
 * Check if energy meter is online.
 * @return HTML block with actual status of energy meter.
 */
function check_energy_meter() {
  
   global $energy_meter_present;

$page = "";	
	if ($energy_meter_present=="true") {
	   
		$timestamp = date("Y-m-d H:i:s", strtotime("-30 minutes"));
		$sql = 'select dal from energy where timestamp >= "'.$timestamp.'"';	
		$result = plaatenergy_db_query($sql);
		$count = plaatenergy_db_num_rows($result);
	
		 if ($count>0){
		 
			$page  = '<div class="checker good">';
			$page .= t('ENERGY_METER_CONNECTION_UP');
			$page .= '</div>';
			
		} else {
		
			$page  = '<div class="checker bad">';
			$page .= t('ENERGY_METER_CONNECTION_DOWN');
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
  
		$timestamp = date("Y-m-d H:i:s", strtotime("-30 minutes"));
		$sql = 'select humidity from weather where timestamp >= "'.$timestamp.'"';	
		$result = plaatenergy_db_query($sql);
		$count = plaatenergy_db_num_rows($result);
		
		if ($count>0){
		 
			$page  = '<div class="checker good">';
			$page .= t('WEATHER_METER_CONNECTION_UP');
			$page .= '</div>';
			
		} else {
		
			$page  = '<div class="checker bad">';
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
	global $password;
	
	$home_password = plaatenergy_db_get_config_item('home_password');
	
	if ($home_password == $password) {
	
		// Correct password, redirect to setting page
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
			
   $page = '<h1>';
   $page .= t('TITLE').' ';
   $page .= '<div id="version" style="display: inline">';
   $page .= plaatenergy_db_get_config_item('database_version');
   $page .= "</div>";
   $page .= '</h1>';

   $page .= '<br/>';
   $page .= '<label>'.t('HOME_LOGIN_TITLE').'</label>';
   $page .= '<input type="text" name="password" size="20" />';
   $page .= '<br/>';

   $page .= '<div class="nav">';
   $page .= plaatenergy_link('pid='.PAGE_HOME_LOGIN.'&eid='.EVENT_LOGIN, t('LINK_LOGIN'));
   $page .= '</div>';

   $page .= '<script type="text/javascript">var ip="'.$_SERVER['SERVER_ADDR'].'";</script>';
   $page .= '<script type="text/javascript" src="js/version.js"></script>';
	
   return $page;
}

/**
 * Home Page
 * @return HTML block which contain home page.
 */
function plaatenergy_home_page() {

	global $weather_station_present;
	global $solar_meter_present;
	
	$page = '<h1>';
	$page .= t('TITLE').' ';
	$page .= '<div id="version" style="display: inline">';
	$page .= plaatenergy_db_get_config_item('database_version');
	$page .= "</div>";
	$page .= '</h1>';

	if ( !file_exists ( "config.inc" )) {
		$page .= '<br/><br/>';
		$page .= t('CONGIG_BAD');
		$page .= '<br/><br/>';
		
	} else {

		$page .= '<div class="home">';
		$page .= '<table>';

		$page .= '<tr>';
		$page .= '<th>'.t('YEARS_REPORT').'</th>';
		$page .= '<th>'.t('YEAR_REPORT').'</th>';
		$page .= '<th>'.t('MONTH_REPORT').'</th>';
		$page .= '<th>'.t('DAY_REPORT').'</th>';

		if ($weather_station_present=="true") { 
		    $page .= '<th>'.t('WEATHER_REPORT').'</th>';
		}                

		$page .= '</tr>';
	
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

		$page .= '<tr>';
		$page .= '<td>';
		$page .= '&nbsp;';
		$page .= '</td>';
		$page .= '</tr>';

		$page .= '<tr>';
		$page .= '<td>';
		$page .= '</td>';
		$page .= '<td>';
		if ($weather_station_present=="false") { 
		   $page .= plaatenergy_link('pid='.PAGE_REPORT, t('LINK_REPORT'));
		}
		$page .= '</td>';
		$page .= '</td>';
		$page .= '<td>';
		
		$settings_password = plaatenergy_db_get_config_item('settings_password');		
		if (strlen($settings_password)>0) {
			$page .= plaatenergy_link('pid='.PAGE_SETTING_LOGIN, t('LINK_SETTINGS')); 
		} else {
			$page .= plaatenergy_link('pid='.PAGE_SETTING_LIST, t('LINK_SETTINGS')); 
		}
		
		$page .= '</td>';
		$page .= '<td>';
		$page .= '</td>';
		$page .= '</tr>';

		$page .= '<tr>';
		$page .= '<td>';
		$page .= plaatenergy_link('pid='.PAGE_ABOUT, t('LINK_ABOUT'));
		$page .= '</td>';
		$page .= '<td>';
		$page .= plaatenergy_link('pid='.PAGE_DONATE, t('LINK_DONATE'));
		$page .= '</td>';
		$page .= '<td>';
		$page .= '<a href="./ui/">'.t('LINK_GUI').'</a>';
		$page .= '</td>';
		$page .= '<td>';
		$page .= plaatenergy_link('pid='.PAGE_RELEASE_NOTES, t('LINK_RELEASE_NOTES'));
		$page .= '</td>';
		
		if ($weather_station_present=="true") { 
			$page .= '<td>';
			$page .= plaatenergy_link('pid='.PAGE_REPORT, t('LINK_REPORT'));
			$page .= '</td>';
		}
		
		$page .= '</tr>';

		$page .= '</table>';
		$page .= '</div>';

		$page .= '<br/><br/>';
	
		$page .= check_energy_meter();
		$page .= check_solar_meter(); 
		$page .= check_weather_station();

		$page .= '<br/><br/>';

		$page .= '<script type="text/javascript">var ip="'.$_SERVER['SERVER_ADDR'].'";</script>';
		$page .= '<script type="text/javascript" src="js/version.js"></script>';
	}
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
