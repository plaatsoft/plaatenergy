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
			echo add_icons('./');
		?>
		
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
		<link rel="stylesheet" type="text/css" href="./main.css"/>
                <script language="JavaScript" src="../js/link.js" type="text/javascript"></script>
	</head>
	<body>
               <form id="plaatenergy" method="POST" action='../.'>
               
		<div class="shadow" onclick="close_all_sidebars();"></div>
		<div class="bg"></div>
		
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
						<option value="0">English</option>
						<option value="1">Nederlands</option>
					</select>
				</div>
				<div class="item">
					<div class="header toggle">
						<span id="t_sidebars_settings_refresh"></span>
						<input id="enableRefresh_toggle" type="checkbox">
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
						<input id="weather_toggle" type="checkbox">
						<div class="switch"></div>
					</div>
				</div>
				<div class="item">
					<div class="header toggle">
						<span id="t_sidebars_settings_sunrise"></span>
						<input id="sunrise_toggle" type="checkbox">
						<div class="switch"></div>
					</div>
				</div>
				<div class="item">
					<div class="header">
						<span id="t_sidebars_settings_background"></span>
					</div>
					<div class="image-box">
						<div class="page upload">
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
								<path d="M9 16h6v-6h4l-7-7-7 7h4zm-4 2h14v2H5z"/>
							</svg>
							<p id="t_sidebars_settings_background_upload"></p>
							<input type="file" accept="image/*" id="background-loader">
						</div>
						<div id="bg1" class="page image">
							<div id="bg2"></div>
							<p id="bg_name">lalal.jpg</p>
							<svg class="close" onclick="close_bg();" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
								<path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
							</svg>
							<svg class="download" onclick="download_bg();" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
								<path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/>
							</svg>
						</div>
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
				<div class="tile normal live bottom-top">
					<div class="a orange">
						<svg class="close" onclick="close_bg();" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
							<path d="M9 11H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2zm2-7h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V9h14v11z"/>
						</svg>
						<div id="date" class="text"></div>
						<div id="t_tiles_date" class="label"></div>
					</div>
					<div class="b bluegray">
						<svg class="close" onclick="close_bg();" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
							<path d="M6.76 4.84l-1.8-1.79-1.41 1.41 1.79 1.79 1.42-1.41zM4 10.5H1v2h3v-2zm9-9.95h-2V3.5h2V.55zm7.45 3.91l-1.41-1.41-1.79 1.79 1.41 1.41 1.79-1.79zm-3.21 13.7l1.79 1.8 1.41-1.41-1.8-1.79-1.4 1.4zM20 10.5v2h3v-2h-3zm-8-5c-3.31 0-6 2.69-6 6s2.69 6 6 6 6-2.69 6-6-2.69-6-6-6zm-1 16.95h2V19.5h-2v2.95zm-7.45-3.91l1.41 1.41 1.79-1.8-1.41-1.41-1.79 1.8z"/>
						</svg>
						<div class="text">PlaatEnergy</div>
						<div class="label"><span id="t_tiles_made"></span> <div class="link" onclick="window.open('http://www.plaatsoft.nl');">PlaatSoft</div></div>
					</div>
				</div>
				<div class="tile normal live three">
					<div class="a indigo">
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
							<path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zM12.5 7H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
						</svg>
						<div id="time" class="text"></div>
						<div id="t_tiles_time" class="label"></div>
					</div>
					<div id="weather_sunrise_tile" class="b indigo700">
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
							<path d="M256,95c8.833,0,16-7.167,16-16V47c0-8.833-7.167-16-16-16s-16,7.167-16,16v32C240,87.833,247.167,95,256,95z M380.438,137.167l22.625-22.625c6.249-6.25,6.249-16.375,0-22.625c-6.25-6.25-16.375-6.25-22.625,0l-22.625,22.625c-6.25,6.25-6.25,16.375,0,22.625S374.188,143.417,380.438,137.167z M64,255h32c8.833,0,16-7.167,16-16s-7.167-16-16-16H64c-8.833,0-16,7.167-16,16S55.167,255,64,255z M400,239c0,8.833,7.167,16,16,16h32c8.833,0,16-7.167,16-16s-7.167-16-16-16h-32C407.167,223,400,230.167,400,239z M131.541,137.167c6.251,6.25,16.376,6.25,22.625,0c6.251-6.25,6.251-16.375,0-22.625l-22.625-22.625c-6.25-6.25-16.374-6.25-22.625,0c-6.25,6.25-6.25,16.375,0,22.625L131.541,137.167z M145.625,255h32c-1.062-5.167-1.625-10.521-1.625-16c0-44.188,35.812-80,80-80c44.188,0,80,35.812,80,80c0,5.479-0.562,10.833-1.625,16h32c0.792-5.271,1.625-10.521,1.625-16c0-61.75-50.25-112-112-112s-112,50.25-112,112C144,244.479,144.875,249.729,145.625,255z M448,287H64c-8.833,0-16,7.167-16,16s7.167,16,16,16h384c8.833,0,16-7.167,16-16S456.833,287,448,287z M448,351H64c-8.833,0-16,7.167-16,16s7.167,16,16,16h384c8.833,0,16-7.167,16-16S456.833,351,448,351z M448,415H64c-8.833,0-16,7.167-16,16s7.167,16,16,16h384c8.833,0,16-7.167,16-16S456.833,415,448,415z"/>
						</svg>
						<div id="weather_sunrise" class="text"></div>
						<div id="t_tiles_sunrise" class="label"></div>
					</div>
					<div id="weather_sunset_tile" class="c indigo900">
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
							<path d="M64,319.001h384c8.833,0,16-7.167,16-16s-7.167-16-16-16H64c-8.833,0-16,7.167-16,16S55.167,319.001,64,319.001z M448,351.001H64c-8.833,0-16,7.167-16,16s7.167,16,16,16h384c8.833,0,16-7.167,16-16S456.833,351.001,448,351.001z M448,415.001H64c-8.833,0-16,7.167-16,16s7.167,16,16,16h384c8.833,0,16-7.167,16-16S456.833,415.001,448,415.001z M205.042,255c-0.333-0.333-0.751-0.542-1.083-0.875c-37.438-37.438-37.438-98.334,0-135.792c2.562-2.542,5.27-4.958,8.146-7.208c-2.251,35.958,10.729,71.375,37.104,97.729c26.374,26.396,61.79,39.333,97.749,37.083c-2.667,3.396-5.874,6.125-8.896,9.062h41.521c5.042-8.042,9.25-16.75,12.583-26.063c2.021-5.854,0.542-12.333-3.833-16.667c-4.291-4.354-10.792-5.854-16.624-3.791c-35.376,12.499-73.646,3.979-99.875-22.25c-26.251-26.25-34.792-64.521-22.251-99.896c2.083-5.812,0.583-12.271-3.791-16.646c-4.334-4.354-10.813-5.833-16.625-3.771c-18.521,6.542-34.625,16.604-47.833,29.792C138.125,138.917,132.479,205.479,164.062,255H205.042z"/>
						</svg>
						<div id="weather_sunset" class="text"></div>
						<div id="t_tiles_sunset" class="label"></div>
					</div>
				</div>

				<div class="tile normal brown" onclick='<?php echo 'link("pid='.PAGE_DAY_TEMPERATURE.'&date='.date('Y-m-d').'");'?>'>
					<div id="temperature" class="text"></div>
					<div id="t_tiles_temperature" class="label"></div>
				</div>
				<div class="tile normal blue" onclick='<?php echo 'link("pid='.PAGE_DAY_PRESSURE.'&date='.date('Y-m-d').'");'?>'>
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
				
				<div class="tile normal live top-bottom">
				        <div class="a gray" onclick='<?php echo 'link("pid='.PAGE_DAY_IN_GAS.'&date='.date('Y-m-d').'");'?>'>
						<div id="gas_today" class="text"></div>
						<div id="t_tiles_gas_today" class="label"></div>
					</div>
				        <div class="b deeppurple" onclick='<?php echo 'link("pid='.PAGE_YEAR_IN_GAS.'&eid='.EVENT_M3.'&date='.date('Y').'");'?>'>
						<div id="total_gas" class="text"></div>
						<div id="t_tiles_total_gas" class="label"></div>
					</div>
				</div>
				
				<div class="tile normal live left-right">
				        <div class="a red" onclick='<?php echo 'link("pid='.PAGE_YEAR_IN_ENERGY.'&eid='.EVENT_KWH.'&date='.date('Y').'");'?>'>
						<div id="total_decrease" class="text"></div>
						<div id="t_tiles_total_decrease" class="label"></div>
					</div>
				        <div class="b green" onclick='<?php echo 'link("pid='.PAGE_YEAR_OUT_ENERGY.'&eid='.EVENT_KWH.'&date='.date('Y').'");'?>'>
						<div id="total_delivery" class="text"></div>
						<div id="t_tiles_total_delivery" class="label"></div>
					</div>
				</div>
			</div>
			<div class="col">
				<div class="tile normal purple" onclick='<?php echo 'link("pid='.PAGE_DAY_HUMIDITY.'&date='.date('Y-m-d').'");'?>'>
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
				
				<div class="tile normal teal">
					<div id="total_tree_offset" class="text"></div>
					<div id="t_tiles_tree" class="label"></div>
				</div>
				
				<div class="tile normal live right-left">
					<div class="a deeppurple">
						<div id="total_energy_co2" class="text"></div>
						<div id="t_tiles_energy_co2" class="label"></div>
					</div>
					<div class="b green">
						<div id="total_gas_co2" class="text"></div>
						<div id="t_tiles_gas_co2" class="label"></div>
					</div>
				</div>
				
			</div>
			<div class="col">
				<div class="tile normal brown">
					<div class="hn">N</div>
					<svg id="weather_wind_arrow" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
						<path d="M4 12l1.41 1.41L11 7.83V20h2V7.83l5.58 5.59L20 12l-8-8-8 8z"/>
					</svg>
					<div id="weather_wind_speed" class="text"></div>
					<div id="t_tiles_w_wind" class="label"></div>
				</div>
				<div class="tile normal blue">
					<div id="weather_temperature" class="text"></div>
					<div id="t_tiles_w_temperature" class="label"></div>
				</div>
				<div class="tile normal orange">
					<div id="weather_pressure" class="text"></div>
					<div id="t_tiles_w_pressure" class="label"></div>
				</div>
				<div class="tile normal teal">
					<div id="weather_humidity" class="text"></div>
					<div id="t_tiles_w_humidity" class="label"></div>
				</div>
			</div>
		</div>
		
		<!---------------------------------------
		------ JAVASCRIPT PART (js folder) ------
		---------------------------------------->
		
		<!-- JS config -->
		<script>
			// The JS config object for JS settings
			var config = {
				// The standart settings (USERS CAN ALWAYS CHANGE THE SETTINGS!!!)
				preferences: {
					language: 0,          // 0 -> English     | 1 -> Dutch
					gasUnit: 0,           // 0 -> m3          | 1 -> dm3
					windUnit: 1,          // 0 -> m/s         | 1 -> km/h
					timeFormat: 1,        // 0 -> 04:33:12 PM | 1 -> 16:33:12
					numbersFormat: 1,     // 0 -> 1,234.56    | 1 -> 1.234,56
					loadWeatherData: 1,   // 0 -> no          | 1 -> yes
					temperatureFormat: 0, // 0 -> Celcius     | 1 -> Fahrenheit | 2 -> Kelvin
					showSunrise: 1,       // 0 -> no          | 1 -> yes
					enableRefresh: 1,     // 0 -> no          | 1 -> yes
					tileUpdateTime: 5     // Globel update time (affected not the time and weather) (in s)
				},
				
				// Need to get access to the weather data
				weather: {
					appID: "2de143494c0b295cca9337e1e96b00e0", // Need to send a request to http://api.openweathermap.org
					place: "Gouda,nl",                         // The name of the city where you live
					updateTime: 15000                          // The time the weather information must be reload (in ms)
				}
			};
		</script>
		
		<!-- Load the JS script files -->
		<script src="./js/functions.js"></script>
		<script src="./js/updateWeatherData.js"></script>
		<script src="./js/init.js"></script>
		
		<!-- Run the JS with try -->
		<script>
			// Try to run the script
			//try {
				init();
			/*}
			
			// If an error report
			catch (error) {
				alert(error);
				console.error(error);
			}*/
		</script>
        </form>
	</body>
</html>
