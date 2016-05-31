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
			
	$hue_ip_address = plaatenergy_db_get_config_item('hue_ip_address', HUE_1);
	$hue_key = plaatenergy_db_get_config_item('hue_key', HUE_1);
			
	$page  = '<h1>'.t('TITLE_HUE').'</h1>';
	$page .= '<ul id="list"></ul>';
	$page .= '<br/>';
	
	$page .= '<script>
	"use strict";

	var key = "'.$hue_key.'";
	
	function setLightState (lightID, state) {
		var http = new XMLHttpRequest();
		http.open("PUT", "http://'.$hue_ip_address.'/api/" + key + "/lights/" + lightID + "/state");
		http.send(\'{"on":\' + state + \'}\');
	}
	function setLightBrightness (lightID, brightness) {
		var http = new XMLHttpRequest();
		http.open("PUT", "http://'.$hue_ip_address.'/api/" + key + "/lights/" + lightID + "/state");
		http.send(\'{"bri":\' + brightness + \'}\');
	}
	
	var list = document.querySelector("#list");
	
	var http = new XMLHttpRequest();
	http.onload = function () {
		var data = JSON.parse(http.responseText);
		for (var id in data) {
			list.innerHTML += "<li><b>" + data[id].name + "</b>: <input type=\"checkbox\" " + (data[id].state.on ? "checked " : "") + "onchange=\"setLightState(" + id + ", this.checked)\"> " +
			"<input type=\"range\" min=\"0\" value=\"" + data[id].state.bri + "\" max=\"255\" onchange=\"setLightBrightness(" + id + ", this.value)\">";
		}
	};
	http.open("GET", "http://'.$hue_ip_address.'/api/" + key + "/lights");
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
