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
 
/*
** ------------------
** GENERAL
** ------------------
*/

$lang['TITLE'] = 'PlaatEnergy';

$lang['LINK_COPYRIGHT'] = 'All Copyright Reserved (c) <a class="normal_link" href="http://www.plaatsoft.nl/">PlaatSoft</a> 2008 - ' . date("Y");

$lang['COOKIE_BAR'] = 'PlaatEnergy uses cookies to save your settings';

$lang['THEME_TO_LIGHT'] = 'Light theme';
$lang['THEME_TO_DARK'] = 'Dark theme';
$lang['ENGLISH'] = 'English';
$lang['DUTCH'] = 'Dutch';

$lang['EURO']            = 'Euro'; 
$lang['USED_LOW_KWH']    = 'Low (kWh)'; 
$lang['USED_HIGH_KWH']   = 'High (kWh)'; 
$lang['USED_SOLAR_KWH']  = 'Solar (kWh)'; 
$lang['DELIVERED_KWH']   = 'Delivered (kWh)'; 
$lang['FORECAST_KWH']    = 'Forecast (kWh)'; 
$lang['USED_M3']         = 'Used (m&sup3;)'; 
$lang['FORECAST_M3']     = 'Forecast (m&sup3;)'; 
$lang['PRESSURE']        = 'Pressure (hPa)';
$lang['TEMPERATURE']     = 'Temperature (&deg;C)';
$lang['HUMIDITY']        = 'Humidity (%)';

/*
** ------------------
** LINKS
** ------------------
*/

$lang['LINK_HOME']       = i('home') . 'Home'; 
$lang['LINK_PREV']       = i('chevron-left') . 'Previous'; 
$lang['LINK_NEXT']       = 'Next' . i('chevron-right'); 
$lang['LINK_PREV_YEAR']  = i('chevron-left') . 'Previous Year'; 
$lang['LINK_PREV_MONTH'] = i('chevron-left') . 'Previous Month'; 
$lang['LINK_PREV_DAY']   = i('chevron-left') . 'Previous Day'; 
$lang['LINK_NEXT_YEAR']  = 'Next Year' . i('chevron-right'); 
$lang['LINK_NEXT_MONTH'] = 'Next Month' . i('chevron-right'); 
$lang['LINK_NEXT_DAY']   = 'Next Day' . i('chevron-right'); 
$lang['LINK_EDIT']       = i('edit') . 'Edit'; 
$lang['LINK_M3']         = i('tint') . 'm&sup3;'; 
$lang['LINK_WATT']       = i('bolt') . 'Watt'; 
$lang['LINK_KWH']        = i('bolt') . 'kWh'; 
$lang['LINK_EURO']       = i('eur') . 'Euro';
$lang['LINK_INSERT']     = 'Insert';  
$lang['LINK_UPDATE']     = 'Update';  
$lang['LINK_EXECUTE']    = 'Execute'; 
$lang['LINK_SAVE']       = 'Save'; 
$lang['LINK_CANCEL']     = 'Cancel';
$lang['LINK_SETTINGS']   = 'Settings';  
$lang['LINK_MAX']        = 'Peak';
 
/*
** ------------------
** HOME
** ------------------
*/

$lang ['CONGIG_BAD' ] = 'The following file "config.inc" is missing in installation directory.<br/><br/>
PlaatEnergy can not  work without!<br/><br/>
Rename config.inc.sample to config.inc, update the database settings en press F5 in your browser!';

$lang['YEARS_REPORT'] = 'Years Reports';
$lang['YEAR_REPORT'] = 'Year Reports';
$lang['MONTH_REPORT'] = 'Month Reports';
$lang['DAY_REPORT'] = 'Day Reports';
$lang['WEATHER_REPORT'] = 'Weather Reports';
$lang['OTHER_REPORT'] = 'Other';

$lang['LINK_IN_ENERGY'] = 'Used Electricity';
$lang['LINK_OUT_ENERGY'] = 'Delivered Electricity';
$lang['LINK_OUT_ENERGY_MAX'] = 'Peak Delivered Electricity';
$lang['LINK_IN_GAS'] = 'Used Gas';

$lang['LINK_PRESSURE'] = 'Air Pressure';
$lang['LINK_TEMPERATURE'] = 'Air Temperature';
$lang['LINK_HUMIDITY'] = 'Air Humidity';

$lang['LINK_RELEASE_NOTES'] = i('align-left') . 'Release Notes';
$lang['LINK_ABOUT'] = i('users') . 'About';
$lang['LINK_DONATE'] = i('money') . 'Donate';
$lang['LINK_REPORT'] = i('archive') . 'Query';
$lang['LINK_GUI'] = i('table') . 'Realtime Info';

$lang['ENERGY_METER_DISABLED'] = i('exclamation') . 'Energy meter is disabled';
$lang['ENERGY_METER_CONNECTION_DOWN'] = i('times') . 'No energy data recent received';
$lang['ENERGY_METER_CONNECTION_UP'] = i('check') . 'Energy meter connection is up';

$lang['SOLAR_METER_DISABLED'] = i('exclamation') . 'Solar meter is disabled';
$lang['SOLAR_METER_CONNECTION_DOWN'] = i('times') . 'No solar data recent received';
$lang['SOLAR_METER_CONNECTION_UP'] = i('check') . 'Solar meter connection is up';

$lang['WEATHER_METER_DISABLED'] = i('exclamation') . 'Weather Station is disabled';
$lang['WEATHER_METER_CONNECTION_DOWN'] = i('times') . 'No weather data recent received';
$lang['WEATHER_METER_CONNECTION_UP'] = i('check') . 'Weather Station connection is up';

/*
** ------------------
** YEARS REPORTS
** ------------------
*/

$lang['TITLE_YEARS_IN_KWH'] = 'Energy Meter - Used Electricity %s - %s';
$lang['TITLE_YEARS_IN_GAS'] = 'Gas Meter - Used Gas %s - %s';
$lang['TITLE_YEARS_OUT_KWH'] = 'Solar Meter - Delivered Electricity %s - %s';

$lang['AVERAGE_PER_YEAR_KWH'] = 'Average per year %s kWh [Total = %s kWh]';
$lang['AVERAGE_PER_YEAR_M3'] = 'Average per year %s m&sup3; [Total = %s m&sup3;]';
$lang['AVERAGE_PER_YEAR_EURO'] = 'Average per year %s euro [Total = %s euro]';

/*
** ------------------
** YEAR REPORTS
** ------------------
*/

$lang['TITLE_YEAR_IN_KWH'] = 'Energy Meter - Used Electricity %s';
$lang['TITLE_YEAR_IN_M3'] = 'Gas Meter - Used Gas %s';
$lang['TITLE_YEAR_OUT_KWH'] = 'Solar Meter - Delivered Electricity %s';

$lang['AVERAGE_PER_MONTH_KWH'] = 'Average per month %s kWh [Total = %s kWh]';
$lang['AVERAGE_PER_MONTH_M3'] = 'Average per month %s m&sup3; [Total = %s m&sup3;]';
$lang['AVERAGE_PER_MONTH_EURO'] = 'Average per month %s euro [Total = %s euro]';

/*
** ------------------
** MONTH REPORTS
** ------------------
*/

$lang['TITLE_MONTH_IN_KWH'] = 'Energy Meter - Used Electricity %s-%s';
$lang['TITLE_MONTH_IN_GAS'] = 'Gas Meter - Used Gas %s-%s';
$lang['TITLE_MONTH_OUT_KWH'] = 'Solar Meter - Delivered Electricity %s-%s';
$lang['TITLE_MONTH_PEAK_OUT_KWH'] = 'Solar Meter - Delivered Peak Electricity %s-%s';

$lang['AVERAGE_PER_DAY_KWH'] = 'Average per day %s kWh [Total = %s kWh]';
$lang['AVERAGE_PER_DAY_M3'] = 'Average per day %s m&sup3; [Total = %s m&sup3;]';
$lang['AVERAGE_PER_DAY_EURO'] = 'Average per day %s euro [Total = %s euro]';
$lang['MAX_PEAK_ENERGY'] = 'Maximum peak energy this month is %s Watt.';

/*
** ------------------
** DAY REPORTS
** ------------------
*/

$lang['TITLE_DAY_TEMPERATURE'] = 'Weather Station - Air Temperature - %s-%s-%s';
$lang['TITLE_DAY_PRESSURE'] = 'Weather Station - Air Pressure - %s-%s-%s';
$lang['TITLE_DAY_HUMIDITY'] = 'Weather Station - Air Humidity - %s-%s-%s';

$lang['TITLE_DAY_IN_KWH'] = 'Energy Meter - Used Electricity %s-%s-%s';
$lang['TITLE_DAY_IN_GAS'] = 'Gas Meter - Used Gas %s-%s-%s';
$lang['TITLE_DAY_OUT_KWH'] = 'Solar Meter - Delivered Electricity %s-%s-%s';

$lang['TOTAL_PER_DAY_KWH'] = 'Total = %s kWh';
$lang['TOTAL_PER_DAY_M3'] = 'Total = %s m&sup3;';

/*
** ------------------
** OTHERS
** ------------------
*/

$lang['TITLE_IN_KWH_EDIT'] = 'Energy Measurement Correction';
$lang['TITLE_OUT_KWH_EDIT'] = 'Solar Measurement Correction';
$lang['TITLE_IN_GAS_EDIT'] = 'Gas Measurement Correction';

$lang['LABEL_LOW_USED'] = 'Low used meter value reading (kWh)';
$lang['LABEL_NORMAL_USED'] = 'Normal used meter value reading (kWh)';
$lang['LABEL_LOW_DELIVERED'] = 'Low delivered meter value reading (kWh)';
$lang['LABEL_NORMAL_DELIVERED'] = 'Normal delivered meter value reading (kWh)';

$lang['LABEL_ETOTAL'] = 'Etotal meter reading value (kWh)';

$lang['LABEL_GAS'] = 'Gas meter value reading (m&sup3)';

$lang['RECORD_SAVED'] = 'Measurement correction stored!';

$lang['TITLE_QUERY_REPORT'] = 'Query Energy Database';
$lang['LABEL_START_DATE'] = 'Start date (format yyyy-mm-dd)';
$lang['LABEL_END_DATE'] = 'End date (format yyyy-mm-dd)';

/*
** ------------------
** ABOUT
** ------------------
*/

$lang['ABOUT_TITLE'] = 'About';
$lang['ABOUT_CONTENT'] = 'PlaatEnergy is created by PlaatSoft.';

$lang['DISCLAIMER_TITLE'] = 'Disclaimer';
$lang['DISCLAIMER_CONTENT'] = 'The program is provided AS IT IS with NO WARRANTY OF ANY KIND, INCLUDING THE WARRANTY OF 
DESIGN, MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE.<br/>';

$lang['CREDITS_TITLE'] = 'Credits';
$lang['CREDITS_CONTENT'] = 'The following people helped creating PlaatEnergy:<br/><br/>
wplaat (Architect / Developer)<br/>
bplaat (Graphical Designer / Developer)<br/>
lplaat (Tester)';

/*
** ------------------
** DONATE
** ------------------
*/

$lang['DONATE_TITLE'] = 'Donate';
$lang['DONATE_CONTENT'] = 'PlaatEnergy software may be used free of charge, but if you wish to express your appreciation 
for the time and resources the author spent developing and supporting it over the years, we do accept and appreciate donations.
<br/><br/>
To make a donation online using your credit card, or PayPal account, click below and enter the amount 
you would like to contribute. Your credit card will be processed by PayPal, a trusted name in secure online transactions.';

/*
** ------------------
** SETTING
** ------------------
*/

$lang['SETTING_TITLE'] = 'Settings';

$lang['LABEL_TOKEN'] = 'Key'; 
$lang['LABEL_VALUE'] = 'Value'; 
$lang['LABEL_DESCRIPTION'] = 'Description'; 

$lang['database_version'] = 'Current database version';
$lang['request_counter'] = 'Page request counter';

$lang['energy_meter_reading_low'] = 'Energy meter reading - low tariff (kwh)';
$lang['energy_meter_reading_normal'] = 'Energy meter reading - normal tariff (kwh)';
$lang['gas_meter_reading'] = 'Gas meter reading (m3)';

$lang['energy_price'] = 'Energy price per kwh';
$lang['gas_price' ] = 'Gas price per m3';
$lang['energy_use_forecast'] = 'Energy use forecast (kWh)';
$lang['energy_delivery_forecast'] = 'Energy delivery forecast (kWh)';
$lang['gas_use_forecast'] = 'Gas use forecast (m3)';

$lang['energy_meter_present'] = 'Energy meter present ("true" or "false")';
$lang['solar_meter_present'] = 'Solar meter present ("true" or "false")';
$lang['weather_station_present'] = 'Weather Station present ("true" or "false")';

$lang['solar_meter_ip' ] = 'Solar meter TCP/IP address';
$lang['solar_meter_port'] = 'Solar meter TCP port number';
$lang['solar_meter_serial_number'] = 'Solar meter serial number';

/*
** ------------------
** THE END
** ------------------
*/

?>
