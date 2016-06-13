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

  // Function to get a config item out the database
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
  $json["gas"]["used"] = $row->gas_used; // m3

  // Energy co2 emission = 1kWh grey energy is 0.526 kg co2
  $json["energy"]["co2"] = ($json["energy"]["used"] - $json["energy"]["delivered"]) * 0.526; // kg

  // Burning 1 m3 gas results in 1.78 kg CO2 emission
  $json["gas"]["co2"] = $json["gas"]["used"] * 1.78; // kg

  // Get raw weather data decode to json it will converted realtime by client with js to readable info
  $weather = json_decode(file_get_contents("http://api.openweathermap.org/data/2.5/weather?id=" . $_GET["q"] . "&appid=4e28f75f5d0eded171ea5eeffb2eb77a"));
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
  *{margin:0;padding:0;font-family:Arial,sans-serif;font-size:14px;line-height:1;transition:all .3s}
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
  .red{background:#E53935}.pink{background:#E91E63}.purple{background:#9C27B0}.deep-purple{background:#673AB7}
  .indigo{background:#3F51B5}.blue{background:#2196F3}.teal{background:#009688}.green{background:#43A047}.lime{background:#689F38}
  .yellow{background:#FFB300}.orange{background:#FF5722}.brown{background:#795548}.gray{background:#757575}.blue-gray{background:#607D8B}
</style>

<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="loader">
  <circle cx="12" cy="12" r="11" fill="none" stroke="#eee" stroke-width="2" stroke-linecap="round" stroke-dasharray="90,150" stroke-dashoffset="-35"></circle>
</svg>

<div class="grid hidden">
  <div class="tile orange">
    <p>PlaatEnergy</p>
    <label>Made by <a href="http://bastiaan.plaatsoft.nl" target="_blank">Bastiaan</a></label>
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

  <div id="energy_today" class="tile" onclick="link('pid=61')">
    <p id="energy_today_text"></p>
    <label>Electricity today</label>
  </div>

  <div id="energy_now" class="tile" onclick="link('pid=60')">
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
    <div class="one gray" onclick="link('pid=62&eid=19')">
      <p id="gas_today"></p>
      <label>Gas used today</label>
    </div>
    <div class="two deep-purple" onclick="link('pid=42&eid=19')">
      <p id="gas_used"></p>
      <label>Gas used annually</label>
    </div>
  </div>

  <div class="tile top-bottom">
    <div class="one deep-purple" onclick="link('pid=71')">
      <p id="temperature"></p>
      <label>Air temperature inside</label>
    </div>
    <div class="two green">
      <p id="weather_temperature"></p>
      <label>Air temperature outside</label>
    </div>
  </div>

  <div class="tile bottom-top">
    <div class="one orange" onclick="link('pid=70')">
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
    <div class="one blue" onclick="link('pid=72')">
      <p id="humidity"></p>
      <label>Air humidity inside</label>
    </div>
    <div class="two pink">
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

  <div class="tile pink small" onclick="link('pid=13')">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
      <path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/>
    </svg>
    <label>Donate</label>
  </div>

  <div class="tile lime small" onclick="link('pid=12')">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
      <path d="M16.5 12c1.38 0 2.49-1.12 2.49-2.5S17.88 7 16.5 7C15.12 7 14 8.12 14 9.5s1.12 2.5 2.5 2.5zM9 11c1.66 0 2.99-1.34 2.99-3S10.66 5 9 5C7.34 5 6 6.34 6 8s1.34 3 3 3zm7.5 3c-1.83 0-5.5.92-5.5 2.75V19h11v-2.25c0-1.83-3.67-2.75-5.5-2.75zM9 13c-2.33 0-7 1.17-7 3.5V19h7v-2.25c0-.85.33-2.34 2.37-3.47C10.5 13.1 9.66 13 9 13z"/>
    </svg>
    <label>About</label>
  </div>

  <div class="tile top-bottom">
    <div class="one green" onclick="link('pid=41')">
      <p id="energy_delivered"></p>
     <label>Electricity delivered annually</label>
    </div>
    <div class="two red" onclick="link('pid=40')">
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
      <option value="dutch" selected>Dutch format</option>
    </select>

    <label>Weather location (only Benelux)</label>
    <select name="weather_location">
<?php

$data = [2744819=>"Gemeente Wervershoof, NL",2784549=>"Visé, BE",2793079=>"Lede, BE",2795101=>"Ieper, BE",2749810=>"Gemeente Noordwijkerhout, NL",2751688=>"Gemeente Leusden, NL",2753467=>"Gemeente Huizen, NL",2753718=>"Gemeente Hoogeveen, NL",2754696=>"Gemeente Heemskerk, NL",2759874=>"Gemeente Alphen aan den Rijn, NL",2751791=>"Gemeente Leeuwarden, NL",2754273=>"Het Hazebos, NL",7874246=>"Musiskwartier, NL",2787891=>"Roeselare, BE",2788507=>"Puurs, BE",2960313=>"Grand Duchy of Luxembourg, LU",2759343=>"Gemeente Beek, NL",2787814=>"Rommelaar, BE",2784199=>"Waimes, BE",2784107=>"Wangenies, BE",2755203=>"Gemeente Grootegast, NL",2753437=>"Gemeente Hulst, NL",2785110=>"Uilenhoek, BE",2801423=>"Bos, BE",2788313=>"Ravels, BE",6544227=>"Gemeente De Wolden, NL",2756100=>"Engeland, NL",2744642=>"Westkapelle, NL",2749851=>"Gemeente Noordoostpolder, NL",2749813=>"Noordwijk aan Zee, NL",2749385=>"Oosterzee, NL",2748200=>"Gemeente Rheden, NL",2756865=>"Dieren, NL",2751271=>"Gemeente Made en Drimmelen, NL",2746607=>"Gemeente Strijen, NL",2746609=>"Strijen, NL",6544252=>"Gemeente Leidschendam-Voorburg, NL",6544287=>"Gemeente Noordenveld, NL",2749835=>"Noord-Scharwoude, NL",2752240=>"Krommenie, NL",2786459=>"Smoorken, BE",2751998=>"Lammerenburg, NL",2747890=>"Gemeente Rotterdam, NL",2747891=>"Rotterdam, NL",2753040=>"Katerveer, NL",2748760=>"Palenstein, NL",2759797=>"Gemeente Amstelveen, NL",2751282=>"Gemeente Maastricht, NL",2756071=>"Enschede, NL",6544259=>"Gemeente Almere, NL",2755250=>"Gemeente Groningen, NL",2755251=>"Groningen, NL",2750052=>"Gemeente Nijmegen, NL",2746012=>"Ubbergen, NL",2754386=>"Hengstdal, NL",2758991=>"Bieberg, NL",2755498=>"Ginneken, NL",2754392=>"Gemeente Hengelo, NL",2754394=>"Hengelo, NL",2756668=>"Gemeente Dordrecht, NL",7535498=>"Gemeente Oldambt, NL",2754063=>"Gemeente Hilversum, NL",2750896=>"Middelburg, NL",2754861=>"Hardenberg, NL",2747350=>"Gemeente 's-Hertogenbosch, NL",2747910=>"Rosmalen, NL",2754651=>"Gemeente Heerlen, NL",2759660=>"Gemeente Arnhem, NL",2755419=>"Gemeente Gouda, NL",2750811=>"Mijnheerkens, NL",2754007=>"Hoek van Holland, NL",2755616=>"Geleen, NL",2756231=>"Gemeente Elburg, NL",2751546=>"Gemeente Lisse, NL",2746837=>"Gemeente Staphorst, NL",2743607=>"Gemeente Zutphen, NL",2749251=>"Oranjewoud, NL",6544290=>"Gemeente Bronckhorst, NL",2754395=>"Hengelo, NL",2755583=>"Gemeente Gennep, NL",2749611=>"Gemeente Ommen, NL",2744002=>"Zeesse, NL",6544269=>"Gemeente Neder-Betuwe, NL",2752899=>"Kesteren, NL",2759151=>"Gemeente Bergen, NL",2749534=>"Oostdorp, NL",2748026=>"Roden, NL",2753439=>"Hulst, NL",2746350=>"Gemeente Tholen, NL",2746351=>"Tholen, NL",2745895=>"Vaartkant, NL",6544245=>"Gemeente Twenterand, NL",2757224=>"Den Ham, NL",2800482=>"Charleroi, BE",2786421=>"Soignies, BE",2784803=>"Arrondissement Veurne, BE",2784805=>"Veurne, BE",2784804=>"Veurne, BE",2792412=>"Arrondissement de Liège, BE",2792414=>"Liège, BE",2793643=>"La Ferté, BE",2800930=>"Arrondissement Brugge, BE",2800935=>"Brugge, BE",2803444=>"Aalter, BE",2791316=>"Merelbeke, BE",2790100=>"Arrondissement de Nivelles, BE",2790102=>"Nivelles, BE",2797671=>"Genk, BE",2789200=>"Pennepoel, BE",2784975=>"Veeweide, BE",2794017=>"Kraainem, BE",2794016=>"Kraainem, BE",2784822=>"Verviers, BE",2789738=>"Oostrozebeke, BE",2803421=>"Aartselaar, BE",2800782=>"Bullange, BE",2795011=>"Ixelles, BE",2793442=>"Lanceaumont, BE",2785623=>"Tervuren, BE",2803074=>"Arlon, BE",2787498=>"Ruyff, BE",2795931=>"Herstal, BE",2803183=>"Angleur, BE",2786548=>"Sint-Stevens-Woluwe, BE",2783737=>"Wezembeek-Oppem, BE",2792008=>"Lummen, BE",2783856=>"Wemmel, BE",2783213=>"Zittaard, BE",2802584=>"Bastogne, BE",2792119=>"Lontzen, BE",2802627=>"Basse Awirs, BE",2802051=>"Bever, BE",2792135=>"Longtain, BE",2790793=>"Montils, BE",2785879=>"Sur le Stockeu, BE",2784373=>"Voroux-lez-Liers, BE",2795818=>"Heulen, BE",2796057=>"Hensies, BE",2790015=>"Nothomb, BE",2791643=>"Martelange, BE",2798594=>"Étalle, BE",2800026=>"Couvin, BE",2788187=>"Rendeux, BE",2802248=>"Sint-Agatha-Berchem, BE",2787601=>"Royère, BE",6957848=>"Courtil Gras, BE",2745859=>"Gemeente Valkenswaard, NL",2755632=>"Gemeente Geldermalsen, NL",2960629=>"Canton d'Echternach, LU",2959975=>"Canton de Wiltz, LU",2960590=>"Ettelbruck, LU",6544229=>"Gemeente De Marne, NL",2751813=>"Leens, NL",2784548=>"Vise, BE",2785779=>"Temse, BE",2787769=>"Ronse, BE",2788499=>"Quaregnon, BE",2789162=>"Peruwelz, BE",2789530=>"Oudenaarde, BE",2791814=>"Manage, BE",2749668=>"Oldebroek, NL",2749756=>"Nunspeet, NL",2751834=>"Leek, NL",2756059=>"Epe, NL",2756295=>"Eibergen, NL",2783278=>"Zelzate, BE",2783717=>"Wielsbeke, BE",2784350=>"Vosselaar, BE",2784349=>"Vosselaar, BE",2786588=>"Lennik, BE",2786587=>"Sint-Martens-Lennik, BE",2789167=>"Perre, BE",2790179=>"Nieuwerkerken, BE",2792423=>"Liedekerke, BE",2793733=>"La Bruyere, BE",2796085=>"Hemiksem, BE",2797771=>"Geer, BE",2799511=>"Dessel, BE",2799798=>"Deerlijk, BE",2800591=>"Cerfontaine, BE",2801438=>"Borsbeek, BE",2801539=>"Bonheiden, BE",2802681=>"Blégny, BE",2802849=>"Awans, BE",2803323=>"Aiseau, BE",7667505=>"Laakdal, BE",2759794=>"Amsterdam, NL",2758401=>"Breda, NL",2796157=>"Heist, BE",2755433=>"Gemeente Gorinchem, NL",2755200=>"Groote Haar, NL",2746222=>"Tolsteeg, NL",2801958=>"Biest, BE",2785140=>"Arrondissement Turnhout, BE",2748686=>"Peelo, NL",2798863=>"Ekstergoor, BE",2744820=>"Wervershoof, NL",2745738=>"Gemeente Veere, NL",2759705=>"Gemeente Apeldoorn, NL",6621532=>"De Heeze, NL",2802399=>"Begijnhof 's Wijk, BE",2747702=>"Schalkwijk, NL",2751721=>"Gemeente Lemsterland, NL",2755873=>"Folgeren, NL",6697777=>"Weidevenne, NL",2758462=>"Boxmeer, NL",2757938=>"Coevering, NL",2751024=>"Meerzicht, NL",2751962=>"Gemeente Langedijk, NL",2745391=>"Gemeente Vlissingen, NL",2756986=>"Gemeente Deventer, NL",2747046=>"Snippeling, NL",7648382=>"Weesperzijde, NL",2752314=>"Kralingen, NL",2744903=>"Gemeente Weesp, NL",2745466=>"Gemeente Vlaardingen, NL",2746300=>"Gemeente Tilburg, NL",6544829=>"Koningshoeven, NL",2743476=>"Gemeente Zwolle, NL",6621526=>"Binnenstad, NL",2756070=>"Gemeente Enschede, NL",6544855=>"Op den Bosch, NL",2753104=>"Gemeente Kampen, NL",2755893=>"Flevowijk, NL",2752260=>"Krispijn, NL",2750324=>"Gemeente Nieuwegein, NL",2753174=>"Jutphaas, NL",2756252=>"Gemeente Eindhoven, NL",2750953=>"Mensfort, NL",2750894=>"Gemeente Middelburg, NL",2745312=>"Voorhof, NL",6697850=>"Hoek, NL",6544255=>"Gemeente Sittard-Geleen, NL",2754858=>"Gemeente Hardenberg, NL",2751005=>"Meezenbroek, NL",2756134=>"Gemeente Emmen, NL",2756038=>"Gemeente Ermelo, NL",2751729=>"Lemmer, NL",2748530=>"Poelpolder, NL",2755281=>"Groenlo, NL",2754668=>"Gemeente Heerenveen, NL",2757857=>"Dalen, NL",2756430=>"Gemeente Edam-Volendam, NL",2750543=>"Munnikeveld, NL",2758619=>"Gemeente Borger-Odoorn, NL",2745931=>"Gemeente Urk, NL",2756428=>"Gemeente Ede, NL",2755994=>"Gemeente Etten-Leur, NL",2799925=>"Cureghem, BE",2786419=>"Arrondissement de Soignies, BE",2783311=>"Zaventem, BE",2784897=>"Venitiaanseheide, BE",2800558=>"Champ de Mai, BE",2784605=>"Vilvoorde, BE",2789787=>"Oostende, BE",2785142=>"Turnhout, BE",2785321=>"Trahison, BE",2796467=>"Haureux, BE",2792432=>"Lichtenberg, BE",2794860=>"Jumet, BE",2796492=>"Hasselt, BE",2793107=>"Le Chenia, BE",2794056=>"Kortrijk, BE",2789882=>"Oliehoek, BE",2790472=>"Namur, BE",2800783=>"Bullange, BE",2795012=>"Elsene, BE",2783160=>"Zuidhoek, BE",2785662=>"Termeulen, BE",2803072=>"Arrondissement d'Arlon, BE",2791825=>"Malplaqués, BE",2785762=>"Tenderlo, BE",2786031=>"Stockem, BE",2783738=>"Wezembeek-Oppem, BE",2787990=>"Rixensart, BE",2783857=>"Wemmel, BE",2802582=>"Arrondissement de Bastogne, BE",2792120=>"Lontzen, BE",2801925=>"Bilzen, BE",2798204=>"Fond Saint-Roch, BE",2800936=>"Brugelette, BE",2790914=>"Molsveld, BE",2803003=>"Atrin, BE",2802251=>"Berchem, BE",2793529=>"L'aisette, BE",2797926=>"Froidmanteau, BE",2797481=>"Goberwelz, BE",2960631=>"Echternach, LU",2960003=>"Wecker, LU",2960656=>"Canton de Diekirch, LU",2783090=>"Zwevegem, BE",2783176=>"Zottegem, BE",2783189=>"Zonhoven, BE",2783205=>"Zoersel, BE",2783275=>"Zemst, BE",2783294=>"Zele, BE",2783309=>"Zedelgem, BE",2783417=>"Wuustwezel, BE",2783633=>"Willebroek, BE",2783760=>"Wevelgem, BE",2783764=>"Wetteren, BE",2783821=>"Wervik, BE",2783942=>"Wavre, BE",2783986=>"Waterloo, BE",2784069=>"Waregem, BE",2784190=>"Walcourt, BE",2784189=>"Walcourt, BE",2785170=>"Tubize, BE",2785365=>"Torhout, BE",2785471=>"Tienen, BE",2785478=>"Tielt, BE",2785613=>"Tessenderlo, BE",2786577=>"Arrondissement Sint-Niklaas, BE",2786088=>"Stekene, BE",2786345=>"Soumagne, BE",2786546=>"Sint-Truiden, BE",2786560=>"Sint-Pieters-Leeuw, BE",2786579=>"Sint-Niklaas, BE",2786695=>"Sint-Gillis-Waas, BE",2786701=>"Sint-Genesius-Rode, BE",2786825=>"Seraing, BE",2786964=>"Schoten, BE",2787049=>"Schilde, BE",2787357=>"Saint-Nicolas, BE",2787663=>"Rotselaar, BE",2787770=>"Ronse, BE",2787888=>"Arrondissement Roeselare, BE",2788089=>"Riemst, BE",2788349=>"Ranst, BE",2788500=>"Quaregnon, BE",2788523=>"Putte, BE",2788727=>"Poperinge, BE",2789163=>"Péruwelz, BE",2789233=>"Peer, BE",2789414=>"Overijse, BE",2789472=>"Oupeye, BE",2789752=>"Oostkamp, BE",2790115=>"Ninove, BE",2790136=>"Nijlen, BE",2790358=>"Neerpelt, BE",2790596=>"Arrondissement de Mouscron, BE",2795937=>"Mouscron, BE",2790677=>"Mortsel, BE",2790871=>"Mons, BE",2791068=>"Mol, BE",2791195=>"Middelkerke, BE",2791425=>"Meise, BE",2791538=>"Mechelen, BE",2791744=>"Marche-en-Famenne, BE",2791815=>"Manage, BE",2791858=>"Maldegem, BE",2791965=>"Maaseik, BE",2792180=>"Lommel, BE",2792197=>"Lokeren, BE",2792236=>"Lochristi, BE",2792361=>"Lille, BE",2792406=>"Lier, BE",2793145=>"Lebbeke, BE",2793447=>"Lanaken, BE",2793509=>"La Louvière, BE",2794071=>"Kortenberg, BE",2794118=>"Kontich, BE",2794167=>"Koksijde, BE",2794216=>"Knokke-Heist, BE",2794664=>"Kasterlee, BE",2794733=>"Kapellen, BE",2794789=>"Kalmthout, BE",2795010=>"Izegem, BE",2795262=>"Houthalen-Helchteren, BE",2795399=>"Hoogstraten, BE",2795802=>"Heusden-Zolder, BE",2795909=>"Herzele, BE",2796010=>"Herentals, BE",2796013=>"Herent, BE",2796154=>"Heist-op-den-Berg, BE",2796543=>"Harelbeke, BE",2796640=>"Hamme, BE",2796834=>"Haaltert, BE",2797115=>"Grimbergen, BE",2797780=>"Geel, BE",2798302=>"Fléron, BE",2798307=>"Flemalle-Haute, BE",2798553=>"Evergem, BE",2798574=>"Eupen, BE",2798616=>"Essen, BE",2799008=>"Edegem, BE",2799091=>"Duffel, BE",2799366=>"Dilbeek, BE",2799368=>"Arrondissement Diksmuide, BE",2799370=>"Diksmuide, BE",2799398=>"Diest, BE",2799413=>"Diepenbeek, BE",2799497=>"Destelbergen, BE",2799648=>"Denderleeuw, BE",2801107=>"Brecht, BE",2801118=>"Brasschaat, BE",2801151=>"Braine-le-Comte, BE",2801448=>"Bornem, BE",2801495=>"Boom, BE",2802035=>"Beveren, BE",2802172=>"Beringen, BE",2802436=>"Beerse, BE",2802744=>"Balen, BE",2803011=>"Ath, BE",2803161=>"Ans, BE",2803451=>"Aalst, BE",2743618=>"Gemeente Zundert, NL",2743948=>"Gemeente Zevenaar, NL",2743976=>"Gemeente Zeist, NL",2743996=>"Gemeente Zeewolde, NL",2744101=>"Gemeente Zaltbommel, NL",2744609=>"Gemeente Weststellingwerf, NL",2744247=>"Gemeente Woerden, NL",6544222=>"Oude IJsselstreek, NL",2744331=>"Gemeente Winterswijk, NL",2744513=>"Gemeente Wijchen, NL",2744548=>"Gemeente Wierden, NL",2744674=>"Gemeente Westervoort, NL",2744826=>"Gemeente Werkendam, NL",2744910=>"Gemeente Weert, NL",2744989=>"Gemeente Wassenaar, NL",2745087=>"Gemeente Wageningen, NL",2745095=>"Gemeente Waddinxveen, NL",2745122=>"Gemeente Waalwijk, NL",2745126=>"Gemeente Waalre, NL",2745153=>"Gemeente Vught, NL",2745295=>"Gemeente Voorst, NL",2745300=>"Gemeente Voorschoten, NL",6544293=>"Gemeente Teylingen, NL",2745579=>"Gemeente Vianen, NL",2745633=>"Gemeente Venray, NL",2745704=>"Gemeente Veldhoven, NL",2745725=>"Gemeente Veghel, NL",2745773=>"Gemeente Veenendaal, NL",2745782=>"Gemeente Veendam, NL",2745783=>"Veendam, NL",2746132=>"Gemeente Tubbergen, NL",2746330=>"Gemeente Tiel, NL",2746421=>"Gemeente Terneuzen, NL",6544235=>"Gemeente Steenwijkerland, NL",2746801=>"Gemeente Steenbergen, NL",2746859=>"Gemeente Stadskanaal, NL",2747020=>"Gemeente Someren, NL",2747168=>"Gemeente Sliedrecht, NL",2747226=>"Gemeente Sint-Oedenrode, NL",2747583=>"Gemeente Schijndel, NL",2747595=>"Gemeente Schiedam, NL",2747719=>"Gemeente Schagen, NL",2747857=>"Gemeente Rucphen, NL",2747929=>"Gemeente Roosendaal, NL",2748075=>"Gemeente Rijswijk, NL",2748171=>"Gemeente Ridderkerk, NL",6544242=>"Gemeente Albrandswaard, NL",2748184=>"Gemeente Rhenen, NL",2748391=>"Gemeente Putten, NL",6544221=>"Gemeente Pijnacker-Nootdorp, NL",2749233=>"Gemeente Oss, NL",2749447=>"Gemeente Oosterhout, NL",2749643=>"Gemeente Oldenzaal, NL",2749667=>"Gemeente Oldebroek, NL",2749679=>"Gemeente Oisterwijk, NL",2749722=>"Gemeente Oegstgeest, NL",2749752=>"Gemeente Nuth, NL",2749754=>"Gemeente Nunspeet, NL",2749779=>"Gemeente Nuenen, Gerwen en Nederwetten, NL",2750064=>"Gemeente Nijkerk, NL",2750466=>"Gemeente Nederweert, NL",2750520=>"Gemeente Naarden, NL",6544250=>"Gemeente De Ronde Venen, NL",2750883=>"Gemeente Middelharnis, NL",2751036=>"Gemeente Meerssen, NL",2751455=>"Gemeente Loon op Zand, NL",2750450=>"Neerbosch West, NL",2751651=>"Lichtenvoorde, NL",2751770=>"Gemeente Leiderdorp, NL",2751807=>"Gemeente Leerdam, NL",2751833=>"Gemeente Leek, NL",2752263=>"Gemeente Krimpen aan den IJssel, NL",2749931=>"Noorderhoogebrug, NL",2752420=>"Korrewegwijk, NL",2753354=>"Gemeente IJsselstein, NL",2753556=>"Gemeente Houten, NL",2753635=>"Gemeente Hoorn, NL",2753705=>"Gemeente Hoogezand-Sappemeer, NL",2753706=>"Hoogezand, NL",2754065=>"Gemeente Hilvarenbeek, NL",2754072=>"Gemeente Hillegom, NL",2754407=>"Gemeente Hendrik-Ido-Ambacht, NL",2754453=>"Gemeente Hellevoetsluis, NL",2754515=>"Gemeente Heiloo, NL",6544268=>"Gemeente Bernheze, NL",2754680=>"Gemeente Heerde, NL",2754681=>"Heerde, NL",2754816=>"Gemeente Harlingen, NL",2754836=>"Gemeente Harenkarspel, NL",2754840=>"Gemeente Haren, NL",2754846=>"Gemeente Harderwijk, NL",2755270=>"Gemeente Groesbeek, NL",2755463=>"Gemeente Goirle, NL",2755475=>"Gemeente Goes, NL",2755599=>"Gendringen, NL",2755668=>"Gemeente Geertruidenberg, NL",2756058=>"Gemeente Epe, NL",2756076=>"Gemeente Enkhuizen, NL",6544276=>"Gemeente Overbetuwe, NL",2756506=>"Gemeente Duiven, NL",2756538=>"Gemeente Druten, NL",2756558=>"Gemeente Dronten, NL",2756722=>"Gemeente Dongen, NL",2756766=>"Gemeente Doetinchem, NL",2756887=>"Gemeente Diemen, NL",2754552=>"Heijplaat, NL",2757782=>"Gemeente De Bilt, NL",2757849=>"Gemeente Dalfsen, NL",2757873=>"Gemeente Cuijk, NL",2758011=>"Gemeente Capelle aan den IJssel, NL",2758063=>"Gemeente Bussum, NL",2758103=>"Gemeente Bunschoten, NL",2758173=>"Gemeente Brunssum, NL",2758176=>"Gemeente Brummen, NL",2758257=>"Gemeente Waterland, NL",2758459=>"Gemeente Boxtel, NL",2758546=>"Gemeente Boskoop, NL",2758588=>"Gemeente Borsele, NL",2758597=>"Gemeente Borne, NL",2758802=>"Gemeente Bloemendaal, NL",2758997=>"Gemeente Beverwijk, NL",2759039=>"Gemeente Best, NL",2759131=>"Gemeente Bergeijk, NL",2759144=>"Gemeente Bergen op Zoom, NL",2759406=>"Gemeente Barneveld, NL",2759425=>"Gemeente Barendrecht, NL",2759543=>"Gemeente Baarn, NL",6544296=>"Gemeente Aa en Hunze, NL",2759746=>"Anloo, NL",2759886=>"Gemeente Almelo, NL",2759914=>"Gemeente Alblasserdam, NL",2760122=>"Gemeente Aalten, NL",2760133=>"Gemeente Aalsmeer, NL",2786391=>"Sombreffe, BE",2787313=>"Saint-Yvon, BE",2790225=>"Niel, BE",2793070=>"Ledeberg, BE",2793072=>"Ledeberg, BE",2799512=>"Dessel, BE",2800883=>"Brunehault, BE",2802610=>"Basse Lasne, BE",2802851=>"Awans, BE",2803286=>"Alken, BE",2803285=>"Alken, BE",2960314=>"District de Luxembourg, LU",2960801=>"Belval, LU",2960655=>"District de Diekirch, LU",2960161=>"Canton de Redange, LU",6693320=>"Vichten, LU",2960019=>"Vichten, LU",2802361=>"Kingdom of Belgium, BE",3337387=>"Walloon Region, BE",2791993=>"Province du Luxembourg, BE",2800038=>"Coutelle, BE",2796741=>"Province du Hainaut, BE",2792460=>"Le Walestru Septentrional, BE",2792411=>"Province de Liège, BE",2792103=>"Lorentswaldchen, BE",2801791=>"Blier, BE",2790469=>"Province de Namur, BE",2799421=>"Diarbois, BE",2790470=>"Arrondissement de Namur, BE",2802903=>"Sambreville, BE",2794480=>"Keumiée, BE",2784730=>"Vieux Tauve, BE",2784820=>"Arrondissement de Verviers, BE",2784200=>"Waimes, BE",2786340=>"Sourbrodt, BE",2787803=>"Ronce, BE",2801652=>"Bois de Nauwe, BE",6957847=>"Crèvecoeur, BE",2791291=>"Merlin, BE",2785908=>"Cortil, BE",2799866=>"Daublain, BE",3337388=>"Flemish Region, BE",2783770=>"Provincie West-Vlaanderen, BE",2788695=>"Potegem, BE",2750405=>"Kingdom of the Netherlands, NL",2751596=>"Provincie Limburg, NL",2759350=>"Beek, NL",2799867=>"Dassenaarde, BE",2792347=>"Provincie Limburg, BE",2797778=>"Geelberg, BE",2789889=>"Okselaar, BE",2796818=>"Haaselbeekstraat, BE",2796488=>"Arrondissement Hasselt, BE",2792857=>"Leopoldsburg, BE",2792856=>"Leopoldsburg, BE",2797686=>"Genenbemd, BE",2789733=>"Provincie Oost-Vlaanderen, BE",2784323=>"Vrechem, BE",2798648=>"Es, BE",2803136=>"Provincie Antwerpen, BE",2785670=>"Terlo, BE",6544233=>"Gemeente Horst aan de Maas, NL",6544725=>"De Steegh, NL",2743698=>"Provincie Zuid-Holland, NL",2758325=>"Gemeente Brielle, NL",2758326=>"Brielle, NL",2747852=>"Rugge, NL",6544272=>"Gemeente Graafstroom, NL",2758405=>"Brandwijk, NL",2751284=>"Gemeente Maassluis, NL",2751285=>"Maassluis, NL",6544261=>"Gemeente Westland, NL",6544755=>"Baakwoning, NL",2745909=>"Provincie Utrecht, NL",2756685=>"Gemeente Utrechtse Heuvelrug, NL",2754788=>"Haspel, NL",2745911=>"Gemeente Utrecht, NL",2745912=>"Utrecht, NL",2749879=>"Provincie Noord-Holland, NL",2754999=>"Gemeente Haarlemmermeer, NL",2753801=>"Hoofddorp, NL",2759793=>"Gemeente Amsterdam, NL",2753201=>"Jordaan, NL",2750395=>"Gemeente Niedorp, NL",2759397=>"Barsingerhorn, NL",2743882=>"Gemeente Zijpe, NL",2792243=>"L'Obélisque, BE",2746031=>"'t Zand, NL",2759014=>"Gemeente Beuningen, NL",2960696=>"Canton de Capellen, LU",2758461=>"Gemeente Boxmeer, NL",6693317=>"Redange-sur-Attert, LU",2960183=>"Pettingen, LU",2791836=>"Malmaison, BE",2794054=>"Arrondissement Kortrijk, BE",2750630=>"Monster, NL",2751772=>"Gemeente Leiden, NL",2744946=>"Weberbuurt, NL",2783082=>"Zwijndrecht, BE",2790212=>"Nieuwdak, BE",2745675=>"Gemeente Velsen, NL",7118109=>"Velserbroek, NL",2794634=>"Kaulen, BE",2800480=>"Arrondissement de Charleroi, BE",2798298=>"Fleurus, BE",2755634=>"Provincie Gelderland, NL",2758095=>"Gemeente Buren, NL",2746066=>"Tweesluizen, NL",2801463=>"Borgt Driesen, BE",2757344=>"Gemeente Delft, NL",2760055=>"Abtswoude, NL",2744537=>"Gemeente Wieringermeer, NL",2750850=>"Middenmeer, NL",2757990=>"Gemeente Castricum, NL",2757991=>"Castricum, NL",2755249=>"Provincie Groningen, NL",2755204=>"Grootegast, NL",2795774=>"Heisel, BE",2960823=>"Aspelt, LU",7535501=>"Gemeente Zuidplas, NL",2750764=>"Moerkapelle, NL",2802872=>"Avelgem, BE",2801348=>"Bossuit, BE",3319179=>"Provincie Flevoland, NL",2751737=>"Gemeente Lelystad, NL",2751738=>"Lelystad, NL",6544220=>"Gemeente Koggenland, NL",2749745=>"Obdam, NL",2789429=>"Over den Demer, BE",2754658=>"Gemeente Heerhugowaard, NL",7626528=>"agz installatietechniek, NL",2757219=>"Gemeente Den Helder, NL",2753184=>"Julianadorp, NL",6544254=>"Gemeente Drechterland, NL",2745645=>"Venhuizen, NL",2745386=>"Gemeente Vlist, NL",2758652=>"Bonrepas, NL",2744540=>"Gemeente Wieringen, NL",2747083=>"Smerp, NL",2747999=>"Gemeente Roermond, NL",2749563=>"Ool, NL",2793861=>"Kutsegem, BE",2802464=>"Beekhoek, BE",2799644=>"Arrondissement Dendermonde, BE",2800817=>"Buggenhout, BE",2800816=>"Buggenhout, BE",2748838=>"Provincie Overijssel, NL",2755031=>"Gemeente Haaksbergen, NL",2755030=>"Haaksbergen, NL",6693265=>"Kopstal, LU",2960363=>"Kopstal, LU",2744011=>"Provincie Zeeland, NL",2752611=>"Kloosterzande, NL",6544228=>"Oost Gelre, NL",2754808=>"Harreveld, NL",2749990=>"Provincie Noord-Brabant, NL",2758400=>"Gemeente Breda, NL",2755774=>"Gageldonk, NL",2755033=>"Haagse Beemden, NL",3333250=>"Provincie Vlaams-Brabant, BE",2784398=>"Voorheide, BE",2744482=>"Gemeente Wijk bij Duurstede, NL",2744483=>"Wijk bij Duurstede, NL",6544270=>"Gemeente Liesveld, NL",2751924=>"Langerak, NL",2749814=>"Gemeente Noordwijk, NL",2749812=>"Noordwijk-Binnen, NL",2788724=>"Poppel, BE",2756631=>"Provincie Drenthe, NL",2759632=>"Gemeente Assen, NL",2759633=>"Assen, NL",2746623=>"Streefkerk, NL",2744040=>"Gemeente Zandvoort, NL",2744042=>"Zandvoort, NL",2758877=>"Gemeente Bladel en Netersel, NL",2754545=>"Heikant, NL",2756584=>"Driemond, NL",6544881=>"Amsterdam-Zuidoost, NL",2756746=>"Domburg, NL",2960189=>"Perlé, LU",2747372=>"Gemeente ’s-Gravenhage, NL",2748533=>"Poeldijk, NL",2752349=>"Kraaijenstein, NL",2746931=>"Gemeente Spijkenisse, NL",2754491=>"Hekelingen, NL",2758432=>"Braband, NL",2789594=>"Oster, BE",2791536=>"Arrondissement Mechelen, BE",2786642=>"Sint-Katelijne-Waver, BE",2789821=>"Onze-Lieve-Vrouw-Waver, BE",2792481=>"Arrondissement Leuven, BE",2783612=>"Wilsele, BE",2755484=>"Gemeente Goedereede, NL",2749164=>"Ouddorp, NL",6544265=>"Gemeente West Maas en Waal, NL",2758490=>"Boven-Leeuwen, NL",2759706=>"Apeldoorn, NL",2760143=>"Gemeente Aalburg, NL",2755601=>"Genderen, NL",2756139=>"Emmeloord, NL",2800867=>"Bruxelles-Capitale, BE",6693370=>"(Bruxelles-Capitale), BE",2794723=>"Kapelleveld, BE",2748728=>"Gemeente Papendrecht, NL",2748729=>"Papendrecht, NL",6544236=>"Gemeente Lingewaard, NL",2756683=>"Doornenburg, NL",2748370=>"Gemeente Raalte, NL",2748371=>"Raalte, NL",2755002=>"Gemeente Haarlem, NL",2754692=>"Heemstede, NL",2793880=>"Kruisweg, BE",2751435=>"Gemeente Lopik, NL",2745961=>"Uitweg, NL",2755812=>"Provincie Friesland, NL",2746383=>"Gemeente Texel, NL",2749009=>"Oudeschild, NL",2960288=>"Massen, LU",2747033=>"Gemeente Soest, NL",2747034=>"Soest, NL",2759016=>"Beuningen, NL",2789223=>"Peerstalle, BE",2754659=>"Heerhugowaard, NL",2755485=>"Goedereede, NL",2747091=>"Gemeente Smallingerland, NL",2756644=>"Drachten, NL",2748412=>"Gemeente Purmerend, NL",2748413=>"Purmerend, NL",6941548=>"Ypenburg, NL",6544291=>"Gemeente Lansingerland, NL",2758838=>"Bleiswijk, NL",2749368=>"Oosthoekeind, NL",2755357=>"Gemeente Grave, NL",2755358=>"Grave, NL",2799324=>"Dolhain, BE",2756569=>"Drimmelen, NL",2757871=>"Gemeente Culemborg, NL",2757872=>"Culemborg, NL",2757936=>"Gemeente Coevorden, NL",2747182=>"Sleen, NL",2759619=>"Gemeente Asten, NL",2759621=>"Asten, NL",6544300=>"Gemeente Littenseradiel, NL",2753516=>"Húns, NL",6544288=>"Gemeente Cranendonck, NL",2746199=>"Toom, NL",2745321=>"Voorburg, NL",2753012=>"Gemeente Katwijk, NL",2753011=>"Katwijk aan den Rijn, NL",6544232=>"Gemeente Geldrop-Mierlo, NL",2755619=>"Geldrop, NL",2752922=>"Gemeente Kerkrade, NL",2746400=>"Terwinselen, NL",2743855=>"Gemeente Zoetermeer, NL",2743856=>"Zoetermeer, NL",2791495=>"Meerhout, BE",2791494=>"Meerhout, BE",6544273=>"Gemeente Rijnwoude, NL",2754724=>"Hazerswoude-Dorp, NL",2743492=>"Gemeente Zwijndrecht, NL",2754656=>"Heerjansdam, NL",2747868=>"Gemeente Rozenburg, NL",2747870=>"Rozenburg, NL",2756380=>"Een, NL",2758974=>"Biesdonk, NL",2756598=>"Driehuis, NL",6544264=>"Gemeente Roerdalen, NL",2750624=>"Montfort, NL",2751423=>"Gemeente Losser, NL",2751424=>"Losser, NL",2759820=>"Gemeente Amersfoort, NL",2753686=>"Hoogland, NL",2750175=>"Nieuwland, NL",2743986=>"Zegwaart, NL",2803137=>"Arrondissement Antwerpen, BE",2803139=>"Antwerpen, BE",2798873=>"Ekeren, BE",2795099=>"Arrondissement Ieper, BE",2791285=>"Mesen, BE",2792198=>"Loker, BE",6544278=>"Gemeente Eemsmond, NL",2745968=>"Uithuizermeeden, NL",2759898=>"Gemeente Alkmaar, NL",2759899=>"Alkmaar, NL",2744113=>"Gemeente Zaanstad, NL",2744118=>"Zaandam, NL",2749181=>"Gemeente Oud-Beijerland, NL",2749182=>"Oud-Beijerland, NL",2747311=>"Gemeente Simpelveld, NL",2747312=>"Simpelveld, NL",2745972=>"Gemeente Uithoorn, NL",2745973=>"Uithoorn, NL",2757345=>"Delft, NL",2796698=>"Arrondissement Halle-Vilvoorde, BE",2792166=>"Londerzeel, BE",2792165=>"Londerzeel, BE",2754446=>"Gemeente Helmond, NL",2752187=>"Kruisschot, NL",2789244=>"Pauwstraat, BE",2750946=>"Gemeente Meppel, NL",2750947=>"Meppel, NL",2745460=>"Gemeente Vlagtwedde, NL",2751861=>"Laudermarke, NL",2960513=>"District de Grevenmacher, LU",2959966=>"Wollefsmillen, LU",6696267=>"Bollendorf-Pont, LU",2795474=>"Honien Platz, BE",6544256=>"Gemeente Montferland, NL",2755348=>"Greffelkamp, NL",6544298=>"Gemeente Onderbanken, NL",2749307=>"Op den Hering, NL",2745904=>"Gemeente Vaals, NL",2745906=>"Vaals, NL",2756443=>"Gemeente Echt-Susteren, NL",2746988=>"Spaanshuisken, NL",2960739=>"Born, LU",2751773=>"Leiden, NL",2754447=>"Helmond, NL",2759821=>"Amersfoort, NL",2744904=>"Weesp, NL",2745467=>"Vlaardingen, NL",2759798=>"Amstelveen, NL",2751283=>"Maastricht, NL",2759879=>"Almere Stad, NL",2745640=>"Gemeente Venlo, NL",2745641=>"Venlo, NL",2753106=>"Kampen, NL",2756669=>"Dordrecht, NL",2744344=>"Winschoten, NL",2754064=>"Hilversum, NL",2750325=>"Nieuwegein, NL",2756253=>"Eindhoven, NL",2751792=>"Leeuwarden, NL",2745392=>"Vlissingen, NL",2747203=>"Sittard, NL",2754652=>"Heerlen, NL",2755420=>"Gouda, NL",2756136=>"Emmen, NL",2756232=>"Elburg, NL",2756039=>"Ermelo, NL",2751547=>"Lisse, NL",2746839=>"Staphorst, NL",2743608=>"Zutphen, NL",2755584=>"Gennep, NL",2750523=>"Naaldwijk, NL",2745340=>"Volendam, NL",2747373=>"Den Haag, NL",2758621=>"Borger, NL",2758663=>"Bong, NL",2754502=>"Heino, NL",2758086=>"Burgersdijk, NL",2752300=>"Kranenburg, NL",2747225=>"Sint Pancras, NL",6544297=>"Gemeente Midden-Drenthe, NL",2744769=>"Westerbork, NL",2756229=>"Elden, NL",2757374=>"De Laar, NL",2745739=>"Veere, NL",2745932=>"Urk, NL",2756429=>"Ede, NL",6544286=>"Berkelland, NL",2743478=>"Zwolle, NL",2753359=>"IJsselmonde, NL",2792483=>"Leuven, BE",2792482=>"Leuven, BE",2799880=>"Dampremy, BE",2784877=>"Verbrandendijk, BE",2797655=>"Arrondissement Gent, BE",2797657=>"Gent, BE",2797656=>"Gent, BE",2786420=>"Soignies, BE",2783310=>"Zaventem, BE",2803138=>"Antwerpen, BE",3333251=>"Province du Brabant Wallon, BE",2792413=>"Liege, BE",2800865=>"Arrondissement Brussel, BE",2798555=>"Evere, BE",2798554=>"Evere, BE",2786661=>"Sint-Job, BE",6957825=>"Grands Prés, BE",2792568=>"Lessines, BE",2792567=>"Lessines, BE",2784604=>"Vilvoorde, BE",2800931=>"Brugge, BE",2785141=>"Turnhout, BE",2803443=>"Aalter, BE",2791315=>"Merelbeke, BE",2790101=>"Nivelles, BE",2797670=>"Genk, BE",2796491=>"Hasselt, BE",2784935=>"Veldkant, BE",2787025=>"Schoenstraat, BE",2803430=>"Aarschot, BE",2803429=>"Aarschot, BE",2794055=>"Kortrijk, BE",2784821=>"Verviers, BE",2785475=>"Arrondissement Tielt, BE",2789737=>"Oostrozebeke, BE",2790471=>"Namur, BE",2803420=>"Aartselaar, BE",2793549=>"La Hulpe, BE",2793548=>"La Hulpe, BE",2796697=>"Halle, BE",2796696=>"Halle, BE",2792907=>"Le Many, BE",2797862=>"Galgendries, BE",2800439=>"Chaudfontaine, BE",2800438=>"Chaudfontaine, BE",2791062=>"Moleken, BE",2798986=>"Arrondissement Eeklo, BE",2798988=>"Eeklo, BE",2798987=>"Eeklo, BE",2794086=>"Korentje, BE",2786259=>"Spleet, BE",2785340=>"Arrondissement de Tournai, BE",2785342=>"Tournai, BE",2785341=>"Tournai, BE",2785622=>"Tervuren, BE",2803073=>"Arlon, BE",2794479=>"Keur, BE",2788124=>"Reutenbeek, BE",2790506=>"Naarmonts, BE",2799747=>"Deinze, BE",2799746=>"Deinze, BE",2795930=>"Herstal, BE",2799367=>"Diksmuidsepoort, BE",2791344=>"Menen, BE",2791343=>"Menen, BE",2790817=>"Mont Falize, BE",2783802=>"Westerlo, BE",2783801=>"Westerlo, BE",2792007=>"Lummen, BE",2785388=>"Arrondissement Tongeren, BE",2785390=>"Tongeren, BE",2785389=>"Tongeren, BE",2787989=>"Rixensart, BE",2797109=>"Grimde, BE",2784720=>"Vijfhoek, BE",2803084=>"Arendonk, BE",2803083=>"Arendonk, BE",2800431=>"Chaumont-Gistoux, BE",2800430=>"Chaumont-Gistoux, BE",2802961=>"Oudergem, BE",2802960=>"Auderghem, BE",2794022=>"Kraaienbroek, BE",2792189=>"L'Olive, BE",2783755=>"Weverstraat, BE",2795446=>"Hoogbutsel, BE",2787629=>"Roussy, BE",2803319=>"Aisémont, BE",2787093=>"Scheiltjenseinde, BE",2803315=>"Aix-sur-Cloix, BE",2794896=>"Jodoigne, BE",2794895=>"Jodoigne, BE",6957813=>"La Courbette, BE",2803035=>"Asse, BE",2803033=>"Asse, BE",2802583=>"Bastogne, BE",2793087=>"Le Crouzet, BE",2784554=>"Arrondissement de Virton, BE",2798273=>"Florenville, BE",2798272=>"Florenville, BE",2796800=>"Hacboister, BE",2799115=>"Drogenbos, BE",2799114=>"Drogenbos, BE",2786264=>"Spinoy, BE",2795520=>"Hollogne, BE",2790796=>"Montignies-sur-Sambre, BE",2785479=>"Tielt-Winge, BE",2791521=>"Meensel-Kiezegem, BE",2787388=>"Saint-Josse-ten-Noode, BE",2787387=>"Saint-Josse-ten-Noode, BE",2799525=>"Derrière Spai, BE",2786210=>"Stalle, BE",2800064=>"Courcelles, BE",2800063=>"Courcelles, BE",2799137=>"Driesch, BE",2792302=>"Linkebeek, BE",2792301=>"Linkebeek, BE",2798730=>"Ensival, BE",2794719=>"Kapelplaats, BE",2789571=>"Ottignies-Louvain-la-Neuve, BE",2789570=>"Ottignies, BE",2795112=>"Arrondissement de Huy, BE",2795950=>"Héron, BE",2795949=>"Heron, BE",2792035=>"Lubbeek, BE",2792034=>"Lubbeek, BE",2784065=>"Arrondissement de Waremme, BE",2800609=>"Faimes, BE",2798512=>"Faimes, BE",2787931=>"Rodebeek, BE",2789424=>"Overeindeveld, BE",2798800=>"Elvaux, BE",2791963=>"Arrondissement Maaseik, BE",2801094=>"Bree, BE",2801093=>"Bree, BE",2799356=>"Arrondissement de Dinant, BE",2800299=>"Ciney, BE",2800298=>"Ciney, BE",2802434=>"Beersel, BE",2802433=>"Beersel, BE",2801924=>"Bilzen, BE",2795085=>"IJzerhand, BE",2788212=>"Remicourt, BE",2788211=>"Remicourt, BE",2802815=>"Baarleveld, BE",2803205=>"Andenne, BE",2803204=>"Andenne, BE",2799690=>"De Meren, BE",2793992=>"Krekelberg, BE",2783171=>"Zoutleeuw, BE",2783170=>"Zoutleeuw, BE",2801490=>"Boonmerkt, BE",2799646=>"Dendermonde, BE",2786697=>"Sint-Gillis-bij-Dendermonde, BE",2784169=>"Walhain, BE",7648367=>"Walhain, BE",2801619=>"Bois Robert, BE",2789564=>"Oude Baan, BE",2801553=>"Bonance, BE",2802484=>"Beauvechain, BE",2802483=>"Beauvechain, BE",2793004=>"Leffe, BE",2802986=>"Aubel, BE",2802985=>"Aubel, BE",2792884=>"Le Mont, BE",2798895=>"Eindhoven, BE",2794191=>"Koekelberg, BE",2785274=>"Triest, BE",2798579=>"Etterbeek, BE",2798578=>"Etterbeek, BE",2795018=>"Ittre, BE",2795017=>"Ittre, BE",2789493=>"Oud-Heverlee, BE",2789492=>"Oud-Heverlee, BE",2791726=>"Marcinelle, BE",2797672=>"Genistreux, BE",2785516=>"Arrondissement de Thuin, BE",2790698=>"Morlanwelz, BE",2790697=>"Morlanwelz-Mariemont, BE",2788347=>"Ransy, BE",2794192=>"Koekelberg, BE",2794190=>"Koekelberg, BE",2802646=>"Bas Bois, BE",2801612=>"Bosvoorde, BE",2798101=>"Fosses-la-Ville, BE",2798099=>"Fosses-la-Ville, BE",2797129=>"Grez-Doiceau, BE",2797128=>"Grez-Doiceau, BE",2803027=>"Assenede, BE",2803026=>"Assenede, BE",3337389=>"City of Brussels, BE",2793656=>"Laeken, BE",2791835=>"Malmédy, BE",2791834=>"Malmedy, BE",2802838=>"Aywaille, BE",2802837=>"Aywaille, BE",2790866=>"Arrondissement de Mons, BE",2798024=>"Frameries, BE",2798023=>"Frameries, BE",2801201=>"Bouvy, BE",2791273=>"Messancy, BE",2791272=>"Messancy, BE",2796631=>"Hammeveer, BE",2786180=>"Steeghoven, BE",2801507=>"Bonvoisin, BE",2791852=>"Malegem, BE",2789309=>"Paradijs, BE",2801008=>"Broek, BE",2790283=>"Arrondissement de Neufchâteau, BE",2790288=>"Neufchâteau, BE",2790286=>"Neufchateau, BE",2793061=>"Le Dossay, BE",2801859=>"Blankenberge, BE",2801858=>"Blankenberge, BE",2784640=>"Villers-le-Bouillet, BE",2784639=>"Villers-le-Bouillet, BE",2784556=>"Virton, BE",2784555=>"Virton, BE",2802275=>"Bemel, BE",2791664=>"Marloie, BE",2797782=>"Gedinne, BE",2797781=>"Gedinne, BE",2793512=>"La Lonhienne, BE",2788768=>"Ponsin, BE",2799754=>"De Hulst, BE",2791587=>"Mathysart, BE",2801923=>"Binche, BE",2801922=>"Binche, BE",2794855=>"Juprelle, BE",2796584=>"Hannut, BE",2796583=>"Hannut, BE",2791019=>"Sint-Jans-Molenbeek, BE",2791018=>"Molenbeek-Saint-Jean, BE",2801779=>"Bloed Putteken, BE",2803447=>"Arrondissement Aalst, BE",2797639=>"Geraardsbergen, BE",2789439=>"Overboelare, BE",2785336=>"Tournay, BE",2783386=>"Yvoir, BE",2797470=>"Godinne, BE",2790757=>"Mont-Saint-Guibert, BE",2790756=>"Mont-Saint-Guibert, BE",2789014=>"Arrondissement de Philippeville, BE",2798277=>"Florennes, BE",2798276=>"Florennes, BE",2801290=>"Bouge, BE",2797615=>"Gerlimpont, BE",2785930=>"Stuivenberg, BE",2803019=>"Assesse, BE",2803018=>"Assesse, BE",2801153=>"Braine-le-Château, BE",2801152=>"Braine-le-Chateau, BE",2787949=>"Rochefort, BE",2787948=>"Rochefort, BE",2793212=>"L'Aulnois, BE",2803247=>"Amay, BE",2803246=>"Amay, BE",2793167=>"La Ville, BE",2800395=>"Chênée, BE",2798230=>"Fond de Malonne, BE",2792778=>"Le Sart Haguet, BE",2802788=>"Baelen, BE",2802787=>"Baelen, BE",2785563=>"Thimister-Clermont, BE",7732060=>"Thimister-Clermont, BE",2793552=>"La Horne, BE",2789367=>"Padraye, BE",2792852=>"Le Panier, BE",2791616=>"Masnuy-Saint-Jean, BE",2789133=>"Petit Axhe, BE",2789636=>"Oreye, BE",2789583=>"Otrange, BE",2798358=>"Fexhe-le-Haut-Clocher, BE",2798357=>"Fexhe-le-Haut-Clocher, BE",2799318=>"Donceel, BE",2799317=>"Donceel, BE",2792518=>"Le Tilleul, BE",2799998=>"Crisnée, BE",2799997=>"Crisnee, BE",2798728=>"En Sterre, BE",2802607=>"Bassenge, BE",2802606=>"Bassenge, BE",2803009=>"Arrondissement d'Ath, BE",2800329=>"Chièvres, BE",2800328=>"Chievres, BE",2803200=>"Anderlues, BE",2803199=>"Anderlues, BE",2785285=>"Tribotte, BE",2784776=>"Vielsalm, BE",2784775=>"Vielsalm, BE",2798131=>"Forge d'en Haut, BE",2791743=>"Arrondissement de Marche-en-Famenne, BE",2790452=>"Nassogne, BE",2790451=>"Nassogne, BE",2793497=>"La Maladrie, BE",2802511=>"Beaumont, BE",2802510=>"Beaumont, BE",2784995=>"Vaux sous Olne, BE",2784615=>"Villers-Saint-Siméon, BE",2798471=>"Farciennes, BE",2798470=>"Farciennes, BE",2792333=>"Lincent, BE",2792332=>"Lincent, BE",2790809=>"Monthouet, BE",2783152=>"Zulte, BE",2783151=>"Zulte, BE",2786230=>"Stabroek, BE",2786229=>"Stabroek, BE",2800501=>"Chapelle-lez-Herlaimont, BE",2800500=>"Chapelle-lez-Herlaimont, BE",2798637=>"Esneux, BE",2798636=>"Esneux, BE",2790468=>"Nandrin, BE",2790467=>"Nandrin, BE",2803175=>"Anhée, BE",2803174=>"Anhee, BE",2802796=>"Baconfoy, BE",2792441=>"Libin Haut, BE",2792986=>"Léglise, BE",2792985=>"Leglise, BE",2802713=>"Banet Sarts, BE",2785794=>"Tellin, BE",2785793=>"Tellin, BE",2787417=>"Saint-Ghislain, BE",2787416=>"Saint-Ghislain, BE",2796056=>"Hensies, BE",6957776=>"Outre l'Eau, BE",2787964=>"Robertsart, BE",2800688=>"Cacqhus, BE",2785444=>"Tillesse, BE",2785423=>"Tintigny, BE",2785422=>"Tintigny, BE",2783863=>"Wellin, BE",2783862=>"Wellin, BE",2800560=>"Champ Blandais, BE",2787380=>"Saint-Léger, BE",2787378=>"Saint-Leger, BE",2786409=>"Solhez, BE",2796810=>"Habay, BE",8199019=>"Habay, BE",2795239=>"Houyet, BE",2795238=>"Houyet, BE",2802136=>"Bernissart, BE",2802135=>"Bernissart, BE",2798667=>"Erquelinnes, BE",2786399=>"Solre-sur-Sambre, BE",2788766=>"Pont-à-Celles, BE",2784742=>"Viesville, BE",6957761=>"Rivage, BE",2802922=>"Au Pont, BE",2798460=>"Faubourg de Bruxelles, BE",2796630=>"Hamoir, BE",2796629=>"Hamoir, BE",2784003=>"Wastinelle, BE",2797283=>"Grande Fonderie, BE",2799750=>"Deidenberg, BE",2796267=>"Heid de Spa, BE",2801285=>"Bouillenne, BE",2798486=>"Famenne, BE",2789473=>"Ounes, BE",2802789=>"Baegnée, BE",2800449=>"Châtelet, BE",2800448=>"Chatelet, BE",2802997=>"Attert, BE",2798367=>"Ferrières, BE",2798365=>"Ferrieres, BE",2802107=>"Bertrix, BE",2802106=>"Bertrix, BE",2801227=>"Boussu, BE",2801226=>"Boussu, BE",2791642=>"Martelange, BE",2787261=>"Sars-la-Bruyère, BE",2788731=>"Pont Troué, BE",2799226=>"Dour, BE",2789080=>"Petit Hainin, BE",2798593=>"Etalle, BE",2798439=>"Fauvillers, BE",2798438=>"Fauvillers, BE",2800458=>"Chastre, BE",7648268=>"Chastre, BE",2785746=>"Tenneville, BE",2785745=>"Tenneville, BE",2797964=>"Frênes, BE",2789785=>"Arrondissement Oostende, BE",2789788=>"Oostende, BE",2789786=>"Oostende, BE",2799329=>"Doische, BE",2799328=>"Doische, BE",2785873=>"Sur le Try, BE",2802502=>"Beauraing, BE",2802501=>"Beauraing, BE",2794990=>"Jalhay, BE",2794989=>"Jalhay, BE",2800205=>"Comblain-au-Pont, BE",2800204=>"Comblain-au-Pont, BE",2800955=>"Brombais, BE",2785543=>"'t Hoeksken, BE",2797961=>"Frenoit, BE",2785383=>"Tongrenelle, BE",2795114=>"Huy, BE",2795113=>"Huy, BE",2787304=>"Saiwiat, BE",2792652=>"Les Haies, BE",2797599=>"Gerpinnes, BE",2797598=>"Gerpinnes, BE",2783185=>"Zonnebeke, BE",2783184=>"Zonnebeke, BE",2791284=>"Messines, BE",2796483=>"Hastière, BE",2796480=>"Hastière-par-delà, BE",2792401=>"Lierneux, BE",2792400=>"Lierneux, BE",2789355=>"Paliseul, BE",2789354=>"Paliseul, BE",2799879=>"Damré, BE",2795760=>"Hietinne, BE",2785667=>"Terme, BE",2800712=>"Butgenbach, BE",2800711=>"Butgenbach, BE",2791734=>"Marchin, BE",2791733=>"Marchin, BE",2791262=>"Mettet, BE",2791261=>"Mettet, BE",2793295=>"La Redoute, BE",2793092=>"Le Coucou, BE",2799992=>"Croisette, BE",2785594=>"Theux, BE",2785593=>"Theux, BE",2792344=>"Gouvy, BE",2797346=>"Gouvy, BE",2792661=>"Les Golettes, BE",2797582=>"Gesves, BE",2797581=>"Gesves, BE",2759553=>"Gemeente Baarle-Nassau, NL",2759554=>"Baarle-Nassau, NL",2800762=>"Burdinne, BE",2800761=>"Burdinne, BE",2802144=>"Berloz, BE",2802143=>"Berloz, BE",2801155=>"Braine-l'Alleud, BE",2801154=>"Braine-lAlleud, BE",2800025=>"Couvin, BE",2789480=>"Ouffet, BE",2789479=>"Ouffet, BE",2798151=>"Fontery, BE",2798313=>"Flawinne, BE",2787495=>"Ry Massart, BE",2789237=>"Pecq, BE",2789236=>"Pecq, BE",2788445=>"Quiévrain, BE",2788444=>"Quievrain, BE",2800608=>"Celles, BE",2800605=>"Celles, BE",2786388=>"Somme-Leuze, BE",2786387=>"Somme-Leuze, BE",2798202=>"Fonds de Lavois, BE",2801284=>"Bouillon, BE",2801283=>"Bouillon, BE",2795127=>"Hurlugeai, BE",2785258=>"Trieu-de-Wazon, BE",2785194=>"Trou des Nutons, BE",2785080=>"Vache Fontaine, BE",2803149=>"Anthisnes, BE",2803148=>"Anthisnes, BE",2800445=>"Châtelineau, BE",2790749=>"Mont-sur-Marchienne, BE",2802114=>"Bertogne, BE",2802113=>"Bertogne, BE",2791735=>"Marchienne-au-Pont, BE",2797349=>"Goutroux, BE",2791535=>"Maasmechelen, BE",2791961=>"Maasmechelen, BE",2788186=>"Rendeux, BE",2802338=>"Belle Fontaine, BE",2786337=>"Sous le Bois, BE",2800540=>"Champ Laurent, BE",2784870=>"Vergadering, BE",2800357=>"Chertal, BE",2784626=>"Les Bons Villers, BE",2791376=>"Mellet, BE",2791133=>"Modave, BE",2791132=>"Modave, BE",2793741=>"La Bretagne, BE",2791677=>"Mariomont, BE",2786492=>"Sluis, BE",2793280=>"La Roche-en-Ardenne, BE",2793279=>"La Roche-en-Ardenne, BE",2800326=>"Chimay, BE",2800325=>"Chimay, BE",2801950=>"Bièvre, BE",2801949=>"Bievre, BE",2797664=>"Gennevaux, BE",2798666=>"Erquelinnes, BE",2800613=>"Cawette, BE",2789622=>"Orp-Jauche, BE",2789621=>"Orp-le-Grand, BE",2787316=>"Saint-Vith, BE",2787315=>"Saint-Vith, BE",2799898=>"Dalhem, BE",2799897=>"Dalhem, BE",2800220=>"Colfontaine, BE",2802247=>"Berchem-Sainte-Agathe, BE",2794073=>"Kortenaken, BE",2794516=>"Kersbeek-Miskom, BE",2790907=>"Momignies, BE",2790906=>"Momignies, BE",2793723=>"La Calamine, BE",2793722=>"La Calamine, BE",2801691=>"Boffereth, BE",2793124=>"Le But, BE",7647765=>"Ottignies-Louvain-la-Neuve, BE",2802419=>"Beez, BE",2800461=>"Chasse Royale, BE",2789070=>"Petit Howardries, BE",2797484=>"Gobard, BE",2797454=>"Goegnies-Chaussée, BE",2784070=>"Waréchaix, BE",2784312=>"Vresse-sur-Semois, BE",2784311=>"Vresse-sur-Semois, BE",2785456=>"Tihange, BE",2791389=>"Linter, BE",2789615=>"Orsmaal-Gussenhoven, BE",2793464=>"Rouvroy, BE",2796520=>"Harnoncourt, BE",2802359=>"Belgrade, BE",2798361=>"Festingue, BE",2793660=>"La Docherie, BE",2790805=>"Montigny-le-Tilleul, BE",2790804=>"Montigny-le-Tilleul, BE",2789837=>"Oneux, BE",2787968=>"Robermont, BE",2795074=>"Im Grünenthal, BE",2795913=>"Herve, BE",2800575=>"Chaineux, BE",2793323=>"La Perche Rompue, BE",2798674=>"Erpent, BE",2800223=>"Cointe, BE",2793748=>"La Bouverie, BE",2791745=>"Marche-en-Famenne, BE",2784211=>"Waha, BE",2800825=>"Bucquoi, BE",6957760=>"Rivreulle, BE",2800377=>"Chenois, BE",2788449=>"Quevaucamps, BE",2800866=>"Brussels, BE",2797714=>"Gembloux, BE",2797233=>"Grand-Manil, BE",2792552=>"Les Tombes, BE",2796305=>"Heer, BE",2796025=>"Herchies, BE",2789528=>"Arrondissement Oudenaarde, BE",2790540=>"Zwalm, BE",2790399=>"Nederzwalm-Hermelgem, BE",2798565=>"Évegnée-Tignée, BE",2787939=>"Roclenge-sur-Geer, BE",2803195=>"Andoy, BE",2785189=>"Trou du Bois, BE",2802253=>"Berceau, BE",2798310=>"Flémalle, BE",7729847=>"Flémalle, BE",2960316=>"Luxembourg, LU",2960630=>"Echternach, LU",2960599=>"Canton d'Esch-sur-Alzette, LU",2960635=>"Dudelange, LU",2960634=>"Dudelange, LU",6693323=>"Esch-sur-Sûre, LU",2960595=>"Esch-sur-Sûre, LU",6693258=>"Bascharage, LU",2960815=>"Bascharage, LU",2960565=>"Foetz, LU",2960315=>"Canton de Luxembourg, LU",6693279=>"Sandweiler, LU",2960117=>"Sandweiler, LU",2960697=>"Capellen, LU",6693281=>"Steinsel, LU",2960058=>"Steinsel, LU",2960143=>"Rodange, LU",6693282=>"Strassen, LU",2960054=>"Strassen, LU",2960275=>"Canton de Mersch, LU",6693286=>"Bissen, LU",2960759=>"Bissen, LU",2960778=>"Bettembourg, LU",2960777=>"Bettembourg, LU",6693293=>"Mersch, LU",2960276=>"Mersch, LU",6693276=>"Contern, LU",2960673=>"Contern, LU",2960514=>"Canton de Grevenmacher, LU",6693347=>"Wormeldange, LU",2959959=>"Wormeldange, LU",2960795=>"Bereldange, LU",6693275=>"Bertrange, LU",2960782=>"Bertrange, LU",2960800=>"Belvaux, LU",6693341=>"Betzdorf, LU",2960774=>"Betzdorf, LU",2960152=>"Canton de Remich, LU",6693352=>"Mondorf-les-Bains, LU",2960257=>"Mondorf-les-Bains, LU",2960326=>"Livange, LU",6693288=>"Fischbach, LU",2960571=>"Fischbach, LU",6693263=>"Kehlen, LU",2960389=>"Kehlen, LU",6693291=>"Lintgen, LU",2960328=>"Lintgen, LU",6693278=>"Niederanven, LU",2960231=>"Niederanven, LU",6693266=>"Mamer, LU",2960299=>"Mamer, LU",6693269=>"Esch-sur-Alzette, LU",2960596=>"Esch-sur-Alzette, LU",2960589=>"Ettelbruck, LU",2960392=>"Kayl, LU",2960391=>"Kayl, LU",2960067=>"Soleuvre, LU",6693280=>"Schuttrange, LU",2960082=>"Schuttrange, LU",6693271=>"Leudelange, LU",2960335=>"Leudelange, LU",2960618=>"Eischen, LU",2960683=>"Canton de Clervaux, LU",6693297=>"Clervaux, LU",2960684=>"Clervaux, LU",2960103=>"Schifflange, LU",2960102=>"Schifflange, LU",2960160=>"Redange-sur-Attert, LU",6693354=>"Remich, LU",2960154=>"Remich, LU",2960130=>"Roodt-lès-Ell, LU",6693274=>"Roeser, LU",2960135=>"Roeser, LU",2959978=>"Wiltz, LU",2959977=>"Wiltz, LU",2960627=>"Ehlerange, LU",2960644=>"Doncols, LU",6693292=>"Lorentzweiler, LU",2960322=>"Lorentzweiler, LU",6693300=>"Hosingen, LU",2960429=>"Hosingen, LU",6693309=>"Medernach, LU",2960283=>"Medernach, LU",2960472=>"Heisdorf-sur-Alzette, LU",6693308=>"Hoscheid, LU",2960432=>"Hoscheid, LU",2960765=>"Binsfeld, LU",2960468=>"Helmsange, LU",6693277=>"Hesperange, LU",2960457=>"Hesperange, LU",6693264=>"Koerich, LU",2960367=>"Koerich, LU",2960720=>"Bridel, LU",2960124=>"Rumelange, LU",2960123=>"Rumelange, LU",2960652=>"Differdange, LU",2960651=>"Differdange, LU",2960575=>"Findel, LU",6693272=>"Mondercange, LU",2960258=>"Mondercange, LU",2960031=>"Troisvierges, LU",2960030=>"Troisvierges, LU",2960781=>"Bettange-sur-Mess, LU",6693346=>"Mertert, LU",2960271=>"Mertert, LU",2960592=>"Eselborn, LU",2960437=>"Holzem, LU",2960584=>"Fentange, LU",6693344=>"Junglinster, LU",2960400=>"Junglinster, LU",2960020=>"Canton de Vianden, LU",6693333=>"Vianden, LU",2960021=>"Vianden, LU",2959993=>"Weimershof, LU",2960741=>"Bonnevoie, LU",2960188=>"Pétange, LU",2960187=>"Petange, LU",2959973=>"Wilwerdange, LU",2960280=>"Mensdorf, LU",6693296=>"Wincrange, LU",2959971=>"Wincrange, LU",2960293=>"Marnach, LU",2960691=>"Christnach, LU",2960107=>"Schengen, LU",2960829=>"Angelsberg, LU",2960133=>"Rollingen, LU",2960387=>"Keispelt, LU",2960282=>"Medingen, LU",2960370=>"Kockelscheuer, LU",2960531=>"Gosseldange, LU",2960586=>"Fennange, LU",6693283=>"Walferdange, LU",2960010=>"Walferdange, LU",3285197=>"Feulen, LU",2960577=>"Niederfeulen, LU",2960537=>"Goeblange, LU",2960820=>"Asselborn, LU",2960042=>"Tetange, LU",2960533=>"Goetzingen, LU",2960212=>"Oberanven, LU",2960754=>"Blaschette, LU",2960410=>"Itzig, LU",2960672=>"Crauthem, LU",2960200=>"Oetrange, LU",2960084=>"Schrassig, LU",2960144=>"Rippweiler, LU",2960214=>"Nospelt, LU",6693350=>"Dalheim, LU",2960665=>"Dalheim, LU",6693259=>"Clemency, LU",2960687=>"Clemency, LU",2960267=>"Michelau, LU",2960668=>"Cruchten, LU",2959985=>"Welscheid, LU",6693289=>"Heffingen, LU",2960481=>"Heffingen, LU",2960252=>"Moutfort, LU",2960605=>"Ernzen, LU",2960089=>"Schoenfels, LU",2960165=>"Reckange-lès-Mersch, LU",6693316=>"Grosbous, LU",2960510=>"Grosbous, LU",2960628=>"Ehlange, LU",6693290=>"Larochette, LU",2960350=>"Larochette, LU",2960266=>"Michelbouch, LU",2960748=>"Boevange-Clervaux, LU",2960718=>"Brouch, LU",2960209=>"Oberfeulen, LU",2960703=>"Buschdorf, LU",2960008=>"Warken, LU",2960532=>"Gonderange, LU",2960088=>"Schoos, LU",2960420=>"Huncherange, LU",2960331=>"Limpach, LU",2960174=>"Pontpierre, LU",2960294=>"Marienthal, LU",2960281=>"Meispelt, LU",2960415=>"Imbringen, LU",2960111=>"Schandel, LU",2960524=>"Greisch, LU",2960607=>"Ernster, LU",6693273=>"Reckange-sur-Mess, LU",2960164=>"Reckange-sur-Mess, LU",6693261=>"Garnich, LU",2960549=>"Garnich, LU",2960441=>"Hollenfels, LU",2960470=>"Hellange, LU",2960803=>"Beidweiler, LU",2960617=>"Eisenborn, LU",2960083=>"Schrondweiler, LU",2960378=>"Kleinbettingen, LU",2960699=>"Canach, LU",6693343=>"Flaxweiler, LU",2960568=>"Flaxweiler, LU",2960312=>"Machtum, LU",6693312=>"Schieren, LU",2960104=>"Schieren, LU",2960166=>"Rameldange, LU",6693294=>"Nommern, LU",2960216=>"Nommern, LU",2960150=>"Reuland, LU",6693260=>"Dippach, LU",2960648=>"Dippach, LU",2960137=>"Roedgen, LU",2960764=>"Bivange, LU",2960832=>"Alzingen, LU",2960047=>"Syren, LU",2960519=>"Grevels, LU",2960735=>"Bourglinster, LU",2960588=>"Everlange, LU",2960217=>"Noertzange, LU",2960062=>"Stegen, LU",2960649=>"Dillingen, LU",2960799=>"Berbourg, LU",2960816=>"Basbellain, LU",2960501=>"Hachiville, LU",6693267=>"Septfontaines, LU",2960070=>"Septfontaines, LU",2960702=>"Buschrodt, LU",6693349=>"Burmerange, LU",2960704=>"Burmerange, LU",6693295=>"Tuntange, LU",2960027=>"Tuntange, LU",2960226=>"Niederpallen, LU",6693326=>"Heiderscheid, LU",2960478=>"Heiderscheid, LU",2960427=>"Hostert, LU",2960094=>"Schlindermanderscheid, LU",6693319=>"Useldange, LU",2960022=>"Useldange, LU",6693268=>"Steinfort, LU",2960061=>"Steinfort, LU",2960358=>"Lamadelaine, LU",2960797=>"Berchem, LU",6693331=>"Tandel, LU",2960561=>"Fouhren, LU",6693270=>"Frisange, LU",2960556=>"Frisange, LU",2960171=>"Pratz, LU",6693357=>"Wellenstein, LU",2959987=>"Wellenstein, LU",2960843=>"Aciérie, LU",2960228=>"Niedercorn, LU",2960085=>"Schouweiler, LU",6693353=>"Remerschen, LU",2960155=>"Remerschen, LU",2960201=>"Oberwampach, LU",6693356=>"Waldbredimus, LU",2960013=>"Waldbredimus, LU",2960261=>"Moesdorf, LU",2960250=>"Mullendorf, LU",2960469=>"Helmdange, LU",2960576=>"Filsdorf, LU",2960072=>"Senningen, LU",2960746=>"Bofferdange, LU",2960141=>"Rodenbourg, LU",6693340=>"Waldbillig, LU",2960014=>"Waldbillig, LU",2959980=>"Wickrange, LU",6693307=>"Erpeldange, LU",2960601=>"Erpeldange-sur-Sûre, LU",6693318=>"Saeul, LU",2960120=>"Saeul, LU",6693262=>"Hobscheid, LU",2960453=>"Hobscheid, LU",2960539=>"Godbrange, LU",2960833=>"Altwies, LU",6693348=>"Bous, LU",2960732=>"Bous, LU",6693298=>"Consthum, LU",2960674=>"Consthum, LU",2960273=>"Merscheid-lès-Putscheid, LU",2960598=>"Eschdorf, LU",2960491=>"Harlange, LU",2960676=>"Colpach-Haut, LU",2960523=>"Greiveldange, LU",2960145=>"Rippig, LU",2960497=>"Haller, LU",6693305=>"Bourscheid, LU",2960734=>"Bourscheid, LU",6693321=>"Wahl, LU",2960017=>"Wahl, LU",2960414=>"Ingeldorf, LU",2960485=>"Hautcharage, LU",2960399=>"Kahler, LU",2960157=>"Reimberg, LU",2960190=>"Peppange, LU",6693306=>"Ermsdorf, LU",2960608=>"Ermsdorf, LU",6693313=>"Beckerich, LU",2960806=>"Beckerich, LU",2960766=>"Bilsdorf, LU",6693284=>"Weiler-la-Tour, LU",2959996=>"Weiler-la-Tour, LU",2960219=>"Noerdange, LU",2960543=>"Gilsdorf, LU",2960573=>"Fingig, LU",6693335=>"Bech, LU",2960807=>"Bech, LU",2960034=>"Trintange, LU",6693334=>"Beaufort, LU",2960809=>"Beaufort, LU",2960486=>"Hautbellain, LU",2959961=>"Wolwelange, LU",2960195=>"Ospern, LU",2960643=>"Doennange, LU",2960454=>"Hivange, LU",2960327=>"Lipperscheid, LU",2960421=>"Huldange, LU",2960667=>"Dahl, LU",2960583=>"Roudenhaff, LU",6693322=>"Boulaide, LU",2960737=>"Boulaide, LU",2960424=>"Hovelange, LU",6693355=>"Stadtbredimus, LU",2960063=>"Stadtbredimus, LU",2960176=>"Platen, LU",2960780=>"Bettborn, LU",6693330=>"Winseler, LU",2959968=>"Winseler, LU",2960205=>"Oberpallen, LU",2960488=>"Hassel, LU",2960026=>"Uebersyren, LU",2960319=>"Lullange, LU",2960158=>"Reichlange, LU",6693339=>"Rosport, LU",2960127=>"Rosport, LU",6693336=>"Berdorf, LU",2960796=>"Berdorf, LU",3283390=>"Bech-Kleinmacher, LU",2960779=>"Bettel, LU",6693337=>"Consdorf, LU",2960675=>"Consdorf, LU",2959988=>"Welfrange, LU",2960329=>"Linger, LU",6693327=>"Kautenbach, LU",2960393=>"Kautenbach, LU",6693299=>"Heinerscheid, LU",2960475=>"Heinerscheid, LU",2960366=>"Koetschette, LU",2960771=>"Beyren, LU",6693302=>"Weiswampach, LU",2959989=>"Weiswampach, LU",6693351=>"Lenningen, LU",2960337=>"Lenningen, LU",2960706=>"Burden, LU",2960055=>"Stolzembourg, LU",2960108=>"Scheidgen, LU",2960044=>"Tarchamps, LU",2959995=>"Weiler-lès-Putscheid, LU",2960221=>"Nocher, LU",2960813=>"Bastendorf, LU",6693315=>"Rambrouch, LU",2960167=>"Rambrouch, LU",6693311=>"Reisdorf, LU",2960156=>"Reisdorf, LU",2960731=>"Boxhorn, LU",2960822=>"Assel, LU",6693345=>"Manternach, LU",2960296=>"Manternach, LU",2960262=>"Moersdorf, LU",2960563=>"Folschette, LU",2960769=>"Bigonville, LU",2960180=>"Pintsch, LU",2960613=>"Elvange-lès-Burmerange, LU",2960373=>"Knaphoscheid, LU",2960436=>"Holzthum, LU",2960834=>"Altrier, LU",2959983=>"Wemperhardt, LU",6693287=>"Boevange-sur-Attert, LU",2960747=>"Boevange-sur-Attert, LU",2959967=>"Wintrange, LU",2960736=>"Bour, LU",2960413=>"Insenborn, LU",2960149=>"Reuler, LU",2960109=>"Scheidel, LU",2960011=>"Walsdorf, LU",2960638=>"Drauffelt, LU",2960625=>"Ehnen, LU",2960213=>"Nothum, LU",2960814=>"Baschleiden, LU",2960056=>"Stockem, LU",2960660=>"Derenbach, LU",2960194=>"Osweiler, LU",2960641=>"Dorscheid, LU",2960694=>"Cessange, LU",2960016=>"Wahlhausen, LU",2960750=>"Bockholz-lès-Hosingen, LU",6693301=>"Munshausen, LU",2960247=>"Munshausen, LU",2960730=>"Brachtenbach, LU",6693328=>"Neunhausen, LU",2960234=>"Neunhausen, LU",2960139=>"Rodershausen, LU",2960244=>"Nagem, LU",2960136=>"Roedt, LU",2960068=>"Siebenaler, LU",2960318=>"Lultzhausen, LU",6693332=>"Putscheid, LU",2960168=>"Putscheid, LU",2960542=>"Girst, LU",2960784=>"Berschbach, LU",2960844=>"Abweiler, LU",2960045=>"Tandel, LU",2960050=>"Surré, LU",2960140=>"Roder, LU",2960758=>"Bivels, LU",6693329=>"Wilwerwiltz, LU",2959972=>"Wilwerwiltz, LU",2960359=>"Kuborn, LU",2960128=>"Roodt-sur-Syre, LU",2959997=>"Weilerbach, LU",2960343=>"Leithum, LU",2960752=>"Blumenthal, LU",2960278=>"Merkholz, LU",2960600=>"Ersange, LU",2960609=>"Eppeldorf, LU",2960467=>"Hemstal, LU",2960340=>"Lellingen, LU",2960211=>"Oberdonven, LU",6693314=>"Ell, LU",2960616=>"Ell, LU",2960333=>"Liefrange, LU",2960615=>"Ellange, LU",2960700=>"Calmus, LU",2960770=>"Bigelbach, LU",2960324=>"Longsdorf, LU",2960342=>"Lellig, LU",2960504=>"Grundhof, LU",2960000=>"Weidingen, LU",2960517=>"Grevenknapp, LU",2960336=>"Lentzweiler, LU",2960178=>"Pissange, LU",2960112=>"Savelborn, LU",2960114=>"Sassel, LU",2960827=>"Arsdorf, LU",2960243=>"Neidhausen, LU",2960033=>"Troine, LU",2960525=>"Graulinster, LU",2960636=>"Drinklange, LU",2960372=>"Kobenbour, LU",2960450=>"Hoesdorf, LU",2960126=>"Roost, LU",2960614=>"Eltz, LU",2960545=>"Geyershaff, LU",2960564=>"Folkendange, LU",2960611=>"Emerange, LU",2960023=>"Urspelt, LU",2960417=>"Huttange, LU",2960285=>"Mecher, LU",2960738=>"Boudler, LU",2960471=>"Heispelt-lès-Wahl, LU",2960692=>"Château Faubourg, LU",2960418=>"Hupperdange, LU",2960438=>"Holtz, LU",2960597=>"Eschette, LU",2960002=>"Weicherdange, LU",2960035=>"Pommerloch, LU",2960499=>"Hagelsdorf, LU",2960223=>"Niederwampach, LU",2960659=>"Dickweiler, LU",2960722=>"Breidweiler, LU",6693338=>"Mompach, LU",2960259=>"Mompach, LU",2960755=>"Biwisch, LU",2960245=>"Nachtmanderscheid, LU",2960828=>"Ansembourg, LU",2960332=>"Lieler, LU",2960260=>"Moestroff, LU",2960046=>"Tadler, LU",2960006=>"Watrange, LU",2960394=>"Kaundorf, LU",2960610=>"Enscherange, LU",2960494=>"Hamiville, LU",2960447=>"Hoffelt, LU",2959982=>"Weyer, LU",2960506=>"Grumelscheid, LU",2960721=>"Breinert, LU",2960185=>"Petit-Nobressart, LU",2960811=>"Bavigne, LU",2960352=>"Lannen, LU",2959984=>"Welsdorf, LU",2959964=>"Wolpert, LU",2960647=>"Dirbach, LU",2788705=>"Posthoornhoek, BE",2747542=>"Schiphol, NL",2755982=>"Euvelgunne, NL",2749451=>"Oosterhoogebrug, NL",2801484=>"Boortmeerbeek, BE",2801483=>"Boortmeerbeek, BE",2757339=>"Gemeente Delfzijl, NL",2752062=>"Ladysmith, NL",2744256=>"Gemeente Woensdrecht, NL",2752403=>"Korteven, NL",2746004=>"Gemeente Uden, NL",2749735=>"Odiliapeel, NL",2757167=>"De Pan, NL",2756016=>"Espelo, NL",2747030=>"Soesterberg, NL",2759089=>"Berkel, NL",2747597=>"Schiebroek, NL",2751790=>"Gemeente Leeuwarderadeel, NL",2753256=>"Jelsum, NL",6544876=>"Friese Buurt, NL",2755506=>"Gemeente Gilze en Rijen, NL",2755019=>"Haansberg, NL",6544282=>"Gemeente Tynaarlo, NL",2744130=>"Yde, NL",2756341=>"Gemeente Eersel, NL",2747611=>"Scherpenering, NL",2788305=>"Raversijde, BE",2787297=>"Salinus, BE",2786125=>"Steenokkerzeel, BE",2791368=>"Melsbroek, BE",2784234=>"Waasdonk, BE",2783081=>"Zwijndrecht, BE",2783089=>"Zwevegem, BE",2783175=>"Zottegem, BE",2783188=>"Zonhoven, BE",2783204=>"Zoersel, BE",2783274=>"Zemst, BE",2783293=>"Zele, BE",2783308=>"Zedelgem, BE",2783416=>"Wuustwezel, BE",2783632=>"Willebroek, BE",2783759=>"Wevelgem, BE",2783763=>"Wetteren, BE",2783820=>"Wervik, BE",2783941=>"Wavre, BE",2783985=>"Waterloo, BE",2784068=>"Waregem, BE",2785169=>"Tubize, BE",2785364=>"Torhout, BE",2785470=>"Tienen, BE",2785476=>"Tielt, BE",2785612=>"Tessenderlo, BE",2785778=>"Temse, BE",2786087=>"Stekene, BE",2786344=>"Soumagne, BE",2786545=>"Sint-Truiden, BE",2786559=>"Sint-Pieters-Leeuw, BE",2786578=>"Sint-Niklaas, BE",2786641=>"Sint-Katelijne-Waver, BE",2786694=>"Sint-Gillis-Waas, BE",2786700=>"Sint-Genesius-Rode, BE",2786824=>"Seraing, BE",2786963=>"Schoten, BE",2787048=>"Schilde, BE",2787356=>"Saint-Nicolas, BE",2787662=>"Rotselaar, BE",2787889=>"Roeselare, BE",2788088=>"Riemst, BE",2788348=>"Ranst, BE",2788506=>"Puurs, BE",2788521=>"Putte, BE",2788726=>"Poperinge, BE",2788765=>"Pont-a-Celles, BE",2789232=>"Peer, BE",2789413=>"Overijse, BE",2789471=>"Oupeye, BE",2789529=>"Oudenaarde, BE",2789751=>"Oostkamp, BE",2790114=>"Ninove, BE",2790135=>"Nijlen, BE",2790357=>"Neerpelt, BE",2790595=>"Mouscron, BE",2790676=>"Mortsel, BE",2790869=>"Mons, BE",2791067=>"Mol, BE",2791194=>"Middelkerke, BE",2791424=>"Meise, BE",2791537=>"Mechelen, BE",2791857=>"Maldegem, BE",2791964=>"Maaseik, BE",2792073=>"Louvain-la-Neuve, BE",2792179=>"Lommel, BE",2792196=>"Lokeren, BE",2792235=>"Lochristi, BE",2792360=>"Lille, BE",2792397=>"Lier, BE",2793077=>"Lede, BE",2793144=>"Lebbeke, BE",2793446=>"Lanaken, BE",2793508=>"La Louviere, BE",2794070=>"Kortenberg, BE",2794117=>"Kontich, BE",2794166=>"Koksijde, BE",2794210=>"Knokke-Heist, BE",2794663=>"Kasterlee, BE",2794730=>"Kapellen, BE",2794788=>"Kalmthout, BE",2795009=>"Izegem, BE",2795100=>"Ieper, BE",2795261=>"Houthalen, BE",2795398=>"Hoogstraten, BE",2795730=>"Hoboken, BE",2795800=>"Heusden, BE",2795908=>"Herzele, BE",2795912=>"Herve, BE",2796009=>"Herentals, BE",2796012=>"Herent, BE",2796132=>"Helchteren, BE",2796153=>"Heist-op-den-Berg, BE",2796542=>"Harelbeke, BE",2796637=>"Hamme, BE",2796833=>"Haaltert, BE",2797114=>"Grimbergen, BE",2797638=>"Geraardsbergen, BE",2797713=>"Gembloux, BE",2797779=>"Geel, BE",2798297=>"Fleurus, BE",2798301=>"Fleron, BE",2798551=>"Evergem, BE",2798573=>"Eupen, BE",2798615=>"Essen, BE",2799007=>"Edegem, BE",2799090=>"Duffel, BE",2799365=>"Dilbeek, BE",2799369=>"Diksmuide, BE",2799397=>"Diest, BE",2799412=>"Diepenbeek, BE",2799496=>"Destelbergen, BE",2799645=>"Dendermonde, BE",2799647=>"Denderleeuw, BE",2800481=>"Charleroi, BE",2801106=>"Brecht, BE",2801117=>"Brasschaat, BE",2801150=>"Braine-le-Comte, BE",2801447=>"Bornem, BE",2801494=>"Boom, BE",2802031=>"Beveren, BE",2802170=>"Beringen, BE",2802435=>"Beerse, BE",2802743=>"Balen, BE",2803010=>"Ath, BE",2803160=>"Ans, BE",2803448=>"Aalst, BE",2743477=>"Zwolle, NL",2743493=>"Zwijndrecht, NL",2743619=>"Zundert, NL",2743949=>"Zevenaar, NL",2743977=>"Zeist, NL",2743997=>"Zeewolde, NL",2744102=>"Zaltbommel, NL",2744114=>"Zaanstad, NL",2744194=>"Wolvega, NL",2744248=>"Woerden, NL",2744257=>"Woensdrecht, NL",2744324=>"Wisch, NL",2744332=>"Winterswijk, NL",2744514=>"Wijchen, NL",2744549=>"Wierden, NL",2744675=>"Westervoort, NL",2744827=>"Werkendam, NL",2744911=>"Weert, NL",2744991=>"Wassenaar, NL",2745088=>"Wageningen, NL",2745096=>"Waddinxveen, NL",2745123=>"Waalwijk, NL",2745127=>"Waalre, NL",2745154=>"Vught, NL",2745297=>"Voorst, NL",2745301=>"Voorschoten, NL",2745311=>"Voorhout, NL",2745461=>"Vlagtwedde, NL",2745580=>"Vianen, NL",2745634=>"Venray, NL",2745673=>"Velsen-Zuid, NL",2745677=>"Velp, NL",2745706=>"Veldhoven, NL",2745726=>"Veghel, NL",2745774=>"Veenendaal, NL",2745860=>"Valkenswaard, NL",2746005=>"Uden, NL",2746133=>"Tubbergen, NL",2746215=>"Tongelre, NL",2746301=>"Tilburg, NL",2746331=>"Tiel, NL",2746420=>"Terneuzen, NL",2746504=>"Tegelen, NL",2746766=>"Steenwijk, NL",2746804=>"Steenbergen, NL",2746860=>"Stadskanaal, NL",2746932=>"Spijkenisse, NL",2747021=>"Someren, NL",2747063=>"Sneek, NL",2747169=>"Sliedrecht, NL",2747227=>"Sint-Oedenrode, NL",2747351=>"s-Hertogenbosch, NL",2747364=>"s-Gravenzande, NL",2747584=>"Schijndel, NL",2747596=>"Schiedam, NL",2747599=>"Scheveningen, NL",2747720=>"Schagen, NL",2747858=>"Rucphen, NL",2747930=>"Roosendaal, NL",2748000=>"Roermond, NL",2748076=>"Rijswijk, NL",2748172=>"Ridderkerk, NL",2748178=>"Rhoon, NL",2748185=>"Rhenen, NL",2748392=>"Putten, NL",2748591=>"Pijnacker, NL",2749234=>"Oss, NL",2749450=>"Oosterhout, NL",2749644=>"Oldenzaal, NL",2749680=>"Oisterwijk, NL",2749723=>"Oegstgeest, NL",2749753=>"Nuth, NL",2749780=>"Nuenen, NL",2749811=>"Noordwijkerhout, NL",2750053=>"Nijmegen, NL",2750065=>"Nijkerk, NL",2750467=>"Nederweert, NL",2750521=>"Naarden, NL",2750815=>"Mijdrecht, NL",2750884=>"Middelharnis, NL",2751037=>"Meerssen, NL",2751316=>"Maarssen, NL",2751456=>"Loon op Zand, NL",2751582=>"Lindenholt, NL",2751687=>"Leusden, NL",2751771=>"Leiderdorp, NL",2751808=>"Leerdam, NL",2752264=>"Krimpen aan den IJssel, NL",2752923=>"Kerkrade, NL",2753010=>"Katwijk aan Zee, NL",2753355=>"IJsselstein, NL",2753468=>"Huizen, NL",2753557=>"Houten, NL",2753587=>"Horst, NL",2753638=>"Hoorn, NL",2753719=>"Hoogeveen, NL",2753996=>"Hoensbroek, NL",2754066=>"Hilvarenbeek, NL",2754073=>"Hillegom, NL",2754408=>"Hendrik-Ido-Ambacht, NL",2754454=>"Hellevoetsluis, NL",2754516=>"Heiloo, NL",2754635=>"Heesch, NL",2754669=>"Heerenveen, NL",2754697=>"Heemskerk, NL",2754817=>"Harlingen, NL",2754837=>"Harenkarspel, NL",2754841=>"Haren, NL",2754848=>"Harderwijk, NL",2755003=>"Haarlem, NL",2755272=>"Groesbeek, NL",2755434=>"Gorinchem, NL",2755464=>"Goirle, NL",2755476=>"Goes, NL",2755633=>"Geldermalsen, NL",2755669=>"Geertruidenberg, NL",2756077=>"Enkhuizen, NL",2756161=>"Elst, NL",2756342=>"Eersel, NL",2756507=>"Duiven, NL",2756539=>"Druten, NL",2756559=>"Dronten, NL",2756619=>"Driebergen-Rijsenburg, NL",2756723=>"Dongen, NL",2756767=>"Doetinchem, NL",2756888=>"Diemen, NL",2756987=>"Deventer, NL",2757220=>"Den Helder, NL",2757340=>"Delfzijl, NL",2757347=>"Delfshaven, NL",2757783=>"De Bilt, NL",2757850=>"Dalfsen, NL",2757874=>"Cuijk, NL",2757890=>"Cranendonck, NL",2758012=>"Capelle aan den IJssel, NL",2758007=>"Capelle-West, NL",2758064=>"Bussum, NL",2758104=>"Bunschoten, NL",2758174=>"Brunssum, NL",2758177=>"Brummen, NL",2758245=>"Broek op Langedijk, NL",2758258=>"Broek in Waterland, NL",2758460=>"Boxtel, NL",2758547=>"Boskoop, NL",2758587=>"Borssele, NL",2758598=>"Borne, NL",2758602=>"Born, NL",2758765=>"Bodegraven, NL",2758804=>"Bloemendaal, NL",2758878=>"Bladel, NL",2758998=>"Beverwijk, NL",2759040=>"Best, NL",2759113=>"Bergschenhoek, NL",2759132=>"Bergeyk, NL",2759145=>"Bergen op Zoom, NL",2759178=>"Benthuizen, NL",2759407=>"Barneveld, NL",2759426=>"Barendrecht, NL",2759544=>"Baarn, NL",2759661=>"Arnhem, NL",2759875=>"Alphen aan den Rijn, NL",2759887=>"Almelo, NL",2759915=>"Alblasserdam, NL",2760123=>"Aalten, NL",2760134=>"Aalsmeer, NL",6929992=>"Berkel en Rodenrijs, NL",2783144=>"Zutendaal, BE",2783143=>"Zutendaal, BE",2783154=>"Zuienkerke, BE",2783153=>"Zuienkerke, BE",2783196=>"Zomergem, BE",2783195=>"Zomergem, BE",2783216=>"Zingem, BE",2783215=>"Zingem, BE",2783279=>"Zelzate, BE",2783307=>"Zeebrugge, BE",2783348=>"Zandhoven, BE",2783347=>"Zandhoven, BE",2783385=>"Yvoir, BE",2783463=>"Wommelgem, BE",2783462=>"Wommelgem, BE",2783587=>"Wingene, BE",2783586=>"Wingene, BE",2783685=>"Wijnegem, BE",2783684=>"Wijnegem, BE",2783718=>"Wielsbeke, BE",2783729=>"Wichelen, BE",2783728=>"Wichelen, BE",2783855=>"De Haan, BE",2783854=>"Wenduine, BE",2783865=>"Wellen, BE",2783864=>"Wellen, BE",2783871=>"Welkenraedt, BE",2783870=>"Welkenraedt, BE",2784011=>"Wasseiges, BE",2784010=>"Wasseiges, BE",2797322=>"Waremme, BE",2784066=>"Waremme, BE",2784093=>"Wanze, BE",2784092=>"Wanze, BE",2784168=>"Walhain-Saint-Paul, BE",2784228=>"Wachtebeke, BE",2784227=>"Wachtebeke, BE",2784232=>"Waasmunster, BE",2784231=>"Waasmunster, BE",2784238=>"Waarschoot, BE",2784237=>"Waarschoot, BE",2784371=>"Vorselaar, BE",2784370=>"Vorselaar, BE",2784642=>"Villers-la-Ville, BE",2784641=>"Villers-la-Ville, BE",2784859=>"Verlaine, BE",2784856=>"Verlaine, BE",2784999=>"Vaux-sur-Sûre, BE",2784994=>"Vaux-sur-Sure, BE",2798035=>"Trooz, BE",2785208=>"Trooz, BE",2784103=>"Trois-Ponts, BE",2785223=>"Trois-Ponts, BE",2785294=>"Tremelo, BE",2785293=>"Tremelo, BE",2786426=>"Tinlot, BE",2785427=>"Tinlot, BE",2785518=>"Thuin, BE",2785517=>"Thuin, BE",2785656=>"Ternat, BE",2785654=>"Ternat, BE",2785990=>"Stoumont, BE",2785989=>"Stoumont, BE",2786124=>"Steenokkerzeel, BE",2786186=>"Stavelot, BE",2786185=>"Stavelot, BE",2786227=>"Staden, BE",2786226=>"Staden, BE",2786244=>"Sprimont, BE",2786242=>"Sprimont, BE",2786319=>"Spa, BE",2786318=>"Spa, BE",2786390=>"Sombreffe, BE",2786592=>"Sint-Martens-Latem, BE",2786591=>"Sint-Martens-Latem, BE",2786604=>"Lierde, BE",2786603=>"Sint-Maria-Lierde, BE",2786617=>"Sint-Lievens-Houtem, BE",2786616=>"Sint-Lievens-Houtem, BE",2786627=>"Sint-Laureins, BE",2786626=>"Sint-Laureins, BE",2786747=>"Sint-Amands, BE",2786746=>"Sint-Amands, BE",2786770=>"Silly, BE",2786769=>"Silly, BE",2786853=>"Seneffe, BE",2786852=>"Seneffe, BE",2787081=>"Schelle, BE",2787080=>"Schelle, BE",2787409=>"Saint-Hubert, BE",2787407=>"Saint-Hubert, BE",2803245=>"Sainte-Ode, BE",2787435=>"Sainte-Ode, BE",2787522=>"Rumst, BE",2787521=>"Rumst, BE",2787531=>"Rumes, BE",2787530=>"Rumes, BE",2787548=>"Ruiselede, BE",2787547=>"Ruiselede, BE",2787622=>"Rouvroy, BE",2787878=>"Roeulx, BE",2788052=>"Rijkevorsel, BE",2788051=>"Rijkevorsel, BE",2788139=>"Retie, BE",2788138=>"Retie, BE",2788300=>"Rebecq-Rognon, BE",2788299=>"Rebecq-Rognon, BE",2788312=>"Ravels, BE",2788382=>"Ramillies, BE",2788381=>"Ramillies, BE",2788411=>"Raeren, BE",2788410=>"Raeren, BE",2788448=>"Quévy, BE",2788446=>"Quevy-le-Petit, BE",2788578=>"Profondeville, BE",2788577=>"Profondeville, BE",2797697=>"Plombières, BE",2788849=>"Plombieres, BE",2788926=>"Pittem, BE",2788925=>"Pittem, BE",2789017=>"Philippeville, BE",2789016=>"Philippeville, BE",2789158=>"Perwez, BE",2789156=>"Perwez, BE",2789191=>"Pepinster, BE",2789190=>"Pepinster, BE",2789193=>"Pepingen, BE",2789192=>"Pepingen, BE",2789404=>"Overpelt, BE",2789403=>"Overpelt, BE",2789484=>"Oud-Turnhout, BE",2789483=>"Oud-Turnhout, BE",2789520=>"Oudenburg, BE",2789519=>"Oudenburg, BE",2789635=>"Oreye, BE",2789655=>"Opwijk, BE",2789654=>"Opwijk, BE",2789715=>"Opglabbeek, BE",2789714=>"Opglabbeek, BE",2789772=>"Oosterzele, BE",2789771=>"Oosterzele, BE",2789835=>"Onhaye, BE",2789834=>"Onhaye, BE",2789870=>"Olne, BE",2789869=>"Olne, BE",2789887=>"Olen, BE",2789886=>"Olen, BE",2789909=>"Ohey, BE",2789908=>"Ohey, BE",2790153=>"Nieuwpoort, BE",2790150=>"Nieuwpoort, BE",2790181=>"Nieuwerkerken, BE",2790226=>"Niel, BE",2790236=>"Nevele, BE",2790235=>"Nevele, BE",2790435=>"Nazareth, BE",2790433=>"Nazareth, BE",2790516=>"Musson, BE",2790515=>"Musson, BE",2790730=>"Moorslede, BE",2790729=>"Moorslede, BE",2791122=>"Moerbeke, BE",2791120=>"Moerbeke, BE",2791256=>"Meulebeke, BE",2791255=>"Meulebeke, BE",2791298=>"Merksplas, BE",2791297=>"Merksplas, BE",2791324=>"Merchtem, BE",2791323=>"Merchtem, BE",2791330=>"Merbes-le-Château, BE",2791329=>"Merbes-le-Chateau, BE",2791386=>"Melle, BE",2791385=>"Melle, BE",2791420=>"Meix-devant-Virton, BE",2791419=>"Meix-devant-Virton, BE",2797227=>"Manhay, BE",2791790=>"Manhay, BE",2791953=>"Machelen, BE",2791951=>"Machelen, BE",2792058=>"Lovendegem, BE",2792057=>"Lovendegem, BE",2792246=>"Lobbes, BE",2792245=>"Lobbes, BE",2792294=>"Lint, BE",2792292=>"Lint, BE",2792349=>"Limbourg, BE",2792348=>"Limbourg, BE",2792424=>"Liedekerke, BE",2792430=>"Lichtervelde, BE",2792428=>"Lichtervelde, BE",2792443=>"Libin, BE",2792442=>"Libin, BE",2792872=>"Lens, BE",2792871=>"Lens, BE",2792880=>"Lendelede, BE",2792879=>"Lendelede, BE",2793068=>"Ledegem, BE",2793067=>"Ledegem, BE",2793430=>"Landen, BE",2793429=>"Landen, BE",2793798=>"Laarne, BE",2793797=>"Laarne, BE",2793858=>"Kuurne, BE",2793857=>"Kuurne, BE",2793908=>"Kruishoutem, BE",2793907=>"Kruishoutem, BE",2793941=>"Kruibeke, BE",2793940=>"Kruibeke, BE",2794064=>"Kortessem, BE",2794063=>"Kortessem, BE",2794072=>"Kortenaken, BE",2794075=>"Kortemark, BE",2794074=>"Kortemark, BE",2794195=>"Koekelare, BE",2794194=>"Koekelare, BE",2794224=>"Knesselare, BE",2794223=>"Knesselare, BE",2794446=>"Kinrooi, BE",2794445=>"Kinrooi, BE",2794620=>"Keerbergen, BE",2794619=>"Keerbergen, BE",2794708=>"Kaprijke, BE",2794707=>"Kaprijke, BE",2794725=>"Kapelle-op-den-Bos, BE",2794724=>"Kapelle-op-den-Bos, BE",2794764=>"Kampenhout, BE",2794763=>"Kampenhout, BE",2794853=>"Jurbise, BE",2794852=>"Jurbise, BE",2794854=>"Juprelle, BE",2795001=>"Jabbeke, BE",2794999=>"Jabbeke, BE",2795057=>"Ingelmunster, BE",2795056=>"Ingelmunster, BE",2795064=>"Incourt, BE",2795063=>"Incourt, BE",2795107=>"Ichtegem, BE",2795106=>"Ichtegem, BE",2795171=>"Hulshout, BE",2795170=>"Hulshout, BE",2795185=>"Huldenberg, BE",2795184=>"Huldenberg, BE",2795233=>"Hove, BE",2795232=>"Hove, BE",2795256=>"Houthulst, BE",2795255=>"Houthulst, BE",2795323=>"Houffalize, BE",2795322=>"Houffalize, BE",2795338=>"Hotton, BE",2795337=>"Hotton, BE",2795424=>"Hooglede, BE",2795423=>"Hooglede, BE",2795512=>"Holsbeek, BE",2795511=>"Holsbeek, BE",2795649=>"Hoeselt, BE",2795648=>"Hoeselt, BE",2795700=>"Hoeilaart, BE",2795699=>"Hoeilaart, BE",2795704=>"Hoegaarden, BE",2795703=>"Hoegaarden, BE",2795934=>"Herselt, BE",2795933=>"Herselt, BE",2795957=>"Herne, BE",2795956=>"Herne, BE",2795986=>"Herk-de-Stad, BE",2795985=>"Herk-de-Stad, BE",2796006=>"Herenthout, BE",2796005=>"Herenthout, BE",2796038=>"Herbeumont, BE",2796037=>"Herbeumont, BE",2796086=>"Hemiksem, BE",2796298=>"Heers, BE",2796297=>"Heers, BE",2796370=>"Havelange, BE",2796369=>"Havelange, BE",2796482=>"Hastiere-Lavaux, BE",2796628=>"Hamois, BE",2796627=>"Hamois, BE",2796716=>"Halen, BE",2796715=>"Halen, BE",2796807=>"Habay-la-Vieille, BE",2796845=>"Haacht, BE",2796844=>"Haacht, BE",2797095=>"Grobbendonk, BE",2797094=>"Grobbendonk, BE",2797418=>"Gooik, BE",2797417=>"Gooik, BE",2797501=>"Glabbeek-Zuurbemde, BE",2797500=>"Glabbeek-Zuurbemde, BE",2797518=>"Gistel, BE",2797517=>"Gistel, BE",2797524=>"Gingelom, BE",2797523=>"Gingelom, BE",2797695=>"Genappe, BE",2797694=>"Genappe, BE",2797762=>"Geetbets, BE",2797761=>"Geetbets, BE",2797770=>"Geer, BE",2797799=>"Gavere, BE",2797798=>"Gavere, BE",2797852=>"Galmaarden, BE",2797851=>"Galmaarden, BE",2797938=>"Froidchapelle, BE",2797937=>"Froidchapelle, BE",2797980=>"Frasnes-lez-Anvaing, BE",2797979=>"Frasnes-lez-Buissenal, BE",2786584=>"Voeren, BE",2798056=>"Sint-Pieters-Voeren, BE",2798280=>"Floreffe, BE",2798279=>"Floreffe, BE",2798291=>"Flobecq, BE",2798290=>"Flobecq, BE",2798598=>"Estinnes, BE",2798597=>"Estinnes-au-Val, BE",2798605=>"Estaimpuis, BE",2798604=>"Estaimpuis, BE",2798699=>"Érezée, BE",2798698=>"Erezee, BE",2790241=>"Neupré, BE",2798743=>"Engis, BE",2798748=>"Enghien, BE",2798747=>"Enghien, BE",2798837=>"Ellezelles, BE",2798836=>"Ellezelles, BE",2798950=>"Éghezée, BE",2798949=>"Eghezee, BE",2791740=>"Écaussinnes, BE",2799024=>"Ecaussinnes-dEnghien, BE",2799048=>"Durbuy, BE",2799047=>"Durbuy, BE",2799348=>"Dison, BE",2799347=>"Dison, BE",2799359=>"Dinant, BE",2799357=>"Dinant, BE",2799571=>"De Pinte, BE",2799570=>"De Pinte, BE",2799578=>"De Panne, BE",2799577=>"De Panne, BE",2799587=>"Dentergem, BE",2799586=>"Dentergem, BE",2799779=>"De Haan, BE",2799797=>"Deerlijk, BE",2799852=>"Daverdisse, BE",2799851=>"Daverdisse, BE",2799886=>"Damme, BE",2799885=>"Damme, BE",2800043=>"Court-Saint-Étienne, BE",2800042=>"Court-Saint-Etienne, BE",2800270=>"Clavier, BE",2800269=>"Clavier, BE",2800321=>"Chiny, BE",2800320=>"Chiny, BE",2800457=>"Chastre-Villeroux-Blanmont, BE",2800592=>"Cerfontaine, BE",2801104=>"Bredene, BE",2801103=>"Bredene, BE",2801148=>"Braives, BE",2801147=>"Braives, BE",2801216=>"Boutersem, BE",2801215=>"Boutersem, BE",2801439=>"Borsbeek, BE",2801468=>"Borgloon, BE",2801467=>"Borgloon, BE",2801540=>"Bonheiden, BE",2801744=>"Boechout, BE",2801743=>"Boechout, BE",2801755=>"Bocholt, BE",2801754=>"Bocholt, BE",2801820=>"Blegny, BE",2801999=>"Bierbeek, BE",2801998=>"Bierbeek, BE",2802016=>"Beyne-Heusay, BE",2802015=>"Beyne-Heusay, BE",2802124=>"Bertem, BE",2802123=>"Bertem, BE",2802155=>"Berlare, BE",2802154=>"Berlare, BE",2802157=>"Berlaar, BE",2802156=>"Berlaar, BE",2802294=>"Beloeil, BE",2802292=>"Beloeil, BE",2802375=>"Bekkevoort, BE",2802374=>"Bekkevoort, BE",2802406=>"Begijnendijk, BE",2802405=>"Begijnendijk, BE",2802438=>"Beernem, BE",2802437=>"Beernem, BE",2802817=>"Baarle-Hertog, BE",2802816=>"Baarle-Hertog, BE",2802871=>"Avelgem, BE",2802991=>"Aubange, BE",2802990=>"Aubange, BE",2802996=>"Attert, BE",2803054=>"As, BE",2803053=>"As, BE",2803092=>"Ardooie, BE",2803091=>"Ardooie, BE",2803131=>"Anzegem, BE",2803130=>"Anzegem, BE",2803145=>"Antoing, BE",2803144=>"Antoing, BE",2803243=>"Amblève, BE",2803242=>"Ambleve, BE",2803253=>"Alveringem, BE",2803252=>"Alveringem, BE",2803324=>"Aiseau-Presles, BE",6640087=>"Frasnes-lez-Anvaing, BE",7648533=>"Lennik, BE",2784365=>"Laakdal, BE",2787061=>"Scherpenheuvel-Zichem, BE",7668893=>"Scherpenheuvel-Zichem, BE",2759880=>"Almere-Haven, NL",2960007=>"Wasserbillig, LU",2960115=>"Sanem, LU",2960197=>"Olm, LU",2960207=>"Obercorn, LU",2960270=>"Mertzig, LU",2960515=>"Grevenmacher, LU",2960657=>"Diekirch, LU",2960680=>"Colmar, LU",2960775=>"Bettendorf, LU",2960791=>"Bergem, LU",2743561=>"Zwartebroek, NL",2743590=>"De Westereen, NL",2743680=>"Zuidlaren, NL",2743694=>"Zuidhorn, NL",2743877=>"Zijtaart, NL",2743952=>"Zetten, NL",2743963=>"Zelhem, NL",2744014=>"Zeeland, NL",2744122=>"Zaamslag, NL",2744129=>"Yerseke, NL",2744147=>"Walterswald, NL",2744153=>"Woudsend, NL",2744156=>"Woudrichem, NL",2744163=>"Woudenberg, NL",2744179=>"Workum, NL",2744191=>"Wommels, NL",2744219=>"Wolfheze, NL",2744327=>"Wirdum, NL",2744337=>"Winsum, NL",2744338=>"Winsum, NL",2744373=>"Wilp, NL",2744388=>"Willemstad, NL",2744489=>"Wijhe, NL",2744608=>"West-Terschelling, NL",2744741=>"Westerhaar-Vriezenveensewijk, NL",2744800=>"Westdorpe, NL",2744859=>"Wellerlooi, NL",2744869=>"Welberg, NL",2744871=>"Wekerom, NL",2744994=>"Waspik, NL",2744998=>"Warten, NL",2745003=>"Warnsveld, NL",2745018=>"Wergea, NL",2745048=>"Wanneperveen, NL",2745169=>"Vroomshoop, NL",2745182=>"Vriezenveen, NL",2745189=>"Vries, NL",2745196=>"Vreeland, NL",2745271=>"Voorthuizen, NL",2745333=>"Vollenhove, NL",2745338=>"Volkel, NL",2745369=>"Voerendaal, NL",2745382=>"Vlodrop, NL",2745388=>"Vlist, NL",2745743=>"Feanwalden, NL",2745800=>"Varsseveld, NL",2745874=>"Valkenburg, NL",2745885=>"Valburg, NL",2745892=>"Vaassen, NL",2745926=>"Urmond, NL",2745944=>"Ulrum, NL",2745978=>"Uitgeest, NL",2746009=>"Uddel, NL",2746014=>"Ubachsberg, NL",2746023=>"Tzummarum, NL",2746024=>"Tzum, NL",2746038=>"Tynaarlo, NL",2746051=>"Twijzelerheide, NL",2746052=>"Twijzel, NL",2746060=>"Twello, NL",2746120=>"Tuk, NL",2746304=>"Tijnje, NL",2746311=>"Tytsjerk, NL",2746354=>"t Hofke, NL",2746407=>"Terschuur, NL",2746424=>"Ternaard, NL",2746557=>"Surhuizum, NL",2746558=>"Surhuisterveen, NL",2746565=>"Sumar, NL",2746596=>"Stroe, NL",2746705=>"Stiens, NL",2746748=>"Stein, NL",2746752=>"Steggerda, NL",2746761=>"Steenwijkerwold, NL",2746974=>"Spankeren, NL",2746981=>"Spakenburg, NL",2747010=>"Son, NL",2747109=>"Sluiskil, NL",2747143=>"Slochteren, NL",2747229=>"Sint Odilienberg, NL",2747231=>"Sint Nicolaasga, NL",2747234=>"Sint-Michielsgestel, NL",2747262=>"Sint Jansklooster, NL",2747270=>"Sint Jacobiparochie, NL",2747290=>"Sint Anthonis, NL",2747297=>"Sint Annaparochie, NL",2747348=>"Sibbe, NL",2747357=>"s-Heerenberg, NL",2747371=>"s Gravenmoer, NL",2747380=>"Sexbierum, NL",2747506=>"Schoonhoven, NL",2747515=>"Schoonebeek, NL",2747550=>"Schinveld, NL",2747551=>"Schin op Geul, NL",2747553=>"Schinnen, NL",2747671=>"Scharnegoutum, NL",2747680=>"Scharendijke, NL",2747704=>"Schalkhaar, NL",2747713=>"Schaijk, NL",2747749=>"Sas van Gent, NL",2747751=>"Sassenheim, NL",2747758=>"Sappemeer, NL",2747835=>"Ruinen, NL",2747865=>"Rozendaal, NL",2747886=>"Rottevalle, NL",2747938=>"Reduzum, NL",2748051=>"Rinsumageast, NL",2748104=>"Rijnsburg, NL",2748201=>"Rheden, NL",2748208=>"Reuver, NL",2748236=>"Renkum, NL",2748240=>"Renesse, NL",2748280=>"Reeuwijk, NL",2748286=>"Reek, NL",2748329=>"Randwijk, NL",2748403=>"Puth, NL",2748481=>"Posterholt, NL",2748864=>"Overberg, NL",2748926=>"Oud-Loosdrecht, NL",2748968=>"Oude Wetering, NL",2748979=>"Oudewater, NL",2749007=>"Oudeschoot, NL",2749017=>"Ouderkerk aan de Amstel, NL",2749034=>"Oude Pekela, NL",2749061=>"Oudemirdum, NL",2749120=>"Oudehaske, NL",2749136=>"Oudega, NL",2749203=>"Otterlo, NL",2749281=>"Opperdoes, NL",2749284=>"Oppenhuizen, NL",2749286=>"Opmeer, NL",2749304=>"Opeinde, NL",2749334=>"Oost-Vlieland, NL",2749423=>"Oosterpark, NL",2749430=>"Eastermar, NL",2749449=>"Oosterhout, NL",2749503=>"Oosterbeek, NL",2749513=>"Oostelbeers, NL",2749626=>"Olst, NL",2749653=>"Oldemarkt, NL",2749660=>"Oldehove, NL",2749669=>"Aldeboarn, NL",2749683=>"Oirschot, NL",2749685=>"Oirsbeek, NL",2749696=>"Offenbeek, NL",2749703=>"Oerle, NL",2749708=>"Oentsjerk, NL",2749712=>"Oene, NL",2749807=>"Noordwolde, NL",2749875=>"Noordhorn, NL",2749992=>"Noardburgum, NL",2750117=>"Nieuw-Vossemeer, NL",2750157=>"Nieuw-Lotbroek, NL",2750158=>"Nieuw-Loosdrecht, NL",2750163=>"Nieuw-Lekkerland, NL",2750187=>"Nieuwkoop, NL",2750194=>"Nieuw-Helvoet, NL",2750280=>"Nieuwenhoorn, NL",2750310=>"Nieuwehorne, NL",2750417=>"Nes, NL",2750420=>"Nes, NL",2750444=>"Neerijnen, NL",2750460=>"Neede, NL",2750479=>"Nederhemert-Noord, NL",2750480=>"Nederhemert, NL",2750626=>"Montfoort, NL",2750641=>"Monnickendam, NL",2750790=>"Minnertsga, NL",2750810=>"Mijnsheerenland, NL",2750821=>"Mierlo, NL",2750903=>"Middelbeers, NL",2750938=>"Merkelbeek, NL",2750965=>"Menaam, NL",2750978=>"Melick, NL",2751122=>"Marsum, NL",2751129=>"Marrum, NL",2751161=>"Markelo, NL",2751180=>"Mariarade, NL",2751193=>"Mariaheide, NL",2751199=>"Margraten, NL",2751212=>"Mantgum, NL",2751253=>"Makkum, NL",2751254=>"Makkum, NL",2751264=>"Magele, NL",2751296=>"Maasdijk, NL",2751301=>"Maasbree, NL",2751303=>"Maasbracht, NL",2751318=>"Maarsbergen, NL",2751320=>"Maarn, NL",2751385=>"Lunteren, NL",2751436=>"Lopik, NL",2751449=>"Loosbroek, NL",2751524=>"Loenen, NL",2751537=>"Lochem, NL",2751599=>"Limbricht, NL",2751621=>"Liesveld, NL",2751641=>"Lienden, NL",2751709=>"Lepelstraat, NL",2751750=>"Leimuiden, NL",2751789=>"Leeuwen, NL",2751793=>"Leesten, NL",2751801=>"Leersum, NL",2751874=>"Laren, NL",2751875=>"Laren, NL",2751980=>"Landsmeer, NL",2752130=>"Kwintsheul, NL",2752151=>"Kunrade, NL",2752192=>"Kruisland, NL",2752367=>"Koudum, NL",2752409=>"Kortenhoef, NL",2752441=>"Kootwijkerbroek, NL",2752444=>"Kootstertille, NL",2752492=>"Koningsbosch, NL",2752520=>"Kollumerzwaag, NL",2752524=>"Kollum, NL",2752547=>"Koewacht, NL",2752600=>"Klundert, NL",2752646=>"Klimmen, NL",2752798=>"Klazienaveen, NL",2752950=>"Marken, NL",2752969=>"Keldonk, NL",2753197=>"Joure, NL",2753307=>"Jirnsum, NL",2753379=>"IJlst, NL",2753470=>"Huizum, NL",2753548=>"Houthem, NL",2753806=>"Honselersdijk, NL",2753852=>"Holwerd, NL",2753887=>"Hollum, NL",2754111=>"Heythuysen, NL",2754287=>"Heteren, NL",2754322=>"Herveld, NL",2754352=>"Herkenbosch, NL",2754389=>"Hengevelde, NL",2754507=>"Heinenoord, NL",2754618=>"Heeze, NL",2754662=>"Heerewaarden, NL",2754682=>"Heer, NL",2754703=>"Heelsum, NL",2754712=>"Heeg, NL",2754719=>"Hedel, NL",2754752=>"Havelte, NL",2754768=>"Hattem, NL",2754779=>"Hasselt, NL",2754804=>"Harskamp, NL",2754821=>"Harkema, NL",2754835=>"Harfsen, NL",2754864=>"Hurdegaryp, NL",2754922=>"Halsteren, NL",2754930=>"Hallum, NL",2754975=>"Haelen, NL",2754978=>"Haastrecht, NL",2755009=>"Haaren, NL",2755023=>"Haamstede, NL",2755052=>"Grou, NL",2755317=>"Grijpskerk, NL",2755399=>"Goutum, NL",2755428=>"Gorssel, NL",2755429=>"Gorredijk, NL",2755449=>"Goor, NL",2755517=>"Giethoorn, NL",2755533=>"Giessenburg, NL",2755542=>"Gytsjerk, NL",2755645=>"Geffen, NL",2755729=>"Garyp, NL",2755732=>"Garderen, NL",2755845=>"Franeker, NL",2755920=>"Ferwert, NL",2756035=>"Erp, NL",2756050=>"Epse, NL",2756072=>"Ens, NL",2756114=>"Emst, NL",2756133=>"Emmer-Compascuum, NL",2756169=>"Elsloo, NL",2756200=>"Ellecom, NL",2756283=>"Eijsden, NL",2756301=>"Egmond aan Zee, NL",2756349=>"Eerde, NL",2756351=>"Eerbeek, NL",2756418=>"Eefde, NL",2756426=>"Ederveen, NL",2756431=>"Edam, NL",2756438=>"Echtenerbrug, NL",2756451=>"Dwingeloo, NL",2756504=>"Duivendrecht, NL",2756561=>"Dronryp, NL",2756567=>"Drogeham, NL",2756591=>"Driel, NL",2756642=>"Drachtstercompagnie, NL",2756673=>"Doorwerth, NL",2756686=>"Doorn, NL",2756759=>"Dokkum, NL",2756772=>"Doesburg, NL",2756774=>"Doenrade, NL",2756787=>"Dirksland, NL",2756800=>"Dinteloord, NL",2756878=>"Diepenheim, NL",2757041=>"De Steeg, NL",2757194=>"Den Oever, NL",2757244=>"Den Burg, NL",2757336=>"De Lier, NL",2757370=>"Delden, NL",2757475=>"Deinum, NL",2757493=>"De Horst, NL",2757685=>"De Doornakkers, NL",2757838=>"Damwald, NL",2757937=>"Coevorden, NL",2758081=>"Burgh, NL",2758106=>"Bunnik, NL",2758131=>"Buitenpost, NL",2758186=>"Bruinisse, NL",2758239=>"Broeksterwald, NL",2758275=>"Broekhem, NL",2758309=>"Britsum, NL",2758333=>"Breukelen, NL",2758336=>"Breugel, NL",2758391=>"Bredeweg, NL",2758393=>"Bredevoort, NL",2758500=>"Boven-Hardinxveld, NL",2758549=>"Boskamp, NL",2758626=>"Borculo, NL",2758633=>"Boornbergum, NL",2758682=>"Bolsward, NL",2758748=>"Boelenslaan, NL",2758759=>"Boekel, NL",2758777=>"Blokzijl, NL",2758803=>"Bloemendaal, NL",2758868=>"Blaricum, NL",2758895=>"Burdaard, NL",2758930=>"Bilgaard, NL",2758992=>"Biddinghuizen, NL",2759057=>"Berltsum, NL",2759103=>"Burgum, NL",2759126=>"Berghem, NL",2759129=>"Bergharen, NL",2759147=>"Berg en Dal, NL",2759163=>"Berg, NL",2759164=>"Berg, NL",2759197=>"Bennekom, NL",2759199=>"Bennebroek, NL",2759286=>"Beesel, NL",2759342=>"Beekbergen, NL",2759356=>"Bedum, NL",2759429=>"Barchem, NL",2759472=>"Balk, NL",2759581=>"Baak, NL",2759594=>"Axel, NL",2759611=>"Augustinusga, NL",2759684=>"Arcen, NL",2759687=>"Appingedam, NL",2759757=>"Anjum, NL",2759781=>"Andelst, NL",2759796=>"Amstenrade, NL",2759828=>"Amerongen, NL",2759878=>"Almkerk, NL",2759884=>"Almen, NL",2759922=>"Akkrum, NL",2759961=>"Aduard, NL",2760144=>"Aalburg, NL",6251994=>"Klundert, NL",6324403=>"Camminghaburen, NL",6692371=>"Jubbega, NL",6695503=>"Meerhoven, NL",6697854=>"De Knipe, NL",6698635=>"Lunetten, NL",6698718=>"Aarle-Rixtel, NL",6929953=>"Kelpen-Oler, NL",6929980=>"Muschberg en Geestenberg, NL",6929984=>"Villapark, NL",6929985=>"Lakerlopen, NL",6948945=>"Oranjewijk, NL",6948946=>"Vondelwijk, NL",6948947=>"Groenswaard, NL",6950811=>"Randenbroek, NL",7870365=>"Hoogkamp, NL",7870372=>"Gulden Bodem, NL",7870373=>"Sterrenberg, NL",7870374=>"Burgemeesterswijk, NL",2751536=>"Gemeente Lochem, NL",2798798=>"Elverdinge, BE",2802243=>"Berendrecht, BE",2755862=>"Fort, NL",2757698=>"Dedemsvaart, NL",2784102=>"Wanne, BE",2755996=>"Etten-Leur, NL",2750888=>"Middeldijk, NL"];
asort($data);
foreach ($data as $id => $name) {
  echo '<option value="' . $id . '"' . ($name=="Gouda, NL"?" selected":"") . '>' . $name . '</option>';
}

?>
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
      <option value="km/h" selected>Kilometers per hour (km/h)</option>
    </select>
  </div>
</div>

<script>
  "use strict";
  var e, i, data, filename = "<?php echo basename(__FILE__); ?>", loaded = false;

  // Check browser support
  if (typeof(Storage) === "undefined" || typeof(XMLHttpRequest) === "undefined" || !("classList" in document.createElement("div"))) {
    alert("Your browser don't support localStorage, XMLHttpRequest or HTML5 classList API!");
  }

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
    e.open("GET", filename + "?q=" + localStorage.weather_location);
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
     document.querySelector("#weather_clouds").innerHTML = format_number(data.weather.clouds) + " %";

     document.querySelector("#weather_sunrise").innerHTML = format_time(data.weather.sunrise * 1e3);
     document.querySelector("#weather_sunset").innerHTML = format_time(data.weather.sunset * 1e3);

     if (localStorage.night_mode == "yes") {
       document.body.classList.add("night");
     } else {
       document.body.classList.remove("night");
     }
  }
</script>
