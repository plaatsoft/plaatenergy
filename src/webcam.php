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
 * @brief contain webcam page
 */

/*
** ---------------------
** PAGE
** ---------------------
*/

/**
 * plaatenergy webcam page
 * @return HTML block which page contain.
 */
function plaatenergy_webcam_page() {

	global $pid;
	
	$device1 = plaatenergy_db_get_config_item('webcam_present', WEBCAM_1);
	$device2 = plaatenergy_db_get_config_item('webcam_present', WEBCAM_2);
		
	$page  = '<h1>'.t('TITLE_WEBCAM').'</h1>';
	$page .= '<br/>';
	$page .= '<style>.image{-moz-animation: none; -o-animation: none; -webkit-animation: none; animation: none}></style>';
  
	if ($device1=="true" ) {
		$page .= '<img class="image" src="webcam/image1.jpg" alt="" id="webcam1" width="480" height="360" >';
		$page .= '<script>window.setInterval(function() { document.getElementById("webcam1").src = "webcam/image1.jpg?random="+new Date().getTime(); }, 500);</script>';
	}
	
	if ($device2=="true" ) {
		$page .= '&nbsp;';
		$page .= '<img class="image" src="webcam/image2.jpg" alt="" id="webcam2" width="480" height="360" >';
		$page .= '<script>window.setInterval(function() { document.getElementById("webcam2").src = "webcam/image2.jpg?random="+new Date().getTime(); }, 500);</script>';
	}
	
	$page .= '<div class="nav">';
	$page .= plaatenergy_link('pid='.PAGE_HOME, t('LINK_HOME'), 'home');
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
function plaatenergy_webcam() {

  /* input */
  global $pid;

  /* Page handler */
  switch ($pid) {

     case PAGE_WEBCAM:
        return plaatenergy_webcam_page();
        break;
  }
}

/*
** ---------------------
** THE END
** ---------------------
*/

?>
