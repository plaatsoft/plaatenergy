// main.js file for PlaatEnergy
// Copyright (c) 2008 - 2016 PlaatSoft

// ================== Remove function for elements ========================
HTMLElement.prototype.remove = function () {
	this.parentNode.removeChild(this);
};

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

// Check weather
COOKIE.check("w", 0);

if (COOKIE.get("w") == 1) {
	document.querySelector("#w").checked = true;
}

// Check Sunrise
COOKIE.check("sr", 0);

if (COOKIE.get("sr") == 1) {
	document.querySelector("#sr").checked = true;
}

var check_sr = function () {
	if (COOKIE.get("sr") == 0) {
		document.querySelector("#w_sunrise").style.opacity = 0;
		document.querySelector("#w_sunset").style.opacity = 0;
	} else {
		document.querySelector("#w_sunrise").style.opacity = 1;
		document.querySelector("#w_sunset").style.opacity = 1;
	}
}

check_sr();

// Check no animation
COOKIE.check("am", 0);

if (COOKIE.get("am") == 1) {
	document.querySelector("#am").checked = true;
}

var check_am = function () {
	if (COOKIE.get("am") == 0) {
		document.body.classList.remove("nm");
	} else {
		document.body.classList.add("nm");
	}
}

check_am();

// Check refresh toggle
COOKIE.check("rt", 1);

if (COOKIE.get("rt") == 1) {
	document.querySelector("#rt").checked = true;
}

// ================== Update the tiles information =======================
var refresh_data = function () {
	var options = localStorage.getItem("numbers");
	options += localStorage.getItem("time");
	options += localStorage.getItem("gas");
	options += localStorage.getItem("temperature");
	options += localStorage.getItem("wind");
	options += COOKIE.get("w");
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
	
	if (COOKIE.get("rt") == 1) {
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

range_refresh_dis(document.querySelector("#rt"));

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
var toggles = ["w", "rt", "sr", "am"];

for (var i = 0; i < toggles.length; i++) {
	document.querySelector("#" + toggles[i]).onchange = function () {
		if (this.checked) {
			COOKIE.set(this.id, 1);
		} else {
			COOKIE.set(this.id, 0);
		}
		
		if (this.id == "rt") {
			range_refresh_dis(this);
		}
		
		if (this.id == "sr") {
			check_sr();
		}
		
		if (this.id == "am") {
			check_am();
		}
	};
}

range.oninput = function () {
	localStorage.setItem("refresh", range.value);
	set_range_helper();
};

// ========================== Background loader ===========================

var check_bg = function () {
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

COOKIE.check("bg", "");
COOKIE.check("bgn", "");
check_bg();

var bg_load = document.querySelector("#background-loader");

bg_load.onchange = function (e) {
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
