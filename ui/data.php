<?php
// Get the data from the database (Willem)
include "../config.inc";
include "../general.inc";

day_parameters();

$conn = new mysqli($servername, $username, $password, $dbname);

// ---------------------------------------------

$timestamp1 = date("Y-m-d 00:00:00", $current_date);
$timestamp2 = date("Y-m-d 23:59:59", $current_date);

$sql1 = 'select temperature,pressure,humidity FROM weather where timestamp>="'.$timestamp1.'" and timestamp<"'.$timestamp2.'" order by id desc limit 0,1';

$result1 = $conn->query($sql1);
$row1 = $result1->fetch_assoc();

// ---------------------------------------------

$sql2 = 'select vermogen, vermogenterug, gas FROM energy where timestamp>="'.$timestamp1.'" and timestamp<"'.$timestamp2.'" order by id desc limit 0,1';
$result2 = $conn->query($sql2);
$row2 = $result2->fetch_assoc();

// ---------------------------------------------

$timestamp1=date('Y-m-d', $current_date);
$timestamp2=date('Y-m-d', $current_date);

$sql = 'select dal, piek, dalterug, piekterug, solar, gas FROM energy_day where date>="'.$timestamp1.'" and date<="'.$timestamp2.'"';
$result = $conn->query($sql);
$row3 = $result->fetch_assoc();

$dal_value= $row3['dal'];
$piek_value = $row3['piek'];
$dalterug_value= $row3['dalterug'];
$piekterug_value = $row3['piekterug'];
$solar_value = $row3['solar'];
$gas_value = $row3['gas'];

$vandaag_verbruikt = $dal_value + $piek_value + ($solar_value-$dalterug_value-$piekterug_value);
$vandaag_opgewekt = $solar_value;

// ---------------------------------------------

$time=mktime(0, 0, 0, 1, 1, date('Y'));
$timestamp1=date('Y-1-1', $time);
$timestamp2=date('Y-12-t', $time);

$sql = 'select sum(dal) as dal, sum(piek) as piek, sum(dalterug) as dalterug, sum(piekterug) as piekterug, sum(solar) as solar, sum(gas) as gas FROM energy_day where date>="'.$timestamp1.'" and date<="'.$timestamp2.'"';
$result = $conn->query($sql);
$row5 = $result->fetch_assoc();

$totaal_verbruikt = $row5['dal'] + $row5['piek'] + ($row5['solar']-$row5['dalterug']-$row5['piekterug']);
$totaal_opgewekt = $row5['solar'];
$totaal_gas = $row5['gas'];

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

// Datum in d:m:Y = 0 / Y:m:d = 1
if ($_GET["q"][1] == "0") {
  $json["date"] = date("Y-m-d");
} elseif ($_GET["q"][1] == "1") {
  $json["date"] = date("d-m-Y");
}

// Tijd in H:i:s = 0 / h:i:s A = 1
if ($_GET["q"][1] == "0") {
  $json["time"] = date("h:i:s A");
} elseif ($_GET["q"][1] == "1") {
  $json["time"] = date("H:i:s");
}

// Bereken de actuele verbruik in Watt
if ($row2["vermogen"] > 0) {
  $json["current_watt"] = "- " . num($row2["vermogen"], 0) . " Watt";
} else {
  $json["current_watt"] = "+ ". num($row2["vermogenterug"], 0) . " Watt";
}

// Bereken het dag verbruik in kWh
$energy_today = $vandaag_opgewekt - $vandaag_verbruikt;
if ($energy_today > 0) {
  $json["energy_today"] = "+ " . num($energy_today) . " kWh";
} else {
  $json["energy_today"] = str_replace("-", "- ", num($energy_today)) . " kWh";
}

$json["total_decrease"] = num($totaal_verbruikt) . " kWh";
$json["total_delivery"] = num($totaal_opgewekt). " kWh";

// Bereken het totale verbruikte gas in m3 = 0 / dm3 = 1
if ($_GET["q"][2] == "0") {
  $json["total_gas"] = num($totaal_gas) . " m&sup3;";
  $json["gas_today"] = num($gas_value) . " m&sup3;";
} elseif ($_GET["q"][2] == "1") {
  $json["total_gas"] = num($totaal_gas * 1000, 0) . " dm&sup3;";
  $json["gas_today"] = num($gas_value * 1000, 0) . " dm&sup3;";
}

// Bereken de temperatuur in graden celcius = 0 / fahrenheit = 1 / kelvin = 2
if ($_GET["q"][3] == "0") {
  $json["temperature"] = num($row1["temperature"]) . " &deg;C";
} elseif ($_GET["q"][3] == "1") {
  $json["temperature"] = num($row1["temperature"] * 9 / 5 + 32) . " &deg;F";
} elseif ($_GET["q"][3] == "2") {
  $json["temperature"] = num($row1["temperature"] + 273.15) . " K";
}

$json["pressure"] = num($row1["pressure"]) . " hPa";
$json["humidity"] = num($row1["humidity"]) . " %";

// Laad de weer informatie alleen als is aangegeven
if ($_GET["q"][5] == 1) {
	$w_data = json_decode(file_get_contents("http://api.openweathermap.org/data/2.5/weather?q=Gouda,nl&appid=2de143494c0b295cca9337e1e96b00e0"));
	
	if ($_GET["q"][3] == "0") {
		$json["w_temperature"] = num($w_data->main->temp - 273.15) . " &deg;C";
	} elseif ($_GET["q"][3] == "1") {
		$json["w_temperature"] = num(($w_data->main->temp - 273.15) * 9 / 5 + 32) . " &deg;F";
	} elseif ($_GET["q"][3] == "2") {
		$json["w_temperature"] = num($w_data->main->temp) . " K";
	}
	
	$json["w_pressure"] = num($w_data->main->pressure) . " hPa";
	$json["w_humidity"] = num($w_data->main->humidity) . " %";
	
	if ($_GET["q"][4] == "0") {
		$json["w_wind_speed"] = num($w_data->wind->speed * 3.6) . " km&#47;h";
	} else {
		$json["w_wind_speed"] = num($w_data->wind->speed) . " m&#47;s";
	}
	
	// Sunset en Sunrise
	if ($_GET["q"][1] == "0") {
		$json["w_sunrise"] = date("h:i:s A", $w_data->sys->sunrise);
		$json["w_sunset"] = date("h:i:s A", $w_data->sys->sunset);
	} elseif ($_GET["q"][1] == "1") {
		$json["w_sunrise"] = date("H:i:s", $w_data->sys->sunrise);
		$json["w_sunset"] = date("H:i:s", $w_data->sys->sunset);
	}
} else {
	// Zeg uit (nl) / off (en)
	if ($_GET["q"][6] == "0") {
		$json["w_temperature"] = "UIT";
		$json["w_pressure"] = "UIT";
		$json["w_humidity"] = "UIT";
		$json["w_wind_speed"] = "UIT";
		$json["w_sunrise"] = "UIT";
		$json["w_sunset"] = "UIT";
	} else {
		$json["w_temperature"] = "OFF";
		$json["w_pressure"] = "OFF";
		$json["w_humidity"] = "OFF";
		$json["w_wind_speed"] = "OFF";
		$json["w_sunrise"] = "OFF";
		$json["w_sunset"] = "OFF";
	}
}


echo json_encode($json);

?>
