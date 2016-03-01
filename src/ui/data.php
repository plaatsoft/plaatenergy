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

$temperature = 0;
$pressure = 0;
$humidity = 0;	
if (isset($row1->temperature)) {
   $temperature = $row1->temperature;
	$pressure = $row1->pressure;
	$humidity = $row1->humidity;
}
  
// ---------------------------------------------

$sql2  = 'select vermogen, vermogenterug, gas from energy where ';
$sql2 .= 'timestamp>="'.$timestamp1.'" and timestamp<"'.$timestamp2.'" order by id desc limit 0,1';
$result2 = plaatenergy_db_query($sql2);
$row2 = plaatenergy_db_fetch_object($result2);

$vermogen = 0;
$vermogenterug = 0;
if ( isset($row2->vermogen) ) {
	$vermogen = $row2->vermogen;
	$vermogenterug = $row2->vermogenterug;
}

// ---------------------------------------------

$timestamp1=date('Y-m-d');
$timestamp2=date('Y-m-d');

$sql3  = 'select dal, piek, dalterug, piekterug, solar, gas from energy_day ';
$sql3 .= 'where date>="'.$timestamp1.'" and date<="'.$timestamp2.'"';
$result3 = plaatenergy_db_query($sql3);
$row3 = plaatenergy_db_fetch_object($result3);

$today_energy_used = 0;
$today_energy_delivered = 0;
$today_gas_used = 0;
if ( isset ($row3->dal)) {

	$delivered_low = $row3->dalterug;
	$delivered_normal = $row3->piekterug;
	$tmp = $row3->solar - $delivered_low -$delivered_normal;
	if ($tmp >0 ) {
		$delivered_local=$tmp;
	}
	$today_energy_delivered = $delivered_low + $delivered_normal + $delivered_local;			
	$today_energy_used = $row3->dal + $row3->piek + $delivered_local;
	$today_gas_used = $row3->gas;
}

// ---------------------------------------------

$time=mktime(0, 0, 0, 1, 1, date('Y'));
$timestamp1=date('Y-1-1', $time);
$timestamp2=date('Y-12-t', $time);

$sql5  = 'select sum(dal) as dal, sum(piek) as piek, sum(dalterug) as dalterug, sum(piekterug) as piekterug, sum(solar) as solar, sum(gas) as gas ';
$sql5 .= 'FROM energy_day where date>="'.$timestamp1.'" and date<="'.$timestamp2.'"';
$result5 = plaatenergy_db_query($sql5);
$row5 = plaatenergy_db_fetch_object($result5);

$total_energy_used = 0;
$total_energy_delivered = 0;
$total_gas_used = 0;
if ( isset ($row5->dal)) {

	$delivered_low = $row5->dalterug;
	$delivered_normal = $row5->piekterug;
	$tmp = $row5->solar - $delivered_low -$delivered_normal;
	if ($tmp >0 ) {
		$delivered_local=$tmp;
	}	
	$total_energy_delivered = $delivered_low + $delivered_normal + $delivered_local;		
	$total_energy_used = $row5->dal + $row5->piek + $delivered_local;
	$total_gas_used = $row5->gas;
}

// ---------------------------------------------

// Creating 1 kWh energy results in 0.001 ton CO2 emission.
$total_energy_co2 = round(($total_energy_used - $total_energy_delivered), 2);  

// Burning 1 m3 gas results in 0.00178 ton CO2 emission.
$total_gas_co2 = round(($row5->gas * 1.78), 2);

// Amount of tree needed to offset gas + energy co2 emission
$total_tree_offset = ($total_energy_co2 + $total_gas_co2) / 200;

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
if ($vermogen > 0) {
  $json["current_watt"] = "- ".num($vermogen,0)." Watt";
} else {
  $json["current_watt"] = "+ ".num($vermogenterug,0)." Watt";
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
  $json["gas_today"] = num($today_gas_used) . " m&sup3;";
} elseif ($_GET["q"][1] == "1") {
  $json["total_gas"] = num($total_gas_used * 1000, 0) . " dm&sup3;";
  $json["gas_today"] = num($today_gas_used * 1000, 0) . " dm&sup3;";
}

// Calculate actual temperature in graden celcius = 0 / fahrenheit = 1 / kelvin = 2
if ($_GET["q"][2] == "0") {
  $json["temperature"] = num($temperature) . " &deg;C";
} elseif ($_GET["q"][2] == "1") {
  $json["temperature"] = num($temperature * 9 / 5 + 32) . " &deg;F";
} elseif ($_GET["q"][2] == "2") {
  $json["temperature"] = num($temperature + 273.15) . " K";
}

$json["pressure"] = num($pressure) . " hPa";
$json["humidity"] = num($humidity) . " %";

// Calculate actual energy and gas co2 emission this year in kg
if ($total_energy_co2 > 0) {
  $json["total_energy_co2"] = "+ " .num($total_energy_co2) . " kg";
} else {
  $json["total_energy_co2"] = str_replace("-", "- ", num($total_energy_co2)) . " kg";
}

if ($total_gas_co2 > 0) {
  $json["total_gas_co2"] = "+ " .num($total_gas_co2) . " kg";
} else {
  $json["total_gas_co2"] = str_replace("-", "- ", num($total_gas_co2)) . " kg";
}

// Trees for co2
if ($total_tree_offset > 0) {
  $json["total_tree_offset"] = "+ " .num($total_tree_offset);
} else {
  $json["total_tree_offset"] = str_replace("-", "- ", num($total_tree_offset));
}

echo json_encode($json);

/*
** ---------------------
** THE END
** ---------------------
*/

?>
