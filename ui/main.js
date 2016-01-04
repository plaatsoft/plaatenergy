// Main.js file for PlaatSolar
// Copyright (c) 2014 - 2015 PlaatSoft

// ================== Remove function for elements ========================
HTMLElement.prototype.remove = function () {
	this.parentNode.removeChild(this);
};

if (!localStorage.getItem("allow_cookies")) {
	localStorage.setItem("allow_cookies", "no");
}

// ============================== Loader ==================================
setTimeout(function () {
	document.querySelector(".loader").style.opacity = 0;
	document.querySelector(".grid").style.opacity = 1;
	
	setTimeout(function () {
		document.querySelector(".loader").remove();
	}, 300);
}, Math.floor((Math.random() * 1000) + 500));

// ========================== Language System =============================
var setLang = function (lang) {
	var http = new XMLHttpRequest();
	http.onload = function () {
		var data = JSON.parse(http.responseText);
		for(var key in data) {
			document.querySelector("#t_" + key).innerHTML = data[key];
		}
	};
	http.open("GET", "./lang.php?q=" + lang)
	http.send();
};

// ==================== Set the cookie if not set =========================
var browsorLanguage = navigator.language || navigator.browserLanguage;

var setLangOption = function () {
	switch (localStorage.getItem("lang")) {
		case "en":
			document.querySelector("#lang_selector > #en").selected = true;
			document.querySelector("#lang_selector > #nl").selected = false;
		break;
		case "nl":
			document.querySelector("#lang_selector > #en").selected = false;
			document.querySelector("#lang_selector > #nl").selected = true;
	}
};

var setTemperatureOption = function () {
	switch (localStorage.getItem("temperature")) {
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
	switch (localStorage.getItem(name)) {
		case "0":
			document.querySelector("#" + name + "_selector > #t_sidebars_settings_" + name + "_0").selected = true;
			document.querySelector("#" + name + "_selector > #t_sidebars_settings_" + name + "_1").selected = false;
		break;
		case "1":
			document.querySelector("#" + name + "_selector > #t_sidebars_settings_" + name + "_0").selected = false;
			document.querySelector("#" + name + "_selector > #t_sidebars_settings_" + name + "_1").selected = true;
	}
};

if (!localStorage.getItem("lang")) {
	switch (browsorLanguage) {
		case  "nl":
			localStorage.setItem("lang", "nl");
		break;
		default:
			localStorage.setItem("lang", "en");
	}
}
setLang(localStorage.getItem("lang"));
setLangOption();

if (!localStorage.getItem("gas")) {
	localStorage.setItem("gas", "0");
}
setOption("gas");

if (!localStorage.getItem("time")) {
	switch (browsorLanguage) {
		case "nl":
			localStorage.setItem("time", "1");
		break;
		default:
			localStorage.setItem("time", "0");
	}
}
setOption("time");

if (!localStorage.getItem("wind")) {
	localStorage.setItem("wind", "0");
}
setOption("wind");

if (!localStorage.getItem("numbers")) {
	switch (browsorLanguage) {
		case "nl":
			localStorage.setItem("numbers", "1");
		break;
		default:
			localStorage.setItem("numbers", "0");
	}
}
setOption("numbers");

if (!localStorage.getItem("weather")) {
	localStorage.setItem("weather", "0");
}

if (!localStorage.getItem("temperature")) {
	switch (browsorLanguage) {
		case "nl":
			localStorage.setItem("temperature", "0");
		break;
		default:
			localStorage.setItem("temperature", "1");
	}
}
setTemperatureOption();

if (!localStorage.getItem("weather")) {
	localStorage.setItem("weather", 0);
}

if (localStorage.getItem("weather") == 1) {
	document.querySelector("#weather").checked = true;
}

if (!localStorage.getItem("show_sunrise")) {
	localStorage.setItem("show_sunrise", 0);
}

if (localStorage.getItem("show_sunrise") == 1) {
	document.querySelector("#show_sunrise").checked = true;
}

var check_show_sunrise = function () {
	if (localStorage.getItem("show_sunrise") == 0) {
		document.querySelector(".sunrise").style.opacity = 0;
		document.querySelector(".sunset").style.opacity = 0;
	} else {
		document.querySelector(".sunrise").style.opacity = 1;
		document.querySelector(".sunset").style.opacity = 1;
	}
}

check_show_sunrise();

if (!localStorage.getItem("refresh_toggle")) {
	localStorage.setItem("refresh_toggle", 1);
}

if (localStorage.getItem("refresh_toggle") == 1) {
	document.querySelector("#refresh_toggle").checked = true;
}

// ================== Update the tiles information =======================
var refresh_data = function () {
	var options = localStorage.getItem("numbers");
	options += localStorage.getItem("time");
	options += localStorage.getItem("gas");
	options += localStorage.getItem("temperature");
	options += localStorage.getItem("wind");
	options += localStorage.getItem("weather");
	if (localStorage.getItem("lang") == "nl") {
		options += 0;
	} else {
		options += 1;
	}
	
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
	http.open("GET", "./data.php?q=" + options);
	http.send();
	
	if (localStorage.getItem("refresh_toggle") == 1) {
		setTimeout(refresh_data, localStorage.getItem("refresh") * 1000);
	}
};

var range = document.querySelector("#refresh");

var range_refresh_dis = function (a) {
	refresh_data();
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
	
	if (localStorage.getItem("numbers") == 0) {
		range_min.innerHTML = "0.1 s";
		range_max.innerHTML = range.max + " s";
		range_value.innerHTML = localStorage.getItem("refresh") + " s";
	} else {
		range_min.innerHTML = "0,1 s";
		range_max.innerHTML = String(range.max).replace(".", ",") + " s";
		range_value.innerHTML = String(localStorage.getItem("refresh")).replace(".", ",") + " s";
	}
};

if (!localStorage.getItem("refresh")) {
	localStorage.setItem("refresh", 1);
}

range_refresh_dis(document.querySelector("#refresh_toggle"));

range.value = localStorage.getItem("refresh");
set_range_helper();

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

var cookie = document.querySelector("#cookie");

// Show cookie popup
if (localStorage.getItem("allow_cookies") == "no") {
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
	
	localStorage.setItem("allow_cookies", "yes");
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

// ================ If a selector is change set in cookie =================
var selectors = ["lang", "numbers", "time", "gas", "temperature", "wind"];

for (var i = 0; i < selectors.length; i++) {
	document.querySelector("#" + selectors[i] + "_selector").onchange = function () {
		var id = this.id.replace("_selector", "");
		
		localStorage.setItem(id, this.value);
		
		if (id == "lang") {
			setLang(this.value);
		} else if (id == "numbers") {
			set_range_helper();
		}
	};
}

// for toggles
var toggles = ["weather", "refresh_toggle", "show_sunrise"];

for (var i = 0; i < toggles.length; i++) {
	document.querySelector("#" + toggles[i]).onchange = function () {
		if (this.checked) {
			localStorage.setItem(this.id, 1);
		} else {
			localStorage.setItem(this.id, 0);
		}
		
		if (this.id == "refresh_toggle") {
			range_refresh_dis(this);
		}
		
		if (this.id == "show_sunrise") {
			check_show_sunrise();
		}
	};
}

range.oninput = function () {
	localStorage.setItem("refresh", range.value);
	set_range_helper();
};
