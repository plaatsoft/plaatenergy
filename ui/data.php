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
** ---------------------
** Author: wplaat part
** ---------------------
*/

include "../config.inc";
include "../general.inc";
include "../database.inc";

plaatenergy_db_connect($dbhost, $dbuser, $dbpass, $dbname);

// ---------------------------------------------

$timestamp1 = date("Y-m-d 00:00:00");
$timestamp2 = date("Y-m-d 23:59:59");

$sql1  = 'select temperature, pressure, humidity from weather where ';
$sql1 .= 'timestamp>="'.$timestamp1.'" and timestamp<"'.$timestamp2.'" order by id desc limit 0,1';
$result1 = plaatenergy_db_query($sql1);
$row1 = plaatenergy_db_fetch_object($result1);

// ---------------------------------------------

$sql2  = 'select vermogen, vermogenterug, gas from energy where ';
$sql2 .= 'timestamp>="'.$timestamp1.'" and timestamp<"'.$timestamp2.'" order by id desc limit 0,1';
$result2 = plaatenergy_db_query($sql2);
$row2 = plaatenergy_db_fetch_object($result2);

// ---------------------------------------------

$timestamp1=date('Y-m-d');
$timestamp2=date('Y-m-d');

$sql3  = 'select dal, piek, dalterug, piekterug, solar, gas from energy_day ';
$sql3 .= 'where date>="'.$timestamp1.'" and date<="'.$timestamp2.'"';
$result3 = plaatenergy_db_query($sql3);
$row3 = plaatenergy_db_fetch_object($result3);

$dal_value = $row3->dal;
$piek_value = $row3->piek;
$dalterug_value = $row3->dalterug;
$piekterug_value = $row3->piekterug;
$solar_value = $row3->solar;
$gas_value = $row3->gas;

$today_energy_used = $dal_value + $piek_value + ($solar_value - $dalterug_value - $piekterug_value);
$today_energy_delivered = $solar_value;

// ---------------------------------------------

$time=mktime(0, 0, 0, 1, 1, date('Y'));
$timestamp1=date('Y-1-1', $time);
$timestamp2=date('Y-12-t', $time);

$sql5  = 'select sum(dal) as dal, sum(piek) as piek, sum(dalterug) as dalterug, sum(piekterug) as piekterug, sum(solar) as solar, sum(gas) as gas ';
$sql5 .= 'FROM energy_day where date>="'.$timestamp1.'" and date<="'.$timestamp2.'"';
$result5 = plaatenergy_db_query($sql5);
$row5 = plaatenergy_db_fetch_object($result5);

$total_energy_used = $row5->dal + $row5->piek + ($row5->solar-$row5->dalterug-$row5->piekterug);
$total_energy_delivered = $row5->solar;
$total_gas_used = $row5->gas;

// ---------------------------------------------

// Creating 1 kWh energy results in 0.001 ton CO2 emission.
$total_energy_co2 = round((($total_energy_delivered - $total_energy_used) / 1000), 2);  

// Burning 1 m3 gas results in 0.00178 ton CO2 emission.
$total_gas_co2 = round((($row5->gas * 1.78) / 1000), 2);

/*
** ---------------------
** Author: bplaat part
** ---------------------
*/

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

// Set HTML header
header("Content-Type: application/json");
header("Cache-Control: no-cache");
header("Pragma: no-cache");

// Calculate actual energy in Watt
if ($row2->vermogen > 0) {
  $json["current_watt"] = "- ".num($row2->vermogen,0)." Watt";
} else {
  $json["current_watt"] = "+ ".num($row2->vermogenterug,0)." Watt";
}

// Calculate actuel used energy today in kWh 
$energy_today = $today_energy_delivered - $today_energy_used;
if ($energy_today > 0) {
  $json["energy_today"] = "+ " .num($energy_today) . " kWh";
} else {
  $json["energy_today"] = str_replace("-", "- ", num($energy_today)) . " kWh";
}

$json["total_decrease"] = num($total_energy_used) . " kWh";
$json["total_delivery"] = num($total_energy_delivered). " kWh";

// Calculate actual used gas today in m3 = 0 / dm3 = 1
if ($_GET["q"][1] == "0") {
  $json["total_gas"] = num($total_gas_used) . " m&sup3;";
  $json["gas_today"] = num($gas_value) . " m&sup3;";
} elseif ($_GET["q"][1] == "1") {
  $json["total_gas"] = num($total_gas_used * 1000, 0) . " dm&sup3;";
  $json["gas_today"] = num($gas_value * 1000, 0) . " dm&sup3;";
}

// Calculate actual temperature in graden celcius = 0 / fahrenheit = 1 / kelvin = 2
if ($_GET["q"][2] == "0") {
  $json["temperature"] = num($row1->temperature) . " &deg;C";
} elseif ($_GET["q"][2] == "1") {
  $json["temperature"] = num($row1->temperature * 9 / 5 + 32) . " &deg;F";
} elseif ($_GET["q"][2] == "2") {
  $json["temperature"] = num($row1->temperature + 273.15) . " K";
}

$json["pressure"] = num($row1->pressure) . " hPa";
$json["humidity"] = num($row1->humidity) . " %";

// Calculate actual energy and gas co2 emission this year in ton (1000 kg)
$json["total_energy_co2"] = $total_energy_co2 . ' ton';  
$json["total_gas_co2"] = $total_gas_co2. ' ton';  

echo json_encode($json);

/*
** ---------------------
** THE END
** ---------------------
*/

?>
