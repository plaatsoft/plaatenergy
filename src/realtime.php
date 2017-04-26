<?php

// This whole file / page is made by Bastiaan van der Plaat (https://bastiaan.plaatsoft.nl)

// When you send a get message to realtime.php you get the db info
if ($_SERVER["REQUEST_METHOD"] == "GET") {

  // Load database username and password
  include "config.php";
  include "general.php";

  global $kwh_to_co2_factor;
  global $m3_to_co2_factor;

  header("Content-Type: application/json"); // Set file mimetype to json
  header("Cache-Control: no-cache");        // Tels browser to don't cache this file

  // Connect to the database
  $db = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

   // Array/object where response data stored
  $json = [];

  // Get energy now
  $row = $db->query("SELECT power FROM energy1 WHERE timestamp>='" . date("Y-m-d 00:00:00") . "' and timestamp<='" . date("Y-m-d 23:59:59") . "' ORDER BY id DESC LIMIT 0,1")->fetch_object();
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
  $row = $db->query("SELECT SUM(low_used) as low_used, SUM(normal_used) as normal_used, SUM(low_delivered) as low_delivered, SUM(normal_delivered) as normal_delivered," .
  " SUM(solar_delivered) as solar_delivered, SUM(gas_used) as gas_used FROM energy_summary WHERE date>='" . date("Y-1-1") . "' and date<='" . date("Y-12-t") . "'")->fetch_object();

  $tmp = $row->solar_delivered - $row->low_delivered - $row->normal_delivered;
  $local_delivered = $tmp > 0 ? $tmp : 0;

  $json["energy"]["delivered"] = $row->low_delivered + $row->normal_delivered + $local_delivered; // kWh
  $json["energy"]["used"] = $row->low_used + $row->normal_used + $local_delivered; // kWh
  $json["gas"]["used"] = (int)$row->gas_used; // m3

  // Energy co2 emission = 1kWh grey energy is 0.526 kg co2
  $json["energy"]["co2"] = ($json["energy"]["used"] - $json["energy"]["delivered"]) * $kwh_to_co2_factor;

  // Burning 1 m3 gas results in 1.78 kg CO2 emission
  $json["gas"]["co2"] = $json["gas"]["used"] * $m3_to_co2_factor;

  // Get raw weather data decode to json it will converted realtime by client with js to readable info because otherwise http vs https clash
  $weather = json_decode(file_get_contents("http://api.openweathermap.org/data/2.5/weather?id=" . $_GET["city"] . "&appid=8cfe5f379c10d3a2d63b3ce8226ce9e3"));
  $json["weather"]["temperature"] = $weather->main->temp;  // K
  $json["weather"]["humidity"] = $weather->main->humidity; // %
  $json["weather"]["pressure"] = $weather->main->pressure; // hPa
  $json["weather"]["clouds"] = $weather->clouds->all;      // %
  $json["weather"]["wind_speed"] = $weather->wind->speed;  // m/s
  $json["weather"]["sunrise"] = $weather->sys->sunrise;    // Unix timestamp
  $json["weather"]["sunset"] = $weather->sys->sunset;      // -^

  // return json and exit
  echo json_encode($json);
  exit;
}

?>

<style>
  *{margin:0;padding:0;font-family:Arial,sans-serif;font-size:14px;line-height:1;-webkit-transition:all .25s;transition:all .25s}
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
  select,input{box-sizing:border-box;background:#fff;color:#222;width:100%;padding:8px;border:1px solid #bbb;outline:0;margin-bottom:24px}
  select:hover,input:hover{border-color:#aaa}
  select:focus,input:focus{border-color:#999}
  select:last-child{margin-bottom:0}

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
  @media(min-width:976px) and (min-height:480px) and (max-height:556px){.grid{width:calc(100% - 8px);height:calc(100% - 8px)}.tile{margin:4px;width:calc(25% - 8px);height:calc(25% - 8px)}.small{width:calc(12.5% - 8px)}}
  @media(max-width:976px){.grid{margin:8px auto;width:480px}.tile{width:calc(50% - 16px)}.small{width:calc(25% - 16px)}.tile:last-child{margin-bottom:16px}}
  @media(max-width:512px){.grid{margin:4px auto;width:calc(100% - 8px)}.tile{margin:4px;width:calc(50% - 8px);height:calc(25% - 8px)}.small{width:calc(25% - 8px)}.tile:last-child{margin-bottom:8px}}

  /* Tile animations */
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
  .red{background:#E33}.pink{background:#E26}.purple{background:#91B}.deep-purple{background:#63B}.lime{background:#992}
  .indigo{background:#35B}.blue{background:#29F}.teal{background:#098}.green{background:#4A4}.light-green{background:#8B5}
  .yellow{background:#FB0}.orange{background:#F52}.brown{background:#754}.gray{background:#777}.blue-gray{background:#678}
</style>

<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="loader">
  <circle cx="12" cy="12" r="11" fill="none" stroke="#eee" stroke-width="2" stroke-linecap="round" stroke-dasharray="90,150" stroke-dashoffset="-35"></circle>
</svg>

<div class="grid hidden">
  <div class="tile orange">
    <p>PlaatEnergy</p>
    <label>Made by <a href="http://plaatsoft.nl" target="_blank">PlaatSoft</a></label>
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

  <div id="energy_today" class="tile" onclick="link('pid=<?php echo PAGE_DAY_OUT_ENERGY; ?>')">
    <p id="energy_today_text"></p>
    <label>Electricity today</label>
  </div>

  <div id="energy_now" class="tile" onclick="link('pid=<?php echo PAGE_DAY_IN_ENERGY; ?>')">
    <p id="energy_now_text"></p>
    <label>Electricity now</label>
  </div>

  <div class="tile left-right">
    <div class="one blue">
       <p id="weather_sunrise"></p>
       <label>Sunrise</label>
    </div>
    <div class="two purple">
       <p id="weather_sunset"></p>
       <label>Sunset</label>
    </div>
  </div>

  <div class="tile brown small" onclick="alert('PlaatProtect')">
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

  <div class="tile small yellow" onclick="alert('PlaatProtect')">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
      <path d="M9 21c0 .55.45 1 1 1h4c.55 0 1-.45 1-1v-1H9v1zm3-19C8.14 2 5 5.14 5 9c0 2.38 1.19 4.47 3 5.74V17c0 .55.45 1 1 1h6c.55 0 1-.45 1-1v-2.26c1.81-1.27 3-3.36 3-5.74 0-3.86-3.14-7-7-7zm2.85 11.1l-.85.6V16h-4v-2.3l-.85-.6C7.8 12.16 7 10.63 7 9c0-2.76 2.24-5 5-5s5 2.24 5 5c0 1.63-.8 3.16-2.15 4.1z"/>
    </svg>
    <label>Lights</label>
  </div>

  <div class="tile small blue-gray" onclick="link('pid=<?php echo PAGE_HOME; ?>')">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
      <path d="M10.09 15.59L11.5 17l5-5-5-5-1.41 1.41L12.67 11H3v2h9.67l-2.58 2.59zM19 3H5c-1.11 0-2 .9-2 2v4h2V5h14v14H5v-4H3v4c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z"/>
    </svg>
    <label>Exit</label>
  </div>

  <div class="tile bottom-top">
    <div class="one gray" onclick="link('pid=<?php echo PAGE_DAY_IN_GAS; ?>&eid=<?php echo EVENT_M3; ?>')">
      <p id="gas_today"></p>
      <label>Gas used today</label>
    </div>
    <div class="two deep-purple" onclick="link('pid=<?php echo PAGE_YEAR_IN_GAS; ?>&eid=<?php echo EVENT_M3; ?>')">
      <p id="gas_used"></p>
      <label>Gas used annually</label>
    </div>
  </div>

  <div class="tile top-bottom">
    <div class="one deep-purple" onclick="alert('PlaatProtect')">
      <p id="temperature"></p>
      <label>Air temperature inside</label>
    </div>
    <div class="two green">
      <p id="weather_temperature"></p>
      <label>Air temperature outside</label>
    </div>
  </div>

  <div class="tile bottom-top">
    <div class="one orange" onclick="alert('PlaatProtect')">
      <p id="pressure"></p>
      <label>Air pressure inside</label>
    </div>
    <div class="two gray">
      <p id="weather_pressure"></p>
      <label>Air pressure outside</label>
    </div>
  </div>

  <div class="tile brown">
    <p id="weather_wind_speed"></p>
    <label>Wind speed outside</label>
  </div>

  <div class="tile left-right">
    <div class="one blue" onclick="alert('PlaatProtect')">
      <p id="humidity"></p>
      <label>Air humidity inside</label>
    </div>
    <div class="two light-green">
      <p id="weather_humidity"></p>
      <label>Air humidity outside</label>
    </div>
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

  <div class="tile pink small" onclick="link('pid=<?php echo PAGE_DONATE; ?>')">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
      <path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/>
    </svg>
    <label>Donate</label>
  </div>

  <div class="tile lime small" onclick="link('pid=<?php echo PAGE_ABOUT; ?>')">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
      <path d="M16.5 12c1.38 0 2.49-1.12 2.49-2.5S17.88 7 16.5 7C15.12 7 14 8.12 14 9.5s1.12 2.5 2.5 2.5zM9 11c1.66 0 2.99-1.34 2.99-3S10.66 5 9 5C7.34 5 6 6.34 6 8s1.34 3 3 3zm7.5 3c-1.83 0-5.5.92-5.5 2.75V19h11v-2.25c0-1.83-3.67-2.75-5.5-2.75zM9 13c-2.33 0-7 1.17-7 3.5V19h7v-2.25c0-.85.33-2.34 2.37-3.47C10.5 13.1 9.66 13 9 13z"/>
    </svg>
    <label>About</label>
  </div>

  <div class="tile top-bottom">
    <div class="one green" onclick="link('pid=<?php echo PAGE_YEAR_OUT_ENERGY; ?>')">
      <p id="energy_delivered"></p>
     <label>Electricity delivered annually</label>
    </div>
    <div class="two red" onclick="link('pid=<?php echo PAGE_YEAR_IN_ENERGY; ?>')">
      <p id="energy_used"></p>
      <label>Electricity used annually</label>
    </div>
  </div>

  <div class="tile teal">
    <p id="weather_clouds"></p>
    <label>Clouds outside</label>
  </div>
</div>

<div class="shadow hidden"></div>

<div id="settings" class="sidebar">
  <div class="header">
    <label>Settings</label>
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" onclick="closeSidebar('#settings')">
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
      <option value="dutch" selected>Dutch format</option>
    </select>

    <label>Weather cityID (<a href="http://openweathermap.org/help/city_list.txt" target="_blank">find your own</a>)</label>
    <input name="weather_cityID" value="2755420">

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
      <option value="km/h" selected>Kilometers per hour (km/h)</option>
    </select>
  </div>
</div>

<script>
  "use strict";
  var data, filename = "<?php echo basename(__FILE__); ?>", loaded = false;

  // Format functions
  function formatNumber (number, precision, positivePlus) {
    if (number == null) number = 0; if (precision == null) precision = 1; if (positivePlus == null) positivePlus = false;
    var formatedNumber = number.toFixed(precision).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,").replace("-", "- ");
    if (localStorage.format == "dutch") formatedNumber = formatedNumber.replace(/\./g, "#").replace(/\,/g, ".").replace(/#/g, ",");
    if (positivePlus == true && number >= 0) formatedNumber = "+ " + formatedNumber;
    return formatedNumber;
  }
  function formatTemperature(kelvin) {
    if (localStorage.temperature == "celcius") return formatNumber(kelvin - 273.15) + " &deg;C";
    if (localStorage.temperature == "fahrenheit") return formatNumber(kelvin * 9/5 - 459.67) + " &deg;F";
    if (localStorage.temperature == "kelvin") return formatNumber(kelvin) + " K";
  }
  function formatGas(m3) {
    if (localStorage.gas == "m3") return formatNumber(m3) + " m&sup3;";
    if (localStorage.gas == "dm3") return formatNumber(m3 * 1000, 0) + " dm&sup3;";
  }
  function formatWindSpeed (mps) {
     if (localStorage.wind_speed == "m/s") return formatNumber(mps) + " m/s";
     if (localStorage.wind_speed == "mph") return formatNumber(mps * 2.237) + " mph";
     if (localStorage.wind_speed == "km/h") return formatNumber(mps * 3.6) + " km/h";
  }
  function addNull (number) { return number < 10 ? "0" + number : number }
  function formatDate (ms) {
    var date = ms == null ? new Date() : new Date(ms);
    if (localStorage.format == "dutch") return addNull(date.getDate()) + "-" + addNull(date.getMonth() + 1) + "-" + date.getFullYear();
    if (localStorage.format == "british") return addNull(date.getDate()) + "/" + addNull(date.getMonth() + 1) + "/" + date.getFullYear();
  }
  function formatTime (ms) {
    var date = ms == null ? new Date() : new Date(ms);
    if (localStorage.format == "dutch") return addNull(date.getHours()) + ":" + addNull(date.getMinutes()) + ":" + addNull(date.getSeconds());
    if (localStorage.format == "british") return addNull(date.getHours() - (date.getHours() > 12 ? 12 : 0)) + ":" +
      addNull(date.getMinutes()) + ":" + addNull(date.getSeconds()) + " " + ["AM", "PM"][date.getHours() > 12 ? 1 : 0];
  }

  // Settings localStorage update script
  var elements = document.querySelectorAll("#settings select, input");
  for (var i = 0; i < elements.length; i++) {
    if (localStorage[elements[i].name]) {
       elements[i].value = localStorage[elements[i].name];
    } else {
       localStorage[elements[i].name] = elements[i].value;
    }
    elements[i].onchange = function () {
      localStorage[this.name] = this.value;
      updateDateTime();
      updateData();
    };
  }

  // Sidebar open and close support
  var shadow = document.querySelector(".shadow");
  function openSidebar (sidebar) {
    shadow.classList.remove("hidden");
    document.querySelector(sidebar).classList.add("open");
  }
  function closeSidebar (sidebar) {
    shadow.classList.add("hidden");
    document.querySelector(sidebar).classList.remove("open");
  }

  // Function to update date and time tile
  function updateDateTime () {
    document.querySelector("#date").innerHTML = formatDate();
    document.querySelector("#time").innerHTML = formatTime();
  }
  function getDateTime () {
    updateDateTime();
    setTimeout(getDateTime, 1000);
  }
  getDateTime();

  // Function to get the data and put it in data var
  function getData () {
    var xhr = new XMLHttpRequest();
    xhr.onload = function () {
      data = JSON.parse(xhr.responseText);
      updateData();
      if (!loaded) {
        loaded = true;
        document.querySelector(".loader").classList.add("hidden");
        document.querySelector(".grid").classList.remove("hidden");
      }
      setTimeout(getData, 6000);
    };
    xhr.open("GET", filename + "?city=" + localStorage.weather_cityID);
    xhr.send();
  }
  getData();

  // Function to update all data
  function updateData () {
     document.querySelector("#gas_today").innerHTML = formatGas(data.gas.today);
     document.querySelector("#gas_used").innerHTML = formatGas(data.gas.used);

     document.querySelector("#energy_co2").innerHTML = formatNumber(data.energy.co2) + " kg";
     document.querySelector("#gas_co2").innerHTML = formatNumber(data.gas.co2) + " kg";

     document.querySelector("#energy_delivered").innerHTML = formatNumber(data.energy.delivered, 0) + " kWh";
     document.querySelector("#energy_used").innerHTML = formatNumber(data.energy.used, 0) + " kWh";

     document.querySelector("#energy_now").classList.add(data.energy.now >= 0 ? "green" : "red");
     document.querySelector("#energy_now_text").innerHTML = formatNumber(data.energy.now, 0, true) + " Watt";

     document.querySelector("#energy_today").classList.add(data.energy.today >= 0 ? "green" : "red");
     document.querySelector("#energy_today_text").innerHTML = formatNumber(data.energy.today, 1, true) + " kWh";

     // Vars are null because this data is remove from PlaatEnergy and go to PlaatProtect thanks to wplaat
     document.querySelector("#temperature").innerHTML = "PlaatProtect";
     document.querySelector("#pressure").innerHTML = "PlaatProtect";
     document.querySelector("#humidity").innerHTML = "PlaatProtect";

     document.querySelector("#weather_temperature").innerHTML = formatTemperature(data.weather.temperature);
     document.querySelector("#weather_pressure").innerHTML = formatNumber(data.weather.pressure) + " hPa";
     document.querySelector("#weather_humidity").innerHTML = formatNumber(data.weather.humidity) + " %";
     document.querySelector("#weather_clouds").innerHTML = formatNumber(data.weather.clouds) + " %";
     document.querySelector("#weather_wind_speed").innerHTML = formatWindSpeed(data.weather.wind_speed);
     document.querySelector("#weather_sunrise").innerHTML = formatTime(data.weather.sunrise * 1000);
     document.querySelector("#weather_sunset").innerHTML = formatTime(data.weather.sunset * 1000);

     if (localStorage.night_mode == "yes") {
       document.body.classList.add("night");
     } else {
       document.body.classList.remove("night");
     }
  }
</script>
