// Update the weather data
var updateWeatherData = function (weather) {
	if (COOKIE.get("wind") == 0) {
		var wind_speed = NUM(weather.wind.speed) + " m/s";
	} else {
		var wind_speed = NUM(weather.wind.speed / 3.6) + " km/h";
	}
	
	document.querySelector("#weather_wind_speed").innerHTML = wind_speed;
	
	document.querySelector("#weather_wind_arrow").style.transform = "rotate(" + weather.wind.deg + "deg)";
	
	document.querySelector("#weather_sunrise").innerHTML = DATE(weather.sys.sunrise);
	document.querySelector("#weather_sunset").innerHTML = DATE(weather.sys.sunset);
	
	if (COOKIE.get("temperature") == 0) {
		document.querySelector("#weather_temperature").innerHTML = NUM(weather.main.temp - 273.15) + " &deg;C";
	} else if (COOKIE.get("temperature") == 1) {
		document.querySelector("#weather_temperature").innerHTML = NUM((weather.main.temp - 273.15) * 9 / 5 + 32) + " &deg;F";
	} else {
		document.querySelector("#weather_temperature").innerHTML = NUM(weather.main.temp) + " K";
	}
	
	document.querySelector("#weather_pressure").innerHTML = NUM(weather.main.pressure) + " hPa";
	document.querySelector("#weather_humidity").innerHTML = NUM(weather.main.humidity) + " %";

};

// Get the weather data
var getWeatherData = function () {
	var weatherRequest = new XMLHttpRequest();
	
	weatherRequest.onload = function () {
		try {
			var weather = JSON.parse(weatherRequest.responseText);
			
			updateWeatherData(weather);
		}
		
		catch (error) {
			report_error(error);
		}
	};
	
	weatherRequest.open("GET", "http://api.openweathermap.org/data/2.5/weather?q=" + config.weather.place + "&appid=" + config.weather.appID);
	weatherRequest.send();
	
	setTimeout(getWeatherData, config.weather.updateTime);
};

getWeatherData();
