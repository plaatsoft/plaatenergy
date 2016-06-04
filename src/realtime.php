<?php

// This whole page is made by Bastiaan van der Plaat

// When you send a get message to realtime.php you get the db info
if ($_SERVER["REQUEST_METHOD"] == "GET") {
  header("Content-Type: application/json"); // Set file mimetype to json
  header("Cache-Control: no-cache");        // Tels browser to don't cache this file

  // Load database username and password
  include "config.inc";

  // Connect to the database
  $db = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

  // Array/object where response data stored
  $json = [];

  // Function to get value of a db config item
  function config ($token) {
    global $db;
    return $db->query("SELECT value FROM config WHERE token='" . $token . "'")->fetch_object()->value;
  }

  // ============================================
  // ============== HUE LIGHT SCRIPTS ===========
  // ============================================

  // Get hue db config information
  $hue_ip = config("hue_ip_address");
  $hue_key = config("hue_key");
  $hue_url = "http://" . $hue_ip . "/api/" . $hue_key . "/lights";

  // When get lights is true give all lights information
  if (isset($_GET["lights"])) {
    foreach (json_decode(file_get_contents($hue_url)) as $id => $light) {
      if ($light->state->reachable) array_push($json, ["id" => (int)$id, "name" => $light->name, "on" => $light->state->on, "bri" => $light->state->bri]);
    }

    // Return json and exit
    echo json_encode($json);
    exit;
  }

  // When light, name, and value are set hue light state and exit
  if (isset($_GET["light"]) && isset($_GET["key"]) && isset($_GET["value"])) {
    $hue_url .= "/" . $_GET["light"] . "/state";
    echo file_get_contents($hue_url, false, stream_context_create(["http" => [
      "method" => "PUT", "header" => "Content-type: application/json",
      "content" => "{\"" . $_GET["key"] . "\":" . $_GET["value"] . "}"
    ]]));
    exit;
  }

  // ============================================
  // ========== REALTIME DASHBOARD DATA =========
  // ============================================

  // Query vars
  $where = " WHERE timestamp>='" . date("Y-m-d 00:00:00") . "' and timestamp<='" . date("Y-m-d 23:59:59") . "' ORDER BY id DESC LIMIT 0,1";

  // Get astro pi weather data
  $row = $db->query("SELECT humidity, pressure, temperature FROM weather" . $where)->fetch_object();
  $json["temperature"] = (float)$row->temperature + 273.15; // K
  $json["pressure"] = (float)$row->pressure; // hPa
  $json["humidity"] = (float)$row->humidity; // %

  // Get energy now
  $row = $db->query("SELECT power FROM energy1" . $where)->fetch_object();
  $json["energy"]["now"] = (int)$row->power * -1; // W

  // Get energy / gas today
  $row = $db->query("SELECT low_delivered, normal_delivered, solar_delivered, low_used, normal_used, gas_used FROM energy_summary WHERE date='" . date("Y-m-d") . "'")->fetch_object();

  $tmp = $row->solar_delivered - $row->low_delivered - $row->normal_delivered;
  $local_delivered = $tmp > 0 ? $tmp : 0;
  $today_delivered = $row->low_delivered + $row->normal_delivered + $local_delivered;
  $today_used = $row->low_used + $row->normal_used + $local_delivered;

  $json["energy"]["today"] = (float)($today_delivered - $today_used); // kWh
  $json["gas"]["today"] = (float)$row->gas_used; // m3

  // Get total energy / gas delivered used
  $row = $db->query("SELECT SUM(low_used) as low_used, SUM(normal_used) as normal_used, SUM(low_delivered) as low_delivered, SUM(normal_delivered) as normal_delivered, SUM(solar_delivered) as solar_delivered, SUM(gas_used) as gas_used FROM energy_summary WHERE date>='" . date("Y-1-1") . "' and date<='" . date("Y-12-t") . "'")->fetch_object();

  $tmp = $row->solar_delivered - $row->low_delivered - $row->normal_delivered;
  $local_delivered = $tmp > 0 ? $tmp : 0;

  $json["energy"]["delivered"] = $row->low_delivered + $row->normal_delivered + $local_delivered; // kWh
  $json["energy"]["used"] = $row->low_used + $row->normal_used + $local_delivered; // kWh
  $json["gas"]["used"] = round($row->gas_used, 1); // m3

  // Energy co2 emission = 1kWh grey energy is 0.526 kg co2
  $json["energy"]["co2"] = round(($json["energy"]["used"] - $json["energy"]["delivered"]) * 0.526, 1); // kg

  // Burning 1 m3 gas results in 1.78 kg CO2 emission
  $json["gas"]["co2"] = round(($json["gas"]["used"] * 1.78), 1); // kg

  // Get raw weather data decode to json it will converted realtime by client with js to readable info
  $weather = json_decode(file_get_contents("http://api.openweathermap.org/data/2.5/weather?q=Gouda,NL&appid=4e28f75f5d0eded171ea5eeffb2eb77a"));
  $json["weather"]["temperature"] = $weather->main->temp;  // Kelvin
  $json["weather"]["humidity"] = $weather->main->humidity; // procent
  $json["weather"]["pressure"] = $weather->main->pressure; // hPa
  $json["weather"]["wind_speed"] = $weather->wind->speed;  // m/s
  $json["weather"]["sunrise"] = $weather->sys->sunrise;    // Unix timestamp
  $json["weather"]["sunset"] = $weather->sys->sunset;      // -^

  // return json and exit
  echo json_encode($json);
  exit;
}

?>

<style>
  *{margin:0;padding:0;font-family:Arial,sans-serif;font-size:14px;line-height:1;transition:all .25s}
  body{background-color:#333;color:#fff;overflow-x:hidden}[onclick]{cursor:pointer}
  .night{-webkit-filter:grayscale(100%) brightness(75%);filter:grayscale(100%) brightness(75%)}
  a{color:inherit;text-decoration:none}a:hover{text-decoration:underline}
  svg{width:24px;fill:#222}.hidden{visibility:hidden;opacity:0}

  /* Sidebars */
  .shadow{position:fixed;top:0;left:0;width:100%;height:100%;background-color:rgba(0,0,0,.75)}
  .sidebar{position:fixed;top:0;left:-320px;width:320px;height:100%;background:#fff;color:#222}
  .sidebar.open{left:0}.sidebar>.header{border-bottom:1px solid #ccc}
  .sidebar>.header>label{display:inline-block;font-size:18px;margin-left:16px;line-height:56px}
  .sidebar>.header>svg{float:right;padding:16px;border-left:1px solid #ccc}
  .sidebar>.header>svg:hover{background-color:#eee}
  .sidebar>.body{padding:16px;height:calc(100% - 89px);overflow:auto}
  .sidebar>.body>label{display:block;font-weight:bold;margin-bottom:16px}
  .sidebar>.body>select{background:#fff;color:#222;width:100%;padding:8px;border:1px solid #bbb;outline:0;margin-bottom:24px}
  .sidebar>.body>select:hover{border-color:#aaa}
  .sidebar>.body>select:focus{border-color:#999}
  .sidebar>.body>select:last-child{margin-bottom:0}

  /* Loader */
  @keyframes rotate{to{transform:rotate(360deg)}}
  .loader{position:fixed;top:calc(50% - 32px);left:calc(50% - 32px);width:64px;height:64px;animation:rotate 1s linear infinite}

  /* Grid and tiles */
  .grid{position:absolute;top:0;left:0;right:0;bottom:0;width:960px;height:540px;margin:auto}
  .tile{position:relative;float:left;margin:8px;width:calc(25% - 16px);height:calc(25% - 16px);overflow:hidden;text-align:center;transition:all 0s}
  .tile>div{position:absolute;width:100%;height:100%}.small{width:calc(12.5% - 16px)}
  .tile svg{position:absolute;top:0;left:0;right:0;bottom:16px;width:48px;height:48px;margin:auto;fill:#fff}
  .tile p{position:absolute;top:0;left:0;bottom:16px;height:32px;line-height:32px;margin:auto;font-size:24px;width:100%}
  .tile label{position:absolute;left:0;bottom:12px;width:100%}

  /* Media Querys: makes grid responsive */
  @media(min-width:976px) and (min-height:480px) and (max-height:572px){.grid{width:calc(100% - 8px);height:calc(100% - 8px)}.tile{margin:4px;width:calc(25% - 8px);height:calc(25% - 8px)}.small{width:calc(12.5% - 8px)}}
  @media(max-width:976px){.grid{margin:8px auto;width:480px}.tile{width:calc(50% - 16px)}.small{width:calc(25% - 16px)}.tile:last-child{margin-bottom:16px}}
  @media(max-width:512px){.grid{margin:4px auto;width:calc(100% - 8px)}.tile{margin:4px;width:calc(50% - 8px);height:calc(25% - 8px)}.small{width:calc(25% - 8px)}.tile:last-child{margin-bottom:8px}}

  /* Tile live animations */
  @keyframes top-bottom{0%,25%{bottom:100%}35%,75%{bottom:0%}85%,100%{bottom:-100%}}
  .top-bottom>.one{animation:top-bottom 8s infinite -4s}
  .top-bottom>.two{animation:top-bottom 8s infinite}
  @keyframes bottom-top{0%,25%{top:100%}35%,75%{top:0%}85%,100%{top:-100%}}
  .bottom-top>.one{animation:bottom-top 8s infinite -4s}
  .bottom-top>.two{animation:bottom-top 8s infinite}
  @keyframes left-right{0%,25%{right:100%}35%,75%{right:0%}85%,100%{right:-100%}}
  .left-right>.one{animation:left-right 8s infinite -4s}
  .left-right>.two{animation:left-right 8s infinite}
  @keyframes right-left{0%,25%{left:100%}35%,75%{left:0%}85%,100%{left:-100%}}
  .right-left>.one{animation:right-left 8s infinite -4s}
  .right-left>.two{animation:right-left 8s infinite}

  /* Tile color palette */
  .red{background:#E53935}.pink{background:#E91E63}.purple{background:#9C27B0}.deep-purple{background:#673AB7}
  .indigo{background:#3F51B5}.blue{background:#2196F3}.teal{background:#009688}.green{background:#43A047}.yellow{background:#FFB300}
  .orange{background:#FF5722}.brown{background:#795548}.gray{background:#757575}.blue-gray{background:#607D8B}
</style>

<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="loader">
  <circle cx="12" cy="12" r="11" fill="none" stroke="#eee" stroke-width="2" stroke-linecap="round" stroke-dasharray="90,150" stroke-dashoffset="-35"></circle>
</svg>

<div class="grid hidden">
  <div class="tile orange">
    <p>PlaatEnergy</p>
    <label>Made by <a href="http://plaatsoft.nl/" target="_blank">PlaatSoft</a></label>
  </div>

  <div class="tile top-bottom">
    <div class="one indigo">
       <p id="time"></p>
       <label>Time</label>
    </div>
    <div class="two blue-gray">
       <p id="date"></p>
       <label>Date</label>
    </div>
  </div>

  <div id="energy_today" class="tile" onclick="link('pid=18')">
    <p id="energy_today_text"></p>
    <label>Electricity today</label>
  </div>

  <div id="energy_now" class="tile" onclick="link('pid=60')">
    <p id="energy_now_text"></p>
    <label>Electricity now</label>
  </div>

  <div class="tile deep-purple" onclick="link('pid=71')">
    <p id="temperature"></p>
    <label>Air temperature inside</label>
  </div>

  <div class="tile brown small" onclick="link('pid=80')">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
      <circle cx="12" cy="12" r="3.2"/>
      <path d="M9 2L7.17 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2h-3.17L15 2H9zm3 15c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5z"/>
    </svg>
    <label>Webcams</label>
  </div>

  <div class="tile small pink" onclick="openSidebar('#settings')">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
      <path d="M19.43 12.98c.04-.32.07-.64.07-.98s-.03-.66-.07-.98l2.11-1.65c.19-.15.24-.42.12-.64l-2-3.46c-.12-.22-.39-.3-.61-.22l-2.49 1c-.52-.4-1.08-.73-1.69-.98l-.38-2.65C14.46 2.18 14.25 2 14 2h-4c-.25 0-.46.18-.49.42l-.38 2.65c-.61.25-1.17.59-1.69.98l-2.49-1c-.23-.09-.49 0-.61.22l-2 3.46c-.13.22-.07.49.12.64l2.11 1.65c-.04.32-.07.65-.07.98s.03.66.07.98l-2.11 1.65c-.19.15-.24.42-.12.64l2 3.46c.12.22.39.3.61.22l2.49-1c.52.4 1.08.73 1.69.98l.38 2.65c.03.24.24.42.49.42h4c.25 0 .46-.18.49-.42l.38-2.65c.61-.25 1.17-.59 1.69-.98l2.49 1c.23.09.49 0 .61-.22l2-3.46c.12-.22.07-.49-.12-.64l-2.11-1.65zM12 15.5c-1.93 0-3.5-1.57-3.5-3.5s1.57-3.5 3.5-3.5 3.5 1.57 3.5 3.5-1.57 3.5-3.5 3.5z"/>
    </svg>
    <label>Settings</label>
  </div>

  <div class="tile small yellow" onclick="link('pid=90')">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
      <path d="M9 21c0 .55.45 1 1 1h4c.55 0 1-.45 1-1v-1H9v1zm3-19C8.14 2 5 5.14 5 9c0 2.38 1.19 4.47 3 5.74V17c0 .55.45 1 1 1h6c.55 0 1-.45 1-1v-2.26c1.81-1.27 3-3.36 3-5.74 0-3.86-3.14-7-7-7zm2.85 11.1l-.85.6V16h-4v-2.3l-.85-.6C7.8 12.16 7 10.63 7 9c0-2.76 2.24-5 5-5s5 2.24 5 5c0 1.63-.8 3.16-2.15 4.1z"/>
    </svg>
    <label>Lights</label>
  </div>

  <div class="tile small blue-gray" onclick="link('pid=11')">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
      <path d="M10.09 15.59L11.5 17l5-5-5-5-1.41 1.41L12.67 11H3v2h9.67l-2.58 2.59zM19 3H5c-1.11 0-2 .9-2 2v4h2V5h14v14H5v-4H3v4c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z"/>
    </svg>
    <label>Exit</label>
  </div>

  <div class="tile bottom-top">
    <div class="one gray" onclick="link('pid=62')">
      <p id="gas_today"></p>
      <label>Gas used today</label>
    </div>
    <div class="two deep-purple" onclick="link('pid=42')">
      <p id="gas_used"></p>
      <label>Gas used annually</label>
    </div>
  </div>

  <div class="tile green" onclick="link('pid=72')">
    <p id="humidity"></p>
    <label>Air humidity inside</label>
  </div>

  <div class="tile blue-gray" onclick="link('pid=70')">
    <p id="pressure"></p>
    <label>Air pressure inside</label>
  </div>

  <div class="tile brown">
    <p id="weather_wind_speed"></p>
    <label>Wind speed outside</label>
  </div>

  <div class="tile purple">
    <p id="weather_temperature"></p>
    <label>Air temperature outside</label>
  </div>

  <div class="tile right-left">
    <div class="one teal">
       <p id="energy_co2"></p>
       <label>Electricity co2 emission annually</label>
    </div>
    <div class="two blue">
       <p id="gas_co2"></p>
       <label>Gas co2 emission annually</label>
    </div>
  </div>

  <div class="tile left-right">
    <div class="one green" onclick="link('pid=41')">
      <p id="energy_delivered"></p>
     <label>Electricity delivered annually</label>
    </div>
    <div class="two red" onclick="link('pid=40')">
      <p id="energy_used"></p>
      <label>Electricity used annually</label>
    </div>
  </div>

  <div class="tile orange">
    <p id="weather_pressure"></p>
    <label>Air pressure outside</label>
  </div>

  <div class="tile teal">
    <p id="weather_humidity"></p>
    <label>Air humidity outside</label>
  </div>
</div>

<div class="shadow hidden"></div>

<div id="settings" class="sidebar">
  <div class="header">
    <label>Settings</label>
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" onclick="closeSidebar(this)">
      <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
    </svg>
  </div>
  <div class="body">
    <label>Dimming screen for night</label>
    <select name="night_mode">
      <option value="yes">Yes, I want to see clear now</option>
      <option value="no" selected>No, I want to dazzle my eyes</option>
    </select>

    <label>Number, Date and Time format</label>
    <select name="format">
      <option value="british">British format</option>
      <option value="dutch">Dutch format</option>
    </select>

    <label>Gas unit</label>
    <select name="gas">
      <option value="m3">Cubic meters (m&sup3;)</option>
      <option value="dm3">Cubic decimeters (dm&sup3;)</option>
    </select>

    <label>Temperature unit</label>
    <select name="temperature">
      <option value="celcius">Celcius (&deg;C)</option>
      <option value="fahrenheit">Fahrenheit (&deg;F)</option>
      <option value="kelvin">Kelvin (K)</option>
    </select>

    <label>Wind speed unit</label>
    <select name="wind_speed">
      <option value="m/s">Meters per second (m/s)</option>
      <option value="mph">Miles per hour (mph)</option>
      <option value="km/h">Kilometers per hour (km/h)</option>
    </select>
  </div>
</div>

<script>
  "use strict";
  var e, i, data, filename = "<?php echo basename(__FILE__); ?>", loaded = false;

  // Format functions
  function $ (e) {
    if (e < 10) e = "0" + e;
    return e
  }
  function format_number (e, d) { e = Number(e);
    if (d) { e = Math.round(e) } else { e = e.toFixed(1) }
    e = String(e).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
    if (localStorage.format == "dutch") e = e.replace(/\./g, "#").replace(/\,/g, ".").replace(/#/g, ",");
    return e.replace("-", "- ");
  }
  function format_time (d) { d = new Date(d), e = d.getHours();
    if (localStorage.format == "dutch") return $(e) + ":" + $(d.getMinutes()) + ":" + $(d.getSeconds());
    if (localStorage.format == "british") return $(e-(e>12?12:0)) + ":" + $(d.getMinutes()) + ":" + $(d.getSeconds()) + " " + ["AM", "PM"][e>12?1:0];
  }
  function format_date (d) { d = new Date(d);
    return $(d.getDate()) + "-" + $(d.getMonth() + 1) + "-" + d.getFullYear();
  }
  function format_temperature (k) {
    if (localStorage.temperature == "celcius") return format_number(k - 273.15) + " &deg;C";
    if (localStorage.temperature == "fahrenheit") return format_number(k * 9 / 5 - 459.67) + " &deg;F";
    if (localStorage.temperature == "kelvin") return format_number(k) + " K";
  }
  function format_gas (m3) {
    if (localStorage.gas == "m3") return format_number(m3) + " m&sup3;";
    if (localStorage.gas == "dm3") return format_number(m3 * 1000, true) + " dm&sup3;";
  }

  // Settings localStorage update script
  e = document.querySelectorAll("select");
  for (i = 0; i < e.length; i++) {
    if (localStorage[e[i].name]) {
       e[i].value = localStorage[e[i].name];
    } else {
       localStorage[e[i].name] = e[i].value;
    }
    e[i].onchange = function () {
      localStorage[this.name] = this.value;
      updateData();
    };
  }

  // Sidebar open and close support
  var shadow = document.querySelector(".shadow");

  function openSidebar (e) {
    shadow.classList.remove("hidden");
    document.querySelector(e).classList.add("open");
  }

  function closeSidebar (e) {
    shadow.classList.add("hidden");
    e.parentNode.parentNode.classList.remove("open");
  }

  // Function to update date and time tile
  function updateDateTime () {
    e = new Date().getTime();
    document.querySelector("#date").innerHTML = format_date(e);
    document.querySelector("#time").innerHTML = format_time(e);
    setTimeout(updateDateTime, 1000);
  }
  updateDateTime();

  // Function to get the data and put it in data var
  function getData () {
    var e = new XMLHttpRequest();
    e.onload = function () {
      data = JSON.parse(e.responseText);
      updateData();
      if (!loaded) { loaded = true;
        document.querySelector(".loader").classList.add("hidden");
        document.querySelector(".grid").classList.remove("hidden");
      }
      setTimeout(getData, 6e3);
    };
    e.open("GET", filename);
    e.send();
  }
  getData();

  // Function to update all data
  function updateData () {
     e = data.weather.wind_speed;
     if (localStorage.wind_speed == "m/s") e = format_number(e) + " m/s";
     if (localStorage.wind_speed == "mph") e = format_number(e * 2.237) + " mph";
     if (localStorage.wind_speed == "km/h") e = format_number(e * 3.6) + " km/h";
     document.querySelector("#weather_wind_speed").innerHTML = e;

     document.querySelector("#gas_today").innerHTML = format_gas(data.gas.today);
     document.querySelector("#gas_used").innerHTML = format_gas(data.gas.used);

     document.querySelector("#energy_co2").innerHTML = format_number(data.energy.co2) + " kg";
     document.querySelector("#gas_co2").innerHTML = format_number(data.gas.co2) + " kg";

     document.querySelector("#energy_delivered").innerHTML = format_number(data.energy.delivered, true) + " kWh";
     document.querySelector("#energy_used").innerHTML = format_number(data.energy.used, true) + " kWh";

     e = data.energy.now;
     document.querySelector("#energy_now").classList.add(e > 0 ? "green" : "red");
     document.querySelector("#energy_now_text").innerHTML = (e > 0 ? "+ " : "") + format_number(e, true) + " Watt";

     e = data.energy.today;
     document.querySelector("#energy_today").classList.add(e > 0 ? "green" : "red");
     document.querySelector("#energy_today_text").innerHTML = (e > 0 ? "+ " : "") + format_number(e) + " kWh";

     document.querySelector("#temperature").innerHTML = format_temperature(data.temperature);
     document.querySelector("#pressure").innerHTML = format_number(data.pressure) + " hPa";
     document.querySelector("#humidity").innerHTML = format_number(data.humidity) + " %";
     document.querySelector("#weather_temperature").innerHTML = format_temperature(data.weather.temperature);
     document.querySelector("#weather_pressure").innerHTML = format_number(data.weather.pressure) + " hPa";
     document.querySelector("#weather_humidity").innerHTML = format_number(data.weather.humidity) + " %";

     if (localStorage.night_mode == "yes") {
       document.body.classList.add("night");
     } else {
       document.body.classList.remove("night");
     }
  }
</script>
