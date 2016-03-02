// A function to remove elements
HTMLElement.prototype.remove = function () {
	this.parentNode.removeChild(this);
};

// A function to write numbers better
var NUM = function (number) {
	var num = String(number.toFixed(1)).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
	
	// If numbers are Dutch replace '.' -> ',' and ',' -> '.'
	if (COOKIE.get("numbers") == 1) {
		num = num.replace(/\./g, "#").replace(/\,/g, ".").replace(/#/g, ",");
	}
	
	return num;
};

// A function to write a date better
var DATE = function (seconds) {
	var date = new Date(seconds * 1e3);
	
	var hours = date.getHours();
	var minutes = date.getMinutes();
	var seconds = date.getSeconds();
	
	// Dutch notation
	if (COOKIE.get("time") == 1) {
		if(hours<10){hours="0"+hours}
		if(minutes<10){minutes="0"+minutes}
		if(seconds<10){seconds="0"+seconds}
		
		return hours + ":" + minutes + ":" + seconds;
	}
	
	// English notation
	else {
		var am_pm = 0;
		
		if (hours > 12) {
			hours = hours - 12;
			am_pm = 1;
		}
		
		if(hours<10){hours="0"+hours}
		if(minutes<10){minutes="0"+minutes}
		if(seconds<10){seconds="0"+seconds}
		
		return hours + ":" + minutes + ":" + seconds + " " + ["AM", "PM"][am_pm];
	}
};

// A function to sort a object (from internet)
function sortObject(r){var t,n={},o=[];for(t in r)r.hasOwnProperty(t)&&o.push(t);for(o.sort(),t=0;t<o.length;t++)n[o[t]]=r[o[t]];return n}

// Cookie function object
var COOKIE = {
	// Cookie support
	cookieSuport: typeof(Storage) != "undefined",
	
	// Standart set function
	set: function (key, value) {
		if (this.cookieSuport) {
			if (!localStorage.getItem("data")) {
				localStorage.setItem("data", "{}");
			}
			
			var data = JSON.parse(localStorage.getItem("data"));
			
			data[key] = value;
			
			localStorage.setItem("data", JSON.stringify(sortObject(data)));
			
			return true;
		} else {
			return false;
		}
	},
	
	// Standart get function
	get: function (key) {
		if (this.cookieSuport) {
			return JSON.parse(localStorage.getItem("data"))[key];
		} else {
			return false;
		}
	},
	
	// Check function
	check: function (key, value) {
		if (this.cookieSuport) {
			if (!localStorage.getItem("data")) {
				localStorage.setItem("data", "{}");
			}
			
			if (!this.get(key)) {
				this.set(key, value);
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
};
