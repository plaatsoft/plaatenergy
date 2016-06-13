<?php

/* 
**  ===========
**  PlaatEnergy
**  ===========
**
**  Created by wplaat
**
**  For more information visit the following website.
**  Website : www.plaatsoft.nl 
**
**  Or send an email to the following address.
**  Email   : info@plaatsoft.nl
**
**  All copyrights reserved (c) 2008-2016 PlaatSoft
*/

/**
 * @file
 * @brief contain hue page
 */

/*
** ---------------------
** PAGE
** ---------------------
*/

/**
 * plaatenergy hue page
 * @return HTML block which page contain.
 */
function plaatenergy_hue_page() {

	global $pid;

        $page ="<style>input[type='checkbox']{width:24px;height:24px}</style>";
	$page .= '<h1>'.t('TITLE_HUE').'</h1>';
	$page .= '<ul id="list"></ul>';
	$page .= '<br/>';

	$page .= '<script>
	function setLightState (lightID, state) {
		var http = new XMLHttpRequest();
		http.open("GET", "realtime.php?light=" + lightID + "&key=on&value=" + state);
		http.send();
	}
	function setLightBrightness (lightID, brightness) {
		var http = new XMLHttpRequest();
		http.open("GET", "realtime.php?light=" + lightID + "&key=bri&value=" + brightness);
		http.send();
	}

	var list = document.querySelector("#list");

	var http = new XMLHttpRequest();
	http.onload = function () {
		var data = JSON.parse(http.responseText);
		for (var i = 0; i < data.length; i++) {
			list.innerHTML += "<li><b>" + data[i].name + "</b>: <input type=\"checkbox\" " + (data[i].on ? "checked " : "") + "onchange=\"setLightState(" + data[i].id + ", this.checked)\"> " +
			"<input type=\"range\" min=\"0\" value=\"" + data[i].bri + "\" max=\"255\" onchange=\"setLightBrightness(" + data[i].id + ", this.value)\">";
		}
	};
	http.open("GET", "realtime.php?lights");
	http.send();
</script>
';
	
	$page .= '<div class="nav">';
	$page .= plaatenergy_link('pid='.PAGE_HOME, t('LINK_HOME'));
	$page .=  '</div>';
	
	return $page;
}

/*
** ---------------------
** HANDLER
** ---------------------
*/

/**
 * plaatenergy about handler
 * @return HTML block which page contain.
 */
function plaatenergy_hue() {

  /* input */
  global $pid;

  /* Page handler */
  switch ($pid) {

     case PAGE_HUE:
        return plaatenergy_hue_page();
        break;
  }
}

/*
** ---------------------
** THE END
** ---------------------
*/

?>
