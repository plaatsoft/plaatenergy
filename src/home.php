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
** ---------------------------------------------------------------- 
** SUPPORT
** ---------------------------------------------------------------- 
*/

/**
 * Check if solar meter is online.
 * @return HTML block with actual status of solar meter.
 */
function check_solar_meter() {

  $solar_meter_present = plaatenergy_db_get_config_item('solar_meter_present');

  if ($solar_meter_present=="false") {
  
   $page  = '<div class="checker disabled">';
   $page .= t('SOLAR_METER_DISABLED');		
   $page .= '</div>';
	
  } else {

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
  
   $energy_meter_present = plaatenergy_db_get_config_item('energy_meter_present');
	
	if ($energy_meter_present=="false") {
  
		$page  = '<div class="checker disabled">';
		$page .= t('ENERGY_METER_DISABLED');		
		$page .= '</div>';

	} else {
	   
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

   $weather_station_present = plaatenergy_db_get_config_item('weather_station_present');
    
   if ($weather_station_present=="false") {
  
		$page  = '<div class="checker disabled">';
		$page .= t('WEATHER_METER_DISABLED');	
		$page .= '</div>';
	
	} else {
	
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
** ---------------------------------------------------------------- 
** PAGE
** ---------------------------------------------------------------- 
*/

/**
 * Home Page
 * @return HTML block which contain home page.
 */
function plaatenergy_home_page() {

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
		$page .= '<th>'.t('WEATHER_REPORT').'</th>';
		$page .= '</tr>';
	
		$page .= '<tr>';
		
		$page .= '<td>';
		$page .= plaatenergy_link('pid='.PAGE_YEARS_IN_ENERGY.'&eid='.EVENT_KWH, t('LINK_IN_ENERGY'));
		$page .= plaatenergy_link('pid='.PAGE_YEARS_OUT_ENERGY.'&eid='.EVENT_KWH, t('LINK_OUT_ENERGY'));
		$page .= plaatenergy_link('pid='.PAGE_YEARS_IN_GAS.'&eid='.EVENT_M3, t('LINK_IN_GAS'));
		$page .= '</td>';
		
		$page .= '<td>';
		$page .= plaatenergy_link('pid='.PAGE_YEAR_IN_ENERGY.'&eid='.EVENT_KWH, t('LINK_IN_ENERGY'));
		$page .= plaatenergy_link('pid='.PAGE_YEAR_OUT_ENERGY.'&eid='.EVENT_KWH, t('LINK_OUT_ENERGY'));
		$page .= plaatenergy_link('pid='.PAGE_YEAR_IN_GAS.'&eid='.EVENT_M3, t('LINK_IN_GAS'));
		$page .= '</td>';

		$page .= '<td>';
		$page .= plaatenergy_link('pid='.PAGE_MONTH_IN_ENERGY.'&eid='.EVENT_KWH, t('LINK_IN_ENERGY'));
		$page .= plaatenergy_link('pid='.PAGE_MONTH_OUT_ENERGY.'&eid='.EVENT_KWH, t('LINK_OUT_ENERGY'));
		$page .= plaatenergy_link('pid='.PAGE_MONTH_IN_GAS.'&eid='.EVENT_M3, t('LINK_IN_GAS'));
		$page .= plaatenergy_link('pid='.PAGE_SETTING_LIST, t('LINK_SETTINGS')); 
		$page .= '</td>';

		$page .= '<td>';
		$page .= plaatenergy_link('pid='.PAGE_DAY_IN_ENERGY.'&eid='.EVENT_KWH, t('LINK_IN_ENERGY'));
		$page .= plaatenergy_link('pid='.PAGE_DAY_OUT_ENERGY.'&eid='.EVENT_KWH, t('LINK_OUT_ENERGY'));
		$page .= plaatenergy_link('pid='.PAGE_DAY_IN_GAS.'&eid='.EVENT_M3, t('LINK_IN_GAS'));
		$page .= '</td>';

		$page .= '<td>';
		$page .= plaatenergy_link('pid='.PAGE_DAY_PRESSURE, t('LINK_PRESSURE'));
		$page .= plaatenergy_link('pid='.PAGE_DAY_TEMPERATURE, t('LINK_TEMPERATURE'));
		$page .= plaatenergy_link('pid='.PAGE_DAY_HUMIDITY, t('LINK_HUMIDITY'));
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
		$page .= '<td>';
		$page .= plaatenergy_link('pid='.PAGE_REPORT, t('LINK_REPORT'));
		$page .= '</td>';
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
		
	/* Page handler */
	switch ($pid) {
		
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
