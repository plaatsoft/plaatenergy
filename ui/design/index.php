<?php
  include "../../general.inc";
?>
<!DOCTYPE html>
<html>
<head>
	<?php
		add_icons("../");
	?>
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
	<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,700|Material+Icons">
	<link rel="stylesheet" type="text/css" href="./ui.css">
</head>
<body>
	<div class="app theme-brown">
		<div class="appbar">
			<span class="material-icons effect" onclick="open_sidebar('#nav')">&#xE5D2;</span>
			<h1>PlaatSolar</h1>
			<span class="material-icons next">&#xE5CC;</span>
			<h1>Dashboard</h1>
		</div>

      <div class="page">
		<div class="card">
		  <h2 id="time" class="numbers"></h2>
		  <h4 id="date" class="numbers small"></h4>
		  <span class="label">Date and time</span>
		</div>
		
		<div class="card">
		  <h2 id="temperature" class="numbers"></h2>
		  <h4 id="humidity" class="numbers small"></h4>
		  <h4 id="pressure" class="numbers small"></h4>
		  <span class="label">Weather</span>
		</div>
		
		<div class="card">
		  <h2 id="current_watt" class="numbers"></h2>
		  <h4 id="energy_today" class="numbers small"></h4>
		  <span class="label">Energy</span>		
		</div>
		
		<div class="card last">
		  <h2 id="gas_today" class="numbers"></h2>
		  <h4 id="total_gas" class="numbers small"></h4>
		  <span class="label">Gas</span>
		</div>
 
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" class="loader">
          <circle cx="32" cy="32" r="29"></circle>
        </svg>
      </div>

      <div class="shadow"></div>

      <div id="nav" class="sidebar">
        <div class="header">
          <h2>PlaatSolar</h2>
          <span class="material-icons effect">close</span>
        </div>
        <div class="body">
          <div class="item active">
            <span class="material-icons">dashboard</span>
            <span class="label">Dashboard</span>
          </div>
          <div class="item">
            <span class="material-icons">settings</span>
            <span class="label">Settings</span>
          </div>

          <h4>Overig</h4>
          <div class="item">
            <span class="material-icons">trending_down</span>
            <span class="label">Afgenomen Elektriciteit</span>
          </div>
        </div>
      </div>
    </div>

    <script>
      window.oncontextmenu = function () {
        return false;
      };

      var refresh = function () {
		var http = new XMLHttpRequest();
		http.onload = function () {
			var data = JSON.parse(http.responseText);
			document.querySelector("#time").innerHTML = data.time;
			document.querySelector("#date").innerHTML = data.date;
			document.querySelector("#temperature").innerHTML = data.weather_temperature;
			document.querySelector("#humidity").innerHTML = data.weather_humidity;
			document.querySelector("#pressure").innerHTML = data.pressure;
			document.querySelector("#gas_today").innerHTML = data.gas_today;
			document.querySelector("#total_gas").innerHTML = data.total_gas;
			document.querySelector("#current_watt").innerHTML = data.current_watt;
			document.querySelector("#energy_today").innerHTML = data.energy_today;
        };  
		http.open("GET", "../data.php?q=11100");
		http.send();
	  };
	  setInterval(refresh, 1000);
	  
	  setTimeout(function () {
		  document.querySelector(".loader").style.opacity = 0;
		  setTimeout(function () {
			document.querySelector(".loader").style.display = "none";
		  }, 500);
		  var cards = document.querySelectorAll(".card");
          for (var i = 0; i < cards.length; i++) {
            cards[i].style.opacity = 1;
          }
	  }, 1000);

      var shadow = document.querySelector(".shadow");

      function open_sidebar (e) {
        shadow.style.visibility = "visible";
        shadow.style.opacity = 1;
        document.querySelector(e + ".sidebar").style.left = "0px";
      };

      function close () {
        shadow.style.visibility = "hidden";
        shadow.style.opacity = 0;
        var sidebars = document.querySelectorAll(".sidebar");
        for (var i = 0; i < sidebars.length; i++) {
          sidebars[i].style.left = "-320px";
        }
      };

      var closers = document.querySelectorAll(".sidebar > .header > .material-icons");
      for (var i = 0; i < closers.length; i++) {
        closers[i].onclick = close;
      }
    </script>
  </body>
</html>

