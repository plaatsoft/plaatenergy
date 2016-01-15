<?php

// Verander de headers
header("Content-Type: application/json");
header("Cache-Control: no-cache");
header("Pragma: no-cache");

// =================================================
// ==================== ENGLISH ====================
// =================================================
$lang["en"]["tiles_date"] = "Date";
$lang["en"]["tiles_time"] = "Time";

$lang["en"]["tiles_temperature"] = "Temperature inside";
$lang["en"]["tiles_humidity"] = "Air humidity inside";
$lang["en"]["tiles_pressure"] = "Air peressure inside";

$lang["en"]["tiles_w_temperature"] = "Temperature outside";
$lang["en"]["tiles_w_humidity"] = "Air humidity outside";
$lang["en"]["tiles_w_pressure"] = "Air peressure outside";
$lang["en"]["tiles_w_wind"] = "Wind speed outside";

$lang["en"]["tiles_gas_today"] = "Gas today";
$lang["en"]["tiles_total_gas"] = "Total consumption gas";
$lang["en"]["tiles_energy_today"] = "Electricity today";
$lang["en"]["tiles_current_watt"] = "Electricity now";
$lang["en"]["tiles_total_decrease"] = "Total decrease electricity";
$lang["en"]["tiles_total_delivery"] = "Total delivery electricity";

$lang["en"]["tiles_exit"] = "Exit";
$lang["en"]["tiles_settings"] = "Settings";

$lang["en"]["sidebars_settings_header"] = "Settings";
$lang["en"]["sidebars_settings_header_more"] = "Format / unit settings";
$lang["en"]["sidebars_settings_more"] = "Format / unit settings";
$lang["en"]["sidebars_settings_less"] = "Back to settings";
$lang["en"]["sidebars_settings_language"] = "Language";
$lang["en"]["sidebars_settings_temperature"] = "Temperature unit";
$lang["en"]["sidebars_settings_gas"] = "Gas unit";
$lang["en"]["sidebars_settings_gas_0"] = "Cubic meters (m&sup3;)";
$lang["en"]["sidebars_settings_gas_1"] = "Cubic decimeters (dm&sup3;)";
$lang["en"]["sidebars_settings_time"] = "Date and Time format";
$lang["en"]["sidebars_settings_time_0"] = "British format";
$lang["en"]["sidebars_settings_time_1"] = "Dutch format";
$lang["en"]["sidebars_settings_numbers"] = "Numbers format";
$lang["en"]["sidebars_settings_numbers_0"] = "British format";
$lang["en"]["sidebars_settings_numbers_1"] = "Dutch format";
$lang["en"]["sidebars_settings_weather"] = "Load weather information";
$lang["en"]["sidebars_settings_sunrise"] = "Show sunrise and sunset time";
$lang["en"]["sidebars_settings_refresh"] = "Information refresh time";
$lang["en"]["sidebars_settings_wind"] = "Wind speed unit";
$lang["en"]["sidebars_settings_wind_0"] = "Kilometers per hour (km/h)";
$lang["en"]["sidebars_settings_wind_1"] = "Meters per second (m/s)";
$lang["en"]["sidebars_settings_background"] = "Background image";
$lang["en"]["sidebars_settings_background_upload"] = "Upload an image";
$lang["en"]["sidebars_settings_anim"] = "Disable animations";

$lang["en"]["popup_cookies_h"] = "Cookies";
$lang["en"]["popup_cookies_p"] = "We use cookies to save your settings. We don't use cookies to gather information about you.";

// =================================================
// =================== Nederlands ==================
// =================================================
$lang["nl"]["tiles_date"] = "Datum";
$lang["nl"]["tiles_time"] = "Tijd";

$lang["nl"]["tiles_temperature"] = "Temperatuur binnen";
$lang["nl"]["tiles_humidity"] = "Luchtvochtigheid binnen";
$lang["nl"]["tiles_pressure"] = "Luchtdruk binnen";

$lang["nl"]["tiles_w_temperature"] = "Temperatuur buiten";
$lang["nl"]["tiles_w_humidity"] = "Luchtvochtigheid buiten";
$lang["nl"]["tiles_w_pressure"] = "Luchtdruk buiten";
$lang["nl"]["tiles_w_wind"] = "Wind snelheid buiten";

$lang["nl"]["tiles_gas_today"] = "Gas vandaag";
$lang["nl"]["tiles_total_gas"] = "Totaal verbruik gas";
$lang["nl"]["tiles_energy_today"] = "Elektriciteit vandaag";
$lang["nl"]["tiles_current_watt"] = "Elektriciteit nu";
$lang["nl"]["tiles_total_decrease"] = "Totaal afname elektriciteit";
$lang["nl"]["tiles_total_delivery"] = "Totaal levering elektriciteit";

$lang["nl"]["tiles_exit"] = "Afsluiten";
$lang["nl"]["tiles_settings"] = "Instellingen";

$lang["nl"]["sidebars_settings_header"] = "Instellingen";
$lang["nl"]["sidebars_settings_header_more"] = "Notatie / eenheid instellingen";
$lang["nl"]["sidebars_settings_more"] = "Notatie / eenheid instellingen";
$lang["nl"]["sidebars_settings_less"] = "Terug naar instellingen";
$lang["nl"]["sidebars_settings_language"] = "Taal";
$lang["nl"]["sidebars_settings_temperature"] = "Temperatuur eenheid";
$lang["nl"]["sidebars_settings_gas"] = "Gas eenheid";
$lang["nl"]["sidebars_settings_gas_0"] = "Kubieke meters (m&sup3;)";
$lang["nl"]["sidebars_settings_gas_1"] = "Kubieke decimeters (dm&sup3;)";
$lang["nl"]["sidebars_settings_time"] = "Datum en Tijd notatie";
$lang["nl"]["sidebars_settings_time_0"] = "Engelse notatie";
$lang["nl"]["sidebars_settings_time_1"] = "Nederlandse notatie";
$lang["nl"]["sidebars_settings_numbers"] = "Getallen notatie";
$lang["nl"]["sidebars_settings_numbers_0"] = "Engelse notatie";
$lang["nl"]["sidebars_settings_numbers_1"] = "Nederlandse notatie";
$lang["nl"]["sidebars_settings_weather"] = "Haal weer informatie op";
$lang["nl"]["sidebars_settings_sunrise"] = "Laat zons- op en ondergang tijd zien";
$lang["nl"]["sidebars_settings_refresh"] = "Informatie ververs tijd";
$lang["nl"]["sidebars_settings_wind"] = "Windsnelheid eenheid";
$lang["nl"]["sidebars_settings_wind_0"] = "Kilometers per uur (km/h)";
$lang["nl"]["sidebars_settings_wind_1"] = "Meters per seconden (m/s)";
$lang["nl"]["sidebars_settings_background"] = "Achtergrond plaatje";
$lang["nl"]["sidebars_settings_background_upload"] = "Upload een plaatje";
$lang["nl"]["sidebars_settings_anim"] = "Stop animaties";

$lang["nl"]["popup_cookies_h"] = "Cookies";
$lang["nl"]["popup_cookies_p"] = "Wij gebruiken cookies om jouw instellingen te bewaren. We gebruiken cookies niet om informatie over jouw te verzamelen.";

$json = $lang[$_GET["q"]];
echo json_encode($json);

?>
