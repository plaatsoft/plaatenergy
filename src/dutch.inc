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
 * @brief contain dutch translation
 */
 
/*
** ------------------
** GENERAL
** ------------------
*/

$lang['TITLE'] = 'PlaatEnergy';

$lang['LINK_COPYRIGHT'] = 'All Copyright Reserved &copy; <a class="normal_link" href="http://www.plaatsoft.nl/">PlaatSoft</a> 2008 - ' . date("Y");

$lang['COOKIE_BAR'] = 'PlaatEnergy gebruikt cookies om jouw instellingen te bewaren';

$lang['THEME_TO_LIGHT'] = 'Licht thema';
$lang['THEME_TO_DARK'] = 'Donker thema';
$lang['ENGLISH'] = 'Engels';
$lang['DUTCH'] = 'Nederlands';

$lang['EURO']            = 'Euro'; 
$lang['WATT']            = 'Watt'; 
$lang['KWH']             = 'kWh';
$lang['M3']              = 'm&sup3;';
$lang['DATE']            = 'Datum';

$lang['USED_LOW_KWH']    = 'Dal (kWh)'; 
$lang['USED_HIGH_KWH']   = 'Normaal (kWh)'; 
$lang['USED_SOLAR_KWH']  = 'Solar (kWh)'; 
$lang['USED_KWH']        = 'Afgenomen (kWh)'; 
$lang['DELIVERED_KWH']   = 'Geleverd (kWh)'; 
$lang['FORECAST_KWH']    = 'Prognose (kWh)'; 
$lang['USED_M3']         = 'Afgenomen (m&sup3;)'; 
$lang['FORECAST_M3']     = 'Prognose (m&sup3;)'; 
$lang['PRESSURE']        = 'Luchtdruk (hPa)';
$lang['TEMPERATURE']     = 'Temperatuur (&deg;C)';
$lang['HUMIDITY']        = 'Luchtvochtigheid (%)';

$lang['DAY_0']           = 'Zo';
$lang['DAY_1']           = 'Ma';
$lang['DAY_2']           = 'Di';
$lang['DAY_3']           = 'Wo';
$lang['DAY_4']           = 'Do';
$lang['DAY_5']           = 'Vr';
$lang['DAY_6']           = 'Za';

/*
** ------------------
** LINKS
** ------------------
*/

$lang['LINK_HOME']       = i('home') . 'Hoofdmenu'; 
$lang['LINK_PREV']       = i('chevron-left') . 'Vorige'; 
$lang['LINK_NEXT']       = 'Volgende' . i('chevron-right');
$lang['LINK_PREV_YEAR']  = i('chevron-left') . 'Vorig Jaar'; 
$lang['LINK_PREV_MONTH'] = i('chevron-left') . 'Vorige Maand'; 
$lang['LINK_PREV_DAY']   = i('chevron-left') . 'Vorige Dag'; 
$lang['LINK_NEXT_YEAR']  = 'Volgend Jaar' . i('chevron-right'); 
$lang['LINK_NEXT_MONTH'] = 'Volgende Maand' . i('chevron-right'); 
$lang['LINK_NEXT_DAY']   = 'Volgende Dag' . i('chevron-right'); 
$lang['LINK_EDIT']       = i('edit') . 'Aanpassen'; 
$lang['LINK_M3']         = i('tint') . 'm&sup3;'; 
$lang['LINK_WATT']       = i('bolt') . 'Watt'; 
$lang['LINK_KWH']        = i('bolt') . 'kWh'; 
$lang['LINK_EURO']       = i('eur') . 'Euro';
$lang['LINK_INSERT']     = i('plus') . 'Toevoegen'; 
$lang['LINK_UPDATE']     = i('edit') . 'Bijwerken'; 
$lang['LINK_EXECUTE']    = i('play') . 'Uitvoeren'; 
$lang['LINK_SAVE']       = i('edit') . 'Opslaan'; 
$lang['LINK_CANCEL']     = i('times') . 'Annuleren'; 
$lang['LINK_SETTINGS']   = i('cog') . 'Configuratie'; 
$lang['LINK_MAX']        = i('bolt') . 'Piek';
$lang['LINK_BACKUP']     = 'Backup';
$lang['LINK_EXPORT']     = 'Export naar CSV';
$lang['LINK_SCATTER']    = 'Scatter';
 
/*
** ------------------
** HOME
** ------------------
*/

$lang ['CONGIG_BAD' ] = 'Het volgende bestand "config.inc" mist in de installatie directory.<br/><br/>
PlaatEnergy werkt niet zonder dit bestand!<br/><br/>
Hernoem config.inc.sample naar config.inc, zet de database instellingen goed en druk op F5 in je browser!';

$lang['YEARS_REPORT'] = 'Jaren Rapportages';
$lang['YEAR_REPORT'] = 'Jaar Rapportages';
$lang['MONTH_REPORT'] = 'Maand Rapportages';
$lang['DAY_REPORT'] = 'Dag Rapportages';
$lang['WEATHER_REPORT'] = 'Weer Rapportages';
$lang['OTHER_REPORT'] = 'Overige';

$lang['LINK_IN_ENERGY'] = 'Afgenomen Elektriciteit';
$lang['LINK_OUT_ENERGY'] = 'Geleverde Elektriciteit';
$lang['LINK_OUT_ENERGY_MAX'] = 'Geleverde Piek Vermogen';
$lang['LINK_IN_GAS'] = 'Afgenomen Gas';

$lang['LINK_PRESSURE'] = 'Luchtdruk';
$lang['LINK_TEMPERATURE'] = 'Lucht Temperatuur';
$lang['LINK_HUMIDITY'] = 'Lucht Vochtigheid';

$lang['LINK_RELEASE_NOTES'] = i('align-left') . 'Release Notes';
$lang['LINK_ABOUT'] = i('users') . 'Over';
$lang['LINK_DONATE'] = i('money') . 'Donatie';
$lang['LINK_REPORT'] = i('archive') . 'Rapportage';
$lang['LINK_GUI'] = i('table') . 'Realtime Dashboard';

$lang['ENERGY_METER_CONNECTION_DOWN'] = i('times') . 'Geen energie meting recentelijk ontvangen';
$lang['ENERGY_METER_CONNECTION_UP'] = i('check') . 'Energie meter verbinding goed';

$lang['SOLAR_METER_CONNECTION_DOWN'] = i('times') . 'Geen solar meting recentelijk ontvangen';
$lang['SOLAR_METER_CONNECTION_UP'] = i('check') . 'Solar meter verbinding goed';

$lang['WEATHER_METER_CONNECTION_DOWN'] = i('times') . 'Geen weer meting recentelijk ontvangen';
$lang['WEATHER_METER_CONNECTION_UP'] = i('check') . 'Weerstation verbinding goed';

/*
** ------------------
** YEARS REPORTS
** ------------------
*/

$lang['TITLE_YEARS_IN_KWH'] = 'Energie Meter - Afgenomen Elektriciteit %s - %s';
$lang['TITLE_YEARS_IN_GAS'] = 'Energie Meter - Afgenomen Gas %s - %s';
$lang['TITLE_YEARS_OUT_KWH'] = 'Solar Meter - Geleverde Elektriciteit %s - %s';

$lang['AVERAGE_PER_YEAR_KWH'] = 'Gemiddeld per jaar %s kWh [Totaal = %s kWh]';
$lang['AVERAGE_PER_YEAR_M3'] = 'Gemiddeld per jaar %s m&sup3; [Totaal = %s m&sup3;]';
$lang['AVERAGE_PER_YEAR_EURO'] = 'Gemiddeld per jaar %s euro [Totaal = %s euro]';

/*
** ------------------
** YEAR REPORTS
** ------------------
*/

$lang['TITLE_YEAR_IN_KWH'] = 'Energie Meter - Afgenomen Elektriciteit - %s';
$lang['TITLE_YEAR_IN_M3'] = 'Energie Meter - Afgenomen Gas - %s';
$lang['TITLE_YEAR_OUT_KWH'] = 'Solar Meter - Geleverde Elektriciteit - %s';

$lang['AVERAGE_PER_MONTH_KWH'] = 'Gemiddeld per maand %s kWh [Totaal = %s kWh]';
$lang['AVERAGE_PER_MONTH_M3'] = 'Gemiddeld per maand %s m&sup3; [Totaal = %s m&sup3;]';
$lang['AVERAGE_PER_MONTH_EURO'] = 'Gemiddeld per maand %s euro [Totaal = %s euro]';

/*
** ------------------
** MONTH REPORTS
** ------------------
*/

$lang['TITLE_MONTH_IN_KWH'] = 'Energie Meter - Afgenomen Elektriciteit %s-%s';
$lang['TITLE_MONTH_IN_GAS'] = 'Energie Meter - Afgenomen Gas %s-%s';
$lang['TITLE_MONTH_OUT_KWH'] = 'Solar Meter - Geleverde Elektriciteit %s-%s';
$lang['TITLE_MONTH_PEAK_OUT_KWH'] = 'Solar Meter - Geleverde Piek vermogen %s-%s';

$lang['AVERAGE_PER_DAY_KWH'] = 'Gemiddeld per dag %s kWh [Totaal = %s kWh]';
$lang['AVERAGE_PER_DAY_M3'] = 'Gemiddeld per dag %s m&sup3; [Totaal = %s m&sup3;]';
$lang['AVERAGE_PER_DAY_EURO'] = 'Gemiddeld per dag %s euro [Totaal = %s euro]';
$lang['MAX_PEAK_ENERGY'] = 'Maximale piek vermogen deze maand is %s Watt.';

/*
** ------------------
** DAY REPORTS
** ------------------
*/

$lang['TITLE_DAY_TEMPERATURE'] = 'Weerstation Meter - Lucht Temperatuur - %s %s-%s-%s';
$lang['TITLE_DAY_PRESSURE'] = 'Weerstation Meter - Luchtdruk - %s %s-%s-%s';
$lang['TITLE_DAY_HUMIDITY'] = 'Weerstation Meter - Lucht Vochtigheid - %s %s-%s-%s';

$lang['TITLE_DAY_IN_KWH'] = 'Energie Meter - Afgenomen Elektriciteit - %s %s-%s-%s';
$lang['TITLE_DAY_IN_GAS'] = 'Energie Meter - Afgenomen Gas - %s %s-%s-%s';
$lang['TITLE_DAY_OUT_KWH'] = 'Solar Meter - Geleverde Elektriciteit - %s %s-%s-%s';

$lang['TOTAL_PER_DAY_KWH'] = 'Totaal vandaag = %s kWh [Prognose = %s kWh]';
$lang['TOTAL_PER_DAY_M3'] = 'Totaal vandaag = %s m&sup3; [Prognose = %s m&sup3;]';

$lang['MIN_MAX_TEMPERATURE'] = 'Minimale temperatuur = %s &deg;C | Maximale temperatuur = %s &deg;C';
$lang['MIN_MAX_HUMIDITY'] = 'Minimale vochtigheid = %s % | Maximale vochtigheid = %s %';
$lang['MIN_MAX_PRESSURE'] = 'Minimale luchtdruk = %s hPa  | Maximale luchtdruk = %s hPa';

/*
** ------------------
** OTHERS
** ------------------
*/

$lang['TITLE_IN_KWH_EDIT'] = 'Energie Meetcorrectie';
$lang['TITLE_OUT_KWH_EDIT'] = 'Solar Meetcorrectie';
$lang['TITLE_IN_GAS_EDIT'] = 'Gas Meetcorrectie';

$lang['LABEL_LOW_USED'] = 'Energie meter dal meterstand (kWh)';
$lang['LABEL_NORMAL_USED'] = 'Energie meter normaal meterstand (kWh)';
$lang['LABEL_LOW_DELIVERED'] = 'Energie meter dal terug  meterstand (kWh)';
$lang['LABEL_NORMAL_DELIVERED'] = 'Energie meter normaal terug meterstand (kWh)';

$lang['LABEL_ETOTAL'] = 'Etotal waarde (kWh)';

$lang['LABEL_GAS'] = 'Gas meter meterstand (m&sup3)';

$lang['TITLE_QUERY_REPORT'] = 'Rapportage';
$lang['LABEL_START_DATE'] = 'Start datum';
$lang['LABEL_END_DATE'] = 'Eind datum';

/*
** ------------------
** ABOUT
** ------------------
*/

$lang['ABOUT_TITLE'] = 'Over';
$lang['ABOUT_CONTENT'] = 'PlaatEnergy is gemaakt door PlaatSoft.';

$lang['DISCLAIMER_TITLE'] = 'Disclaimer';
$lang['DISCLAIMER_CONTENT'] = 'Deze tool wordt zonder enige garantie geleverd.<br/>De auteurs kunnen nergens aansprakelijk voor worden gesteld.<br/>';

$lang['CREDITS_TITLE'] = 'Dankbetuiging';
$lang['CREDITS_CONTENT'] = 'De volgende mensen hebben PlaatEnergy mogelijk gemaakt:<br/><br/>
wplaat (Architect / Ontwikkelaar)</br>
bplaat (Grafisch Ontwerper / Ontwikkelaar)</br>
lplaat (Tester)<br/>';

/*
** ------------------
** DONATE
** ------------------
*/

$lang['DONATE_TITLE'] = 'Donate';
$lang['DONATE_CONTENT'] = 'PlaatEnergy software kan gratis gebruikt worden.<br/>
Als u uw waardering wil uiten voor de tijd en de middelen die de <br/>
auteurs besteed hebben aan de ontwikkeling accepteren wij een donatie.<br/><br/>

U kunt een donatie online overmaken met een creditcard of PayPal-account.<br/>
Klik hiervoor op het onderstaande logo en voer het bedrag in wat u wil doneren.<br/>
Uw transactie zal verwerkt worden door PayPal, een vertrouwde naam<br/>
in de beveiligde online transacties.';

/*
** ------------------
** SETTING
** ------------------
*/

$lang['SETTING_TITLE'] = 'Configuratie';
$lang['LABEL_TOKEN'] = 'Item'; 
$lang['LABEL_VALUE'] = 'Waarde'; 
$lang['LABEL_DESCRIPTION'] = 'Omschrijving'; 

$lang['database_version'] = 'Huidige database versie';
$lang['request_counter'] = 'Pagina request counter';

$lang['energy_meter_reading_low'] = 'Start energie meterstand - laag tarief (kwh)';
$lang['energy_meter_reading_normal'] = 'Start energie meterstand - normaal tarief (kwh)';
$lang['gas_meter_reading'] = 'Start gas meterstand (m&sup3;)';

$lang['energy_price'] = 'Energie prijs per kwh';
$lang['gas_price' ] = 'Gas prijs per m&sup3;';
$lang['energy_use_forecast'] = 'Jaar voorspelling energie verbruik (kWh)';
$lang['energy_delivery_forecast'] = 'Jaar voorspelling energie teruglevering (kWh)';
$lang['gas_use_forecast'] = 'Jaar voorspelling gas verbruik (m&sup3;)';

$lang['energy_meter_present'] = 'Energie meter aanwezig';
$lang['solar_meter_present'] = 'Solar meter aanwezig';
$lang['weather_station_present'] = 'Weerstation aanwezig';

$lang['solar_meter_ip' ] = 'Solar meter TCP/IP adres';
$lang['solar_meter_port'] = 'Solar meter TCP poort nummer';
$lang['solar_meter_serial_number'] = 'Solar meter serial nummer';

$lang['slide_show_on'] = 'Activeert automatisch slide show als gebruiker inactief is';
$lang['slide_show_page_delay'] = 'Slide show pagina vertraging (seconden)';
$lang['chart_legend'] = 'Grafiek legenda option';
$lang['chart_dimensions'] = 'Grafiek grootte in pixels';

/*
** ------------------
** REPORT
** ------------------
*/

$lang['TAG_DATE'] = 'datum' ;
$lang['TAG_LOW_USED'] = 'afgenomen_laag' ;
$lang['TAG_NORMAL_USED'] = 'afgenomen_normaal';
$lang['TAG_SOLAR_USED'] = 'afgenomen_solar';
$lang['TAG_LOW_DELIVERED'] = 'geleverd_laag';
$lang['TAG_NORMAL_DELIVERED'] = 'geleverd_normaal';
$lang['TAG_SOLAR_DELIVERED'] = 'geleverd_solar';
$lang['TAG_GAS_USED'] = 'afgenomen_gas';

/*
** ------------------
** THE END
** ------------------
*/

?>
