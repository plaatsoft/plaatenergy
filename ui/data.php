<?php

// Get the data from the database (Willem)
include "../config.inc";
include "../general.inc";
include "../database.inc";

plaatenergy_db_connect($dbhost, $dbuser, $dbpass, $dbname);

day_parameters();

// ---------------------------------------------

$timestamp1 = date("Y-m-d 00:00:00", $current_date);
$timestamp2 = date("Y-m-d 23:59:59", $current_date);

$sql1 = 'select temperature,pressure,humidity FROM weather where timestamp>="'.$timestamp1.'" and timestamp<"'.$timestamp2.'" order by id desc limit 0,1';
$result1 = plaatenergy_db_query($sql1);
$row1 = plaatenergy_db_fetch_object($result1);


// ---------------------------------------------

$sql2 = 'select vermogen, vermogenterug, gas FROM energy where timestamp>="'.$timestamp1.'" and timestamp<"'.$timestamp2.'" order by id desc limit 0,1';
$result2 = plaatenergy_db_query($sql2);
$row2 = plaatenergy_db_fetch_object($result2);

// ---------------------------------------------

$timestamp1=date('Y-m-d', $current_date);
$timestamp2=date('Y-m-d', $current_date);

$sql3 = 'select dal, piek, dalterug, piekterug, solar, gas FROM energy_day where date>="'.$timestamp1.'" and date<="'.$timestamp2.'"';
$result3 = plaatenergy_db_query($sql3);
$row3 = plaatenergy_db_fetch_object($result3);

$dal_value= $row3->dal;
$piek_value = $row3->piek;
$dalterug_value= $row3->dalterug;
$piekterug_value = $row3->piekterug;
$solar_value = $row3->solar;
$gas_value = $row3->gas;

$vandaag_verbruikt = $dal_value + $piek_value + ($solar_value-$dalterug_value-$piekterug_value);
$vandaag_opgewekt = $solar_value;

// ---------------------------------------------

$time=mktime(0, 0, 0, 1, 1, date('Y'));
$timestamp1=date('Y-1-1', $time);
$timestamp2=date('Y-12-t', $time);

$sql5 = 'select sum(dal) as dal, sum(piek) as piek, sum(dalterug) as dalterug, sum(piekterug) as piekterug, sum(solar) as solar, sum(gas) as gas FROM energy_day where date>="'.$timestamp1.'" and date<="'.$timestamp2.'"';
$result5 = plaatenergy_db_query($sql5);
$row5 = plaatenergy_db_fetch_object($result5);

$totaal_verbruikt = $row5->dal + $row5->piek + ($row5->solar-$row5->dalterug-$row5->piekterug);
$totaal_opgewekt = $row5->solar;
$totaal_gas = $row5->gas;

// ===================================================
// ==================== JSON ========================= (Bastiaan)
// ===================================================

// Function om getallen mooi te maken 1.000,4 = 0 / 1,000.4 = 1
if ($_GET["q"][0] == "0") {
  function num ($number, $d = 1) {
    if ($number != 0) {
      return number_format($number, $d);
    } else {
      return 0;
    }
  }
}
elseif ($_GET["q"][0] == "1") {
  function num ($number, $d = 1) {
    if ($number != 0) {
      return str_replace("|", ".", str_replace(".", ",", str_replace(",", "|", number_format($number, $d))));
    } else {
      return 0;
    }
  }
}

// Verander de headers
header("Content-Type: application/json");
header("Cache-Control: no-cache");
header("Pragma: no-cache");

// Bereken de actuele verbruik in Watt
if ($row2->vermogen > 0) {
  $json["current_watt"] = "- ".num($row2->vermogen,0)." Watt";
} else {
  $json["current_watt"] = "+ ".num($row2->vermogenterug,0)." Watt";
}

// Bereken het dag verbruik in kWh
$energy_today = $vandaag_opgewekt - $vandaag_verbruikt;
if ($energy_today > 0) {
  $json["energy_today"] = "+ " .num($energy_today) . " kWh";
} else {
  $json["energy_today"] = str_replace("-", "- ", num($energy_today)) . " kWh";
}

$json["total_decrease"] = num($totaal_verbruikt) . " kWh";
$json["total_delivery"] = num($totaal_opgewekt). " kWh";

// Bereken het totale verbruikte gas in m3 = 0 / dm3 = 1
if ($_GET["q"][1] == "0") {
  $json["total_gas"] = num($totaal_gas) . " m&sup3;";
  $json["gas_today"] = num($gas_value) . " m&sup3;";
} elseif ($_GET["q"][1] == "1") {
  $json["total_gas"] = num($totaal_gas * 1000, 0) . " dm&sup3;";
  $json["gas_today"] = num($gas_value * 1000, 0) . " dm&sup3;";
}

// Bereken de temperatuur in graden celcius = 0 / fahrenheit = 1 / kelvin = 2
if ($_GET["q"][2] == "0") {
  $json["temperature"] = num($row1->temperature) . " &deg;C";
} elseif ($_GET["q"][2] == "1") {
  $json["temperature"] = num($row1->temperature * 9 / 5 + 32) . " &deg;F";
} elseif ($_GET["q"][2] == "2") {
  $json["temperature"] = num($row1->temperature + 273.15) . " K";
}

$json["pressure"] = num($row1->pressure) . " hPa";
$json["humidity"] = num($row1->humidity) . " %";

echo json_encode($json);

?>
