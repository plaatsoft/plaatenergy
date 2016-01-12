<?php
	include '../general.inc';
	
	function random_color () {
		$color = ["blue", "green", "pink", "brown", "red", "bluegray", "purple", "teal", "orange", "indigo", "deeppurple", "gray"];
		return $color[rand(0, (count($color) - 1))];
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<?php
			add_icons('./');
		?>
		
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
		
		<link rel="stylesheet" type="text/css" href="./main.css"/>
	</head>
	<body>
		<div class="shadow" onclick="close_all_sidebars();"></div>
		
		<div id="cookie" class="popup <?php echo random_color(); ?>">
			<svg class="close" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" onclick="close_all_popups();">
				<path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
			</svg>
			
			<svg class="left" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
				<path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/>
			</svg>
			<div class="right">
				<h1 id="t_popup_cookies_h"></h1>
				<p id="t_popup_cookies_p"></p>
			</div>
		</div>
		
		<div id="settings" class="sidebar">
			<div class="header">
				<div id="t_sidebars_settings_header" class="label"></div>
				<div id="t_sidebars_settings_header_more" class="label more"></div>
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" onclick="close_all_sidebars();">
					<path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
				</svg>
			</div>
			<div class="body">
				<div class="item button" onclick="sidebar_show_body('#more_settings');">
					<div class="header">
						<span id="t_sidebars_settings_more"></span>
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
							<path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/>
						</svg>
					</div>
				</div>
				<div class="item">
					<div class="header" id="t_sidebars_settings_language"></div>
					<select id="lang_selector">
						<option id="en" value="en">English</option>
						<option id="nl" value="nl">Nederlands</option>
					</select>
				</div>
				<div class="item">
					<div class="header toggle">
						<span id="t_sidebars_settings_refresh"></span>
						<input id="refresh_toggle" type="checkbox">
						<div class="switch"></div>
					</div>
					<div id="range_min" class="range_helper"></div>
					<input id="refresh" type="range" step="0.1" min="0.1" max="10">
					<div id="range_max" class="range_helper a"></div>
					<div id="range_value" class="range_helper"></div>
				</div>
				<div class="item">
					<div class="header toggle">
						<span id="t_sidebars_settings_weather"></span>
						<input id="weather" type="checkbox">
						<div class="switch"></div>
					</div>
				</div>
				<div class="item">
					<div class="header toggle">
						<span id="t_sidebars_settings_sunrise"></span>
						<input id="show_sunrise" type="checkbox">
						<div class="switch"></div>
					</div>
				</div>
				<div class="item">
					<div class="header toggle">
						<span id="t_sidebars_settings_background"></span>
						<input id="background" type="checkbox">
						<div class="switch"></div>
					</div>
					<input type="file">
					<?php
						for ($i = 0; $i < 5; $i++) {
							echo '<div class="image-box"><div class="content">' . $i . '</div></div>';
						}
					?>
					<div class="image-box">
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
							<path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
						</svg>
						<input type="file">
					</div>
				</div>
			</div>
			<div id="more_settings" class="body more">
				<div class="item button" onclick="sidebar_show_body_cancel();">
					<div class="header">
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
							<path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/>
						</svg>
						<span id="t_sidebars_settings_less"></span>
					</div>
				</div>
				<div class="item">
					<div class="header" id="t_sidebars_settings_numbers"></div>
					<select id="numbers_selector">
						<option id="t_sidebars_settings_numbers_0" value="0"></option>
						<option id="t_sidebars_settings_numbers_1" value="1"></option>
					</select>
				</div>
				<div class="item">
					<div class="header" id="t_sidebars_settings_time"></div>
					<select id="time_selector">
						<option id="t_sidebars_settings_time_0" value="0"></option>
						<option id="t_sidebars_settings_time_1" value="1"></option>
					</select>
				</div>
				<div class="item">
					<div id="t_sidebars_settings_gas" class="header"></div>
					<select id="gas_selector">
						<option id="t_sidebars_settings_gas_0" value="0"></option>
						<option id="t_sidebars_settings_gas_1" value="1"></option>
					</select>
				</div>
				<div class="item">
					<div id="t_sidebars_settings_temperature" class="header"></div>
					<select id="temperature_selector">
						<option id="celcius" value="0">Celcius (&deg;C)</option>
						<option id="fahrenheit" value="1">Fahrenheit (&deg;F)</option>
						<option id="kelvin" value="2">Kelvin (K)</option>
					</select>
				</div>
				<div class="item">
					<div id="t_sidebars_settings_wind" class="header"></div>
					<select id="wind_selector">
						<option id="t_sidebars_settings_wind_0" value="0"></option>
						<option id="t_sidebars_settings_wind_1" value="1"></option>
					</select>
				</div>
			</div>
		</div>
		
		<div class="grid">
			<div class="col">
				<div class="tile normal orange">
					<div id="date" class="text"></div>
					<div id="t_tiles_date" class="label"></div>
				</div>
				<div class="tile normal indigo">
					<div id="time" class="text"></div>
					<div id="w_sunrise" class="sunrise"></div>
					<div id="w_sunset" class="sunset"></div>
					<div id="t_tiles_time" class="label"></div>
				</div>
				<div class="tile normal brown" onclick="window.location = '../day_temperature.php';">
					<div id="temperature" class="text"></div>
					<div id="t_tiles_temperature" class="label"></div>
				</div>
				<div class="tile normal blue" onclick="window.location = '../day_pressure.php';">
					<div id="pressure" class="text"></div>
					<div id="t_tiles_pressure" class="label"></div>
				</div>
			</div>
			<div class="col">
				<div id="energy_today" class="tile normal">
					<div id="energy_today_text" class="text"></div>
					<div id="t_tiles_energy_today" class="label"></div>
				</div>
				<div id="current_watt" class="tile normal">
					<div id="current_watt_text" class="text"></div>
					<div id="t_tiles_current_watt" class="label"></div>
				</div>
				<div class="tile normal gray" onclick="window.location = '../day_in_gas.php';">
					<div id="gas_today" class="text"></div>
					<div id="t_tiles_gas_today" class="label"></div>
				</div>
				<div class="tile normal indigo" onclick="window.location = '../year_in_gas.php';">
					<div id="total_gas" class="text"></div>
					<div id="t_tiles_total_gas" class="label"></div>
				</div>
			</div>
			<div class="col">
				<div class="tile normal purple" onclick="window.location = '../day_huminity.php';">
					<div id="humidity" class="text"></div>
					<div id="t_tiles_humidity" class="label"></div>
				</div>
				<div class="tile small pink" onclick="open_sidebar('#settings');">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
						<path d="M19.43 12.98c.04-.32.07-.64.07-.98s-.03-.66-.07-.98l2.11-1.65c.19-.15.24-.42.12-.64l-2-3.46c-.12-.22-.39-.3-.61-.22l-2.49 1c-.52-.4-1.08-.73-1.69-.98l-.38-2.65C14.46 2.18 14.25 2 14 2h-4c-.25 0-.46.18-.49.42l-.38 2.65c-.61.25-1.17.59-1.69.98l-2.49-1c-.23-.09-.49 0-.61.22l-2 3.46c-.13.22-.07.49.12.64l2.11 1.65c-.04.32-.07.65-.07.98s.03.66.07.98l-2.11 1.65c-.19.15-.24.42-.12.64l2 3.46c.12.22.39.3.61.22l2.49-1c.52.4 1.08.73 1.69.98l.38 2.65c.03.24.24.42.49.42h4c.25 0 .46-.18.49-.42l.38-2.65c.61-.25 1.17-.59 1.69-.98l2.49 1c.23.09.49 0 .61-.22l2-3.46c.12-.22.07-.49-.12-.64l-2.11-1.65zM12 15.5c-1.93 0-3.5-1.57-3.5-3.5s1.57-3.5 3.5-3.5 3.5 1.57 3.5 3.5-1.57 3.5-3.5 3.5z"/>
					</svg>
					<div id="t_tiles_settings" class="label"></div>
				</div>
				<div class="tile small ws bluegray" onclick="window.location = '../';">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
						<path d="M10.09 15.59L11.5 17l5-5-5-5-1.41 1.41L12.67 11H3v2h9.67l-2.58 2.59zM19 3H5c-1.11 0-2 .9-2 2v4h2V5h14v14H5v-4H3v4c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z"/>
					</svg>
					<div id="t_tiles_exit" class="label"></div>
				</div>
				<div class="tile normal red" onclick="window.location = '../year_in_kwh.php';">
					<div id="total_decrease" class="text"></div>
					<div id="t_tiles_total_decrease" class="label"></div>
				</div>
				<div class="tile normal green" onclick="window.location = '../year_out_kwh.php';">
					<div id="total_delivery" class="text"></div>
					<div id="t_tiles_total_delivery" class="label"></div>
				</div>
			</div>
			<div class="col">
				<div class="tile normal brown">
					<div id="w_wind_speed" class="text"></div>
					<div id="t_tiles_w_wind" class="label"></div>
				</div>
				<div class="tile normal blue">
					<div id="w_temperature" class="text"></div>
					<div id="t_tiles_w_temperature" class="label"></div>
				</div>
				<div class="tile normal orange">
					<div id="w_pressure" class="text"></div>
					<div id="t_tiles_w_pressure" class="label"></div>
				</div>
				<div class="tile normal teal last">
					<div id="w_humidity" class="text"></div>
					<div id="t_tiles_w_humidity" class="label"></div>
				</div>
			</div>
		</div>
		
		<svg class="loader" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 128 128">
			<circle cx="64" cy="64" r="59"></circle>
		</svg>
		
		<script src="./main.js"></script>
	</body>
</html>
