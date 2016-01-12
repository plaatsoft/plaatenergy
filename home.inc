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

function home_page() {

  $page = '<h1>' . t('TITLE') . ' ' . $version . '</h1>';

  $page .= "<div id='version'></div>";

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
  $page .= '<a href="years_in_kwh.php">'.t('LINK_IN_ENERGY').'</a>';
  $page .= '<a href="years_out_kwh.php">'.t('LINK_OUT_ENERGY').'</a>';
  $page .= '<a href="years_in_gas.php">'.t('LINK_IN_GAS').'</a>';
  $page .= '</td>';

  $page .= '<td>';
  $page .= '<a href="year_in_kwh.php">'.t('LINK_IN_ENERGY').'</a>';
  $page .= '<a href="year_out_kwh.php">'.t('LINK_OUT_ENERGY').'</a>';
  $page .= '<a href="year_in_gas.php">'.t('LINK_IN_GAS').'</a>';
  $page .= '</td>';

  $page .= '<td>';
  $page .= '<a href="month_in_kwh.php">'.t('LINK_IN_ENERGY').'</a>';
  $page .= '<a href="month_out_kwh.php">'.t('LINK_OUT_ENERGY').'</a>';
  $page .= '<a href="month_in_gas.php">'.t('LINK_IN_GAS').'</a>';
  $page .= '<a href="month_out_max.php">'.t('LINK_PEAK_OUT_ENERGY').'</a>';
  $page .= '</td>';

  $page .= '<td>';
  $page .= '<a href="day_in_kwh.php">'.t('LINK_IN_ENERGY').'</a>';
  $page .= '<a href="day_out_kwh.php">'.t('LINK_OUT_ENERGY').'</a>';
  $page .= '<a href="day_in_gas.php">'.t('LINK_IN_GAS').'</a>';
  $page .= '</td>';

  $page .= '<td>';
  $page .= '<a href="day_pressure.php">'.t('LINK_PRESSURE').'</a>';
  $page .= '<a href="day_temperature.php">'.t('LINK_TEMPERATURE').'</a>';
  $page .= '<a href="day_huminity.php">'.t('LINK_HUMINITY').'</a>';
  $page .= '</td>';

  $page .= '</tr>';

  $page .= '<tr>';
  $page .= '<td>';
  $page .= '<a href="about.php">'.t('LINK_ABOUT').'</a>';
  $page .= '</td>';
  $page .= '<td>';
  $page .= '<a href="donate.php">'.t('LINK_DONATE').'</a>';
  $page .= '</td>';
  $page .= '<td>';
  $page .= '<a href="./ui/">'.t('LINK_GUI').'</a>';
  $page .= '</td>';
  $page .= '<td>';
  $page .= '<a href="release_notes.php">'.t('LINK_RELEASE_NOTES').'</a>';
  $page .= '</td>';
  $page .= '<td>';
  $page .= '<a href="report.php">'.t('LINK_REPORT').'</a>';
  $page .= '</td>';
  $page .= '</tr>';

  $page .= '</table>';

  $page .= '<br/><br/>';

  check_energy_meter();
  check_solar_meter($solar_meter_ip_address); 
  check_weather_station();

  $page .= '<br/><br/>';

  $page .= '<script type="text/javascript" src="js/version.js"></script>';
  $page .= '<script type="text/javascript">check_version("'.$version.'")</script>';

  return $page;
}

?>
