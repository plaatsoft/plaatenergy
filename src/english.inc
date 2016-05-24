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
 * @brief contain english translation
 */
  
/*
** ------------------
** GENERAL
** ------------------
*/

$lang['TITLE'] = 'PlaatEnergy';
$lang['LINK_COPYRIGHT'] = '<a class="normal_link" href="http://www.plaatsoft.nl/">PlaatSoft</a> 2008-'.date("Y").' - All Copyright Reserved ';
$lang['COOKIE_BAR'] = 'PlaatEnergy uses cookies to save your settings';
$lang['THEME_TO_LIGHT'] = 'Light theme';
$lang['THEME_TO_DARK'] = 'Dark theme';
$lang['ENGLISH'] = 'English';
$lang['DUTCH'] = 'Dutch';

$lang['EURO']                 = 'Euro'; 
$lang['WATT']                 = 'Watt'; 
$lang['KW']                   = 'kW'; 
$lang['KWH']                  = 'kWh';
$lang['MWH']                  = 'MWh';  
$lang['M3']                   = 'm&sup3;'; 
$lang['DATE']                 = 'Date'; 

$lang['USED_LOW_KWH']         = 'Low (kWh)'; 
$lang['USED_HIGH_KWH']        = 'Normal (kWh)'; 
$lang['USED_LOCAL_KWH']       = 'Local (kWh)'; 
$lang['USED_KWH']             = 'Used (kWh)'; 
$lang['DELIVERED_LOW_KWH']    = 'Low (kWh)'; 
$lang['DELIVERED_NORMAL_KWH'] = 'Normal (kWh)'; 
$lang['DELIVERED_LOCAL_KWH']  = 'Local (kWh)'; 
$lang['DELIVERED_KWH']        = 'Delivered (kWh)'; 
$lang['FORECAST_KWH']         = 'Forecast (kWh)'; 
$lang['USED_M3']              = 'Used (m&sup3;)'; 
$lang['FORECAST_M3']          = 'Forecast (m&sup3;)'; 
$lang['PRESSURE']             = 'Pressure (hPa)';
$lang['TEMPERATURE']          = 'Temperature (&deg;C)';
$lang['HUMIDITY']             = 'Humidity (%)';

$lang['DAY_0']           = 'Sun';
$lang['DAY_1']           = 'Mon';
$lang['DAY_2']           = 'Tue';
$lang['DAY_3']           = 'Wed';
$lang['DAY_4']           = 'Thu';
$lang['DAY_5']           = 'Fri';
$lang['DAY_6']           = 'Sat';

/*
** ------------------
** LINKS
** ------------------
*/

$lang['LINK_HOME']          = i('home') . 'Home'; 
$lang['LINK_PREV']          = i('chevron-left') . 'Previous'; 
$lang['LINK_NEXT']          = 'Next' . i('chevron-right'); 
$lang['LINK_PREV_YEAR']     = i('chevron-left') . 'Previous Year'; 
$lang['LINK_PREV_MONTH']    = i('chevron-left') . 'Previous Month'; 
$lang['LINK_PREV_DAY']      = i('chevron-left') . 'Previous Day'; 
$lang['LINK_NEXT_YEAR']     = 'Next Year' . i('chevron-right'); 
$lang['LINK_NEXT_MONTH']    = 'Next Month' . i('chevron-right'); 
$lang['LINK_NEXT_DAY']      = 'Next Day' . i('chevron-right'); 
$lang['LINK_EDIT']          = i('edit') . 'Edit'; 
$lang['LINK_M3']            = i('tint') . 'm&sup3;'; 
$lang['LINK_WATT']          = i('bolt') . 'Watt'; 
$lang['LINK_KWH']           = i('bolt') . 'kWh'; 
$lang['LINK_EURO']          = i('eur') . 'Euro';
$lang['LINK_INSERT']        = i('plus') . 'Insert';  
$lang['LINK_UPDATE']        = i('edit') . 'Update';  
$lang['LINK_EXECUTE']       = i('play') . 'Execute'; 
$lang['LINK_SAVE']          = i('edit') . 'Save'; 
$lang['LINK_CANCEL']        = i('times') . 'Cancel';
$lang['LINK_SETTINGS']      = i('cog') . 'Settings';  
$lang['LINK_MAX']           = i('bolt') . 'Peak';
$lang['LINK_BACKUP']        = i('archive') . 'Export to SQL';
$lang['LINK_EXPORT']        = i('download') . 'Export to CSV';
$lang['LINK_SCATTER']       = 'Scatter';
$lang['LINK_LOGIN']         = 'Login';
$lang['LINK_BACK']          = i('home') . 'Back'; 
$lang['LINK_IN_ENERGY']     = i('bar-chart').'Used Electricity';
$lang['LINK_OUT_ENERGY']    = i('bar-chart').'Delivered Electricity';
$lang['LINK_IN_GAS']        = i('bar-chart').'Used Gas';
$lang['LINK_PRESSURE']      = i('bar-chart').'Air Pressure';
$lang['LINK_TEMPERATURE']   = i('bar-chart').'Air Temperature';
$lang['LINK_HUMIDITY']      = i('bar-chart').'Air Humidity';
$lang['LINK_RELEASE_NOTES'] = i('align-left') . 'Release Notes';
$lang['LINK_ABOUT']         = i('users') . 'About';
$lang['LINK_DONATE']        = i('money') . 'Donate';
$lang['LINK_REPORT']        = i('rocket') . 'Custom Query';
$lang['LINK_GUI']           = i('table') . 'Realtime Dashboard';
$lang['LINK_SYSTEM'] 	    = i('fort-awesome') .'System Overview';
$lang['LINK_IMPORT_EXPORT'] = i('download') .'Export Data'; 
$lang['LINK_DELETE']        = 'Delete'; 
$lang['LINK_WEBCAM']        = i('camera') . 'Webcams'; 
$lang['LINK_HUE']           = i('lightbulb-o') . 'HUE';
$lang['LINK_PICTURE']       = i('camera') . 'Picture'; 
$lang['LINK_ARCHIVE']        = i('folder-open') . 'Archive'; 
 
/*
** ------------------
** HOME
** ------------------
*/

$lang['LABEL_PASSWORD'] = 'Password';

$lang ['CONGIG_BAD' ] = 'The following file "config.inc" is missing in installation directory.<br/><br/>
PlaatEnergy can not  work without!<br/><br/>
Rename config.inc.sample to config.inc, update the database settings en press F5 in your browser!';

$lang['DATABASE_CONNECTION_FAILED' ] = 'The connection to the database failed. Please check if config.inc settings are right!';

$lang['YEARS_REPORT'] = 'Years Reports';
$lang['YEAR_REPORT'] = 'Year Reports';
$lang['MONTH_REPORT'] = 'Month Reports';
$lang['DAY_REPORT'] = 'Day Reports';
$lang['WEATHER_REPORT'] = 'Weather Reports';
$lang['OTHER_REPORT'] = 'Other';

$lang['NO_MEASUREMENT_ERROR'] = 'No measurement received lately!';

$lang['ENERGY_METER_1_CONNECTION_DOWN'] = i('times') . 'Energy meter';
$lang['ENERGY_METER_1_CONNECTION_UP'] = i('check') . 'Energy meter';

$lang['SOLAR_METER_1_CONNECTION_DOWN'] = i('times') . 'Solar Convertor 1';
$lang['SOLAR_METER_1_CONNECTION_UP'] = i('check') . 'Solar Convertor 1';

$lang['SOLAR_METER_2_CONNECTION_DOWN'] = i('times') . 'Solar Convertor 2';
$lang['SOLAR_METER_2_CONNECTION_UP'] = i('check') . 'Solar Convertor 2';

$lang['SOLAR_METER_3_CONNECTION_DOWN'] = i('times') . 'Solar Convertor 3';
$lang['SOLAR_METER_3_CONNECTION_UP'] = i('check') . 'Solar Convertor 3';

$lang['WEATHER_METER_CONNECTION_DOWN'] = i('times') . 'Weather Station';
$lang['WEATHER_METER_CONNECTION_UP'] = i('check') . 'Weather Station';

/*
** ------------------
** YEARS REPORTS
** ------------------
*/

$lang['TITLE_YEARS_IN_KWH'] = 'Used Electricity %s - %s';
$lang['TITLE_YEARS_IN_GAS'] = 'Used Gas %s - %s';
$lang['TITLE_YEARS_OUT_KWH'] = 'Delivered Electricity %s - %s';

$lang['AVERAGE_PER_YEAR_KWH'] = 'Average per year %s kWh [Total = %s kWh]';
$lang['AVERAGE_PER_YEAR_M3'] = 'Average per year %s m&sup3; [Total = %s m&sup3;]';
$lang['AVERAGE_PER_YEAR_EURO'] = 'Average per year %s euro [Total = %s euro]';

/*
** ------------------
** YEAR REPORTS
** ------------------
*/

$lang['TITLE_YEAR_IN_KWH'] = 'Used Electricity - %s';
$lang['TITLE_YEAR_IN_M3'] = 'Used Gas - %s';
$lang['TITLE_YEAR_OUT_KWH'] = 'Delivered Electricity - %s';

$lang['AVERAGE_PER_MONTH_KWH'] = 'Average per month %s kWh [Total = %s kWh]';
$lang['AVERAGE_PER_MONTH_OUT_KWH'] = 'Average per month %s kWh [Total = %s kWh | Efficiency = %s kWh/kWp/day]';
$lang['AVERAGE_PER_MONTH_M3'] = 'Average per month %s m&sup3; [Total = %s m&sup3;]';
$lang['AVERAGE_PER_MONTH_EURO'] = 'Average per month %s euro [Total = %s euro]';

/*
** ------------------
** MONTH REPORTS
** ------------------
*/

$lang['TITLE_MONTH_IN_KWH'] = 'Used Electricity %s-%s';
$lang['TITLE_MONTH_IN_GAS'] = 'Used Gas %s-%s';
$lang['TITLE_MONTH_OUT_KWH'] = 'Delivered Electricity %s-%s';
$lang['TITLE_MONTH_PEAK_OUT_KWH'] = 'Delivered Peak Electricity %s-%s';

$lang['AVERAGE_PER_DAY_KWH'] = 'Average per day %s kWh [Total = %s kWh]';
$lang['AVERAGE_PER_DAY_M3'] = 'Average per day %s m&sup3; [Total = %s m&sup3;]';
$lang['AVERAGE_PER_DAY_EURO'] = 'Average per day %s euro [Total = %s euro]';
$lang['MAX_PEAK_ENERGY'] = 'Maximum peak energy this month is %s.';

/*
** ------------------
** DAY REPORTS
** ------------------
*/

$lang['TITLE_DAY_TEMPERATURE'] = 'Air Temperature - %s %s-%s-%s';
$lang['TITLE_DAY_PRESSURE'] = 'Air Pressure - %s %s-%s-%s';
$lang['TITLE_DAY_HUMIDITY'] = 'Air Humidity - %s %s-%s-%s';

$lang['TITLE_DAY_IN_KWH'] = 'Used Electricity - %s %s-%s-%s';
$lang['TITLE_DAY_IN_GAS'] = 'Used Gas - %s %s-%s-%s';
$lang['TITLE_DAY_OUT_KWH'] = 'Delivered Electricity - %s %s-%s-%s';

$lang['TOTAL_PER_DAY_KWH'] = 'Total today = %s kWh [Forecast = %s kWh]';
$lang['TOTAL_PER_DAY_M3'] = 'Total today = %s m&sup3; [Forecast = %s m&sup3;]';

$lang['MIN_MAX_TEMPERATURE'] = 'Minimum temperature = %s &deg;C | Maximum temperature = %s &deg;C';
$lang['MIN_MAX_HUMIDITY'] = 'Minimum humidity = %s % | Maximum humidity = %s %';
$lang['MIN_MAX_PRESSURE'] = 'Minimum pressure = %s hPa  | Maximum pressure = %s hPa';

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

$lang['LABEL_ETOTAL'] = 'Etotal solar converter %s meter reading value (kWh)';

$lang['LABEL_GAS'] = 'Gas meter value reading (m&sup3)';

$lang['TITLE_QUERY_REPORT'] = 'Custom Query';
$lang['LABEL_START_DATE'] = 'Start date';
$lang['LABEL_END_DATE'] = 'End date';

/*
** ------------------
** ABOUT
** ------------------
*/

$lang['ABOUT_TITLE'] = 'About';
$lang['ABOUT_CONTENT'] = 'PlaatEnergy is created by PlaatSoft.';

$lang['DISCLAIMER_TITLE'] = 'Disclaimer';
$lang['DISCLAIMER_CONTENT'] = 'The program is provided AS IT IS with NO WARRANTY OF ANY KIND,</br>  
INCLUDING THE WARRANTY OF DESIGN, MERCHANTABILITY AND<br/> FITNESS FOR A PARTICULAR PURPOSE.<br/>';

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
$lang['DONATE_CONTENT'] = 'PlaatEnergy software may be used free of charge, <br/>but if you wish to express your appreciation<br/>
for the time and resources the author spent developing and supporting <br/>it over the years, we do accept and appreciate donations.
<br/><br/>
To make a donation online using your credit card, or PayPal account,<br/>click below and enter the amount 
you would like to contribute.<br/>Your credit card will be processed by PayPal, a trusted name in secure online transactions.';

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

$lang['meter_reading_used_low'] = 'Initial energy meter reading - used low (kwh)';
$lang['meter_reading_used_normal'] = 'Initial energy meter reading - used normal (kwh)';
$lang['meter_reading_delivered_low'] = 'Initial energy meter reading - delivered low (kwh)';
$lang['meter_reading_delivered_normal'] = 'Initial energy meter reading - delivered normal (kwh)';
$lang['meter_reading_used_gas'] = 'Initial gas meter reading (m&sup3;)';

$lang['energy_meter_present'] = 'Energy meter present';
$lang['energy_meter_vendor'] = 'Energy meter vendor';
$lang['energy_price'] = 'Energy price per kwh';
$lang['energy_use_forecast'] = 'Energy use annual forecast (kWh)';
$lang['energy_delivery_forecast'] = 'Energy delivery annual forecast (kWh)';

$lang['solar_description'] = 'Solar description (comments field)';
$lang['solar_meter_present'] = 'Solar converter present';
$lang['solar_meter_vendor'] = 'Solar converter vendor';
$lang['solar_meter_ip' ] = 'Solar converter TCP/IP address';
$lang['solar_meter_port'] = 'Solar converter TCP port number';
$lang['solar_meter_serial_number'] = 'Solar converter serial number';
$lang['solar_initial_meter_reading'] = 'Solar converter initial meter reading (kWh)';
$lang['solar_peak_power'] = 'Solar converter peak power (Wp)';

$lang['gas_meter_present' ] = 'Gas meter present';
$lang['gas_price' ] = 'Gas price per m&sup3;';
$lang['gas_use_forecast'] = 'Gas use annual forecast (m&sup3;)';

$lang['weather_station_present'] = 'Weather station present';
$lang['weather_station_vendor'] = 'Weather station vendor';

$lang['slide_show_on'] = 'Activate slide show if user is inactive';
$lang['slide_show_page_delay'] = 'Slide show page delay (seconds)';
$lang['chart_legend'] = 'Chart legend options';
$lang['chart_dimensions'] = 'Chart dimensions in pixels';
$lang['system_name'] = 'System name';

$lang['home_password'] = 'Protect application with a password.';
$lang['settings_password'] = 'Protect settings with a password.';

$lang['webcam_name'] = 'Webcam name';
$lang['webcam_description'] = 'Webcam description';
$lang['webcam_resolution'] = 'Webcam Resolution';
$lang['webcam_present'] = 'Webcam present';
$lang['webcam_device'] = 'Webcam device mapping';

$lang['hue_description'] = 'Philips HUE description';
$lang['hue_ip_address'] = 'Philips HUE IP address of bridge';
$lang['hue_key'] = 'Philips HUE access key';
$lang['hue_present'] = 'Philips HUE present';

$lang['CATEGORY0'] =  'Forecast'; 
$lang['CATEGORY11'] = 'Gas Meter'; 
$lang['CATEGORY21'] = 'Energy Meter'; 
$lang['CATEGORY31'] = 'Solar Converter 1'; 
$lang['CATEGORY32'] = 'Solar Converter 2'; 
$lang['CATEGORY33'] = 'Solar Converter 3'; 
$lang['CATEGORY41'] = 'Weather Meter'; 
$lang['CATEGORY51'] = 'Security'; 
$lang['CATEGORY52'] = 'Look and Feel'; 
$lang['CATEGORY61'] = 'Webcam 1'; 
$lang['CATEGORY62'] = 'Webcam 2'; 
$lang['CATEGORY71'] = 'Philips HUE'; 

/*
** ------------------
** REPORT
** ------------------
*/

$lang['TAG_DATE'] = 'date';
$lang['TAG_LOW_USED'] = 'low_used' ;
$lang['TAG_NORMAL_USED'] = 'normal_used';
$lang['TAG_LOCAL_USED'] = 'local_used';
$lang['TAG_TOTAL_USED'] = 'total_used';

$lang['TAG_GAS_USED'] = 'gas_used';

$lang['TAG_LOW_DELIVERED'] = 'low_delivered';
$lang['TAG_NORMAL_DELIVERED'] = 'normal_delivered';
$lang['TAG_LOCAL_DELIVERED'] = 'local_delivered';
$lang['TAG_TOTAL_DELIVERED'] = 'total_delivered';

/*
** ------------------
** SYSTEM
** ------------------
*/

$lang['SYSTEM_TITLE'] = 'System Overview';

/*
** ------------------
** EXPORT / IMPORT
** ------------------
*/

$lang['TITLE_EXPORT_IMPORT'] ='Export Data';

/*
** ------------------
** WEBCAM
** ------------------
*/

$lang['TITLE_WEBCAM'] ='Webcams';

/*
** ------------------
** HUE
** ------------------
*/

$lang['TITLE_HUE'] ='Philips HUE System';

/*
** ------------------
** THE END
** ------------------
*/

?>
