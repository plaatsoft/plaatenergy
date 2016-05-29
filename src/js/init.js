// ========================== Language System =============================
var setLang = function (lang) {
	var http = new XMLHttpRequest();
	http.onload = function () {
		var data = JSON.parse(http.responseText);
		for(var key in data) {
			document.querySelector("#t_" + key).innerHTML = data[key];
		}
	};
	http.open("GET", "lang.php?q=" + lang);
	http.send();
};

// ==================== Set the cookies if they are not set =========================
	var checkSunrise = function () {
		if (COOKIE.get("sunrise") == 0) {
			document.querySelector("#weather_sunrise_tile").style.opacity = 0;
			document.querySelector("#weather_sunset_tile").style.opacity = 0;
		} else {
			document.querySelector("#weather_sunrise_tile").style.opacity = 1;
			document.querySelector("#weather_sunset_tile").style.opacity = 1;
		}
	}
	
	var checkCookies = function () {
	// Check functions
	var setTemperatureOption = function () {
		switch (COOKIE.get("temperature")) {
			case "0":
				document.querySelector("#temperature_selector > #celcius").selected = true;
				document.querySelector("#temperature_selector > #fahrenheit").selected = false;
				document.querySelector("#temperature_selector > #kelvin").selected = false;
			break;
			case "1":
				document.querySelector("#temperature_selector > #celcius").selected = false;
				document.querySelector("#temperature_selector > #fahrenheit").selected = true;
				document.querySelector("#temperature_selector > #kelvin").selected = false;
			break;
			case "2":
				document.querySelector("#temperature_selector > #celcius").selected = false;
				document.querySelector("#temperature_selector > #fahrenheit").selected = false;
				document.querySelector("#temperature_selector > #kelvin").selected = true;
			break;
		}
	};
	
	var setOption = function (name) {
		switch (COOKIE.get(name)) {
			case "0":
				document.querySelector("#" + name + "_selector > [value='0']").selected = true;
				document.querySelector("#" + name + "_selector > [value='1']").selected = false;
			break;
			case "1":
				document.querySelector("#" + name + "_selector > [value='0']").selected = false;
				document.querySelector("#" + name + "_selector > [value='1']").selected = true;
		}
	};
	
	var setToggle = function (name) {
		if (COOKIE.get(name) == 1) {
			document.querySelector("#" + name + "_toggle").checked = true;
		}
	}
	
	// Check lang
	COOKIE.check("lang", config.preferences.language);
	setLang(COOKIE.get("lang"));
	setOption("lang");
	
	// Check lang
	COOKIE.check("gas", config.preferences.gasUnit);
	setOption("gas");
	
	// Check time
	COOKIE.check("time", config.preferences.timeFormat);
	setOption("time");
	
	// Check wind
	COOKIE.check("wind", config.preferences.windUnit);
	setOption("wind");
	
	// Check numbers
	COOKIE.check("numbers", config.preferences.numbersFormat);
	setOption("numbers");
	
	// Check weather_toggle
	COOKIE.check("weather", config.preferences.loadWeatherData);
	setToggle("weather");
	
	// Check Temprature
	COOKIE.check("temperature", config.preferences.temperatureFormat);
	setTemperatureOption();
	
	// Check Sunrise
	COOKIE.check("sunrise", config.preferences.showSunrise);
	setToggle("sunrise");
	
	checkSunrise();
	
	// Check refresh toggle
	COOKIE.check("enableRefresh", config.preferences.enableRefresh);
	setToggle("enableRefresh");
};


// ========================= Date & Time System ============================
var updateDateAndTime = function () {
	var date = new Date();
	
	var year = date.getFullYear();
	var month = date.getMonth() + 1;
	var day = date.getDate();
	
	if(month<10){month="0"+month}
	if(day<10){day="0"+day}
	
	// Dutch notation
	if (COOKIE.get("time") == 1) {
		document.querySelector("#date").innerHTML = day + "-" + month + "-" + year;
	}
	
	// English notation
	else {
		document.querySelector("#date").innerHTML = year + "-" + month + "-" + day;
	}
	
	document.querySelector("#time").innerHTML = DATE(Math.round(date.getTime() / 1000));
	
	setTimeout(updateDateAndTime, 1000);
	
	console.log(localStorage.getItem("data"));
};


// =============================== Sidebars ===============================
	var shadow = document.querySelector(".shadow");

	var sidebar_show_body = function (selector) {
		document.querySelector(".body").style.left = "-100%";
		document.querySelector(selector + ".body.more").style.left = "0%";
		document.querySelector(".header > .label").style.left = "calc(-100% - 16px)";
		document.querySelector(".header > .label.more").style.left = "16px";
	};

	var sidebar_show_body_cancel = function () {
		document.querySelector(".body").style.left = "0%";
		document.querySelector(".body.more").style.left = "100%";
		document.querySelector(".header > .label").style.left = "16px";
		document.querySelector(".header > .label.more").style.left = "calc(100% + 16px)";
	};

	// Function to open a sidebar
	var open_sidebar = function (selector) {
		shadow.style.visibility = "visible";
		shadow.style.opacity = 1;
		document.querySelector(selector + ".sidebar").style.left = "0px";
	};

	COOKIE.check("ac", 0);

	var cookie = document.querySelector("#cookie");

	// Show cookie popup
	if (COOKIE.get("ac") == 0) {
		setTimeout(function () {
			shadow.style.visibility = "visible";
			shadow.style.opacity = 1;
			
			cookie.style.bottom = "0px";
			cookie.style.opacity = 1;  
		}, 300);
	} else {
		cookie.style.display = "none";
	}

	// Function to close all the popups
	var close_all_popups = function () {
		var popups = document.querySelectorAll(".popup");
		for (var i = 0; i < popups.length; i++) {
			popups[i].style.bottom = "15%";
			popups[i].style.opacity = 0;  
		}
		
		setTimeout(function () {
			for (var i = 0; i < popups.length; i++) {
				popups[i].remove();
			}
		}, 300);
			
		shadow.style.visibility = "hidden";
		shadow.style.opacity = 0;
		
		COOKIE.set("ac", 1);
	};

	// Function to close all sidebars
	var close_all_sidebars = function () {
		close_all_popups();
		
		// Select all the sidebars
		var sidebars = document.querySelectorAll(".sidebar");
		for (var i = 0; i < sidebars.length; i++) {
			sidebars[i].style.left = "-320px";  
		}
		
		// Set all the scroll in the body to 0
		setTimeout(function () {
			var bodys = document.querySelectorAll(".sidebar .body");
			for (var i = 0; i < bodys.length; i++) {
				bodys[i].scrollTop = 0;  
			}
			sidebar_show_body_cancel();
		}, 300);
	};

// ========================== Background system ===========================
var check_bg = function () {
	COOKIE.check("bg", "");
	COOKIE.check("bgn", "");
	
	if (COOKIE.get("bg") != "") {
		document.querySelector(".bg").style.backgroundImage = "url(" + COOKIE.get("bg") + ")";
		document.querySelector("#bg2").style.backgroundImage = "url(" + COOKIE.get("bg") + ")";
		
		if (COOKIE.get("bgn").length > 28) {
			document.querySelector("#bg_name").innerHTML = COOKIE.get("bgn").substr(0, 27) + "...";
		} else {
			document.querySelector("#bg_name").innerHTML = COOKIE.get("bgn");
		}
		
		document.querySelector("#bg1").style.display = "block";
	} else {
		document.querySelector(".bg").removeAttribute("style");
		document.querySelector("#bg2").removeAttribute("style");
		document.querySelector("#bg_name").innerHTML = "";
		
		document.querySelector("#bg1").style.display = "none";
	}
};

var bg_functions = function () {
	document.querySelector("#background-loader").onchange = function (e) {
		var reader = new FileReader();
		reader.onload = function(){
			var dataURL = reader.result;
			
			COOKIE.set("bg", dataURL);
			COOKIE.set("bgn", e.target.files[0].name);
			
			check_bg();
		};
		reader.readAsDataURL(e.target.files[0]);
	};
	
	var close_bg = function () {
		COOKIE.set("bg", "");
		COOKIE.set("bgn", "");
		
		check_bg();
	};
	
	var download_bg = function () {
		var l = document.createElement("a");
		l.download = COOKIE.get("bgn");
		l.href = COOKIE.get("bg");
		l.click();
	};
};

// ================================ Init ==================================
var init = function () {

	
	
	// Change the background by an image
	check_bg();
	bg_functions();
	
	// Check the cookies
	checkCookies();
	
	// Set the date and time
	updateDateAndTime();
	
	// Get the weather data (updateWeatherData.js)
	getWeatherData();
	
	// Other stuff
	
	// ================== Update the tiles information =======================
	var updateTileData = function () {
		var http = new XMLHttpRequest();
		http.onload = function () {
			var data = JSON.parse(http.responseText);
			for(var key in data) {
				switch (key) {
					case "current_watt":
					case "energy_today":
						if (data[key].charAt(0) == "-" ) {
							document.querySelector("#" + key).style.backgroundColor = "#f44336";
						} else {
							document.querySelector("#" + key).style.backgroundColor = "#4caf50";
						}
						document.querySelector("#" + key + "_text").innerHTML = data[key];
					break;
					default:
						document.querySelector("#" + key).innerHTML = data[key];
				}
			}
		};
		http.open("GET", "data.php?q=" + COOKIE.get("numbers") + COOKIE.get("gas") + COOKIE.get("temperature"));
		http.send();
		
		if (COOKIE.get("enableRefresh") == 1) {
			setTimeout(updateTileData, COOKIE.get("refresh") * 1000);
		}
	};

	var range = document.querySelector("#refresh");

	var range_refresh_dis = function (a) {
		updateTileData();
		range.disabled = false;
		
		var range_helpers = document.querySelectorAll(".range_helper");
		for (var i = 0; i < range_helpers.length; i++) {
			range_helpers[i].classList.remove("disabled");  
		}
		
		if (a.checked == false) {
			range.disabled = true;
			
			var range_helpers = document.querySelectorAll(".range_helper");
			for (var i = 0; i < range_helpers.length; i++) {
				range_helpers[i].classList.add("disabled");  
			}
		}
	};

	var set_range_helper = function () {
		var range_min = document.querySelector("#range_min");
		var range_max = document.querySelector("#range_max");
		var range_value = document.querySelector("#range_value");
		
		if (COOKIE.get("numbers") == 0) {
			range_min.innerHTML = "0.1 s";
			range_max.innerHTML = range.max + " s";
			range_value.innerHTML = COOKIE.get("refresh") + " s";
		} else {
			range_min.innerHTML = "0,1 s";
			range_max.innerHTML = String(range.max).replace(".", ",") + " s";
			range_value.innerHTML = String(COOKIE.get("refresh")).replace(".", ",") + " s";
		}
	};

	// Check refresh range
	COOKIE.check("refresh", config.preferences.tileUpdateTime);
	range_refresh_dis(document.querySelector("#enableRefresh_toggle"));
	range.value = COOKIE.get("refresh");
	set_range_helper();

	// ================ If a selector is change set in cookie =================
	var selectors = ["lang", "numbers", "time", "gas", "temperature", "wind"];

	for (var i = 0; i < selectors.length; i++) {
		document.querySelector("#" + selectors[i] + "_selector").onchange = function () {
			var id = this.id.replace("_selector", "");
			
			COOKIE.set(id, this.value);
			
			if (id == "lang") {
				setLang(this.value);
			} else if (id == "numbers") {
				set_range_helper();
			}
		};
	}

	// for toggles
	var toggles = ["weather_toggle", "enableRefresh_toggle", "sunrise_toggle"];

	for (var i = 0; i < toggles.length; i++) {
		document.querySelector("#" + toggles[i]).onchange = function () {
			if (this.checked) {
				COOKIE.set(this.id.replace("_toggle", ""), 1);
			} else {
				COOKIE.set(this.id.replace("_toggle", ""), 0);
			}
			
			if (this.id == "enableRefresh_toggle") {
				range_refresh_dis(this);
			}
			
			if (this.id == "sunrise_toggle") {
				checkSunrise();
			}
		};
	}

	range.oninput = function () {
		COOKIE.set("refresh", range.value);
		set_range_helper();
	};
};
