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

general_header();

plaatenergy_db_connect($dbhost, $dbuser, $dbpass, $dbname);
plaatenergy_db_check_version($version);

$solar_meter_ip_address = plaatenergy_db_get_config_item('solar_meter_ip_address');

echo '<h1>';
echo '<img src="./ui/images/icons/32.png">';
echo t('TITLE').' '.$version;
echo '<img src="./ui/images/icons/32.png">';
echo '</h1>';

echo "<div id='version'></div>";

echo '<table>';

echo '<tr>';
echo '<th>'.t('YEARS_REPORT').'</th>';
echo '<th>'.t('YEAR_REPORT').'</th>';
echo '<th>'.t('MONTH_REPORT').'</th>';
echo '<th>'.t('DAY_REPORT').'</th>';
echo '<th>'.t('WEATHER_REPORT').'</th>';
echo '</tr>';

echo '<tr>';

echo '<td>';
echo '<a href="years_in_kwh.php">'.t('LINK_IN_ENERGY').'</a>';
echo '<a href="years_out_kwh.php">'.t('LINK_OUT_ENERGY').'</a>';
echo '<a href="years_in_gas.php">'.t('LINK_IN_GAS').'</a>';
echo '</td>';

echo '<td>';
echo '<a href="year_in_kwh.php">'.t('LINK_IN_ENERGY').'</a>';
echo '<a href="year_out_kwh.php">'.t('LINK_OUT_ENERGY').'</a>';
echo '<a href="year_in_gas.php">'.t('LINK_IN_GAS').'</a>';
echo '</td>';

echo '<td>';
echo '<a href="month_in_kwh.php">'.t('LINK_IN_ENERGY').'</a>';
echo '<a href="month_out_kwh.php">'.t('LINK_OUT_ENERGY').'</a>';
echo '<a href="month_in_gas.php">'.t('LINK_IN_GAS').'</a>';
echo '<a href="month_out_max.php">'.t('LINK_PEAK_OUT_ENERGY').'</a>';
echo '</td>';

echo '<td>';
echo '<a href="day_in_kwh.php">'.t('LINK_IN_ENERGY').'</a>';
echo '<a href="day_out_kwh.php">'.t('LINK_OUT_ENERGY').'</a>';
echo '<a href="day_in_gas.php">'.t('LINK_IN_GAS').'</a>';
echo '</td>';

echo '<td>';
echo '<a href="day_pressure.php">'.t('LINK_PRESSURE').'</a>';
echo '<a href="day_temperature.php">'.t('LINK_TEMPERATURE').'</a>';
echo '<a href="day_huminity.php">'.t('LINK_HUMINITY').'</a>';
echo '</td>';

echo '</tr>';

echo '<tr>';
echo '<td>';
echo '<a href="about.php">'.t('LINK_ABOUT').'</a>';
echo '</td>';
echo '<td>';
echo '<a href="donate.php">'.t('LINK_DONATE').'</a>';
echo '</td>';
echo '<td>';
echo '<a href="./ui/">'.t('LINK_GUI').'</a>';
echo '</td>';
echo '<td>';
echo '<a href="release_notes.php">'.t('LINK_RELEASE_NOTES').'</a>';
echo '</td>';
echo '<td>';
echo '<a href="report.php">'.t('LINK_REPORT').'</a>';
echo '</td>';
echo '</tr>';

echo '</table>';

echo '<br/><br/>';

check_energy_meter();
check_solar_meter($solar_meter_ip_address); 
check_weather_station();

echo '<br/><br/>';

echo '<script type="text/javascript" src="js/version.js"></script>';
echo '<script type="text/javascript">check_version("'.$version.'")</script>';
general_footer();

?>
