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
 * @brief contain about page
 */

/*
** ---------------------
** PAGE
** ---------------------
*/

/**
 * plaatenergy about page
 * @return HTML block which page contain.
 */
function plaatenergy_webcam_page() {

  global $pid;
	
  $page  = '<h1>'.t('TITLE_WEBCAM').'</h1>';
  $page .= '<br/>';
  $page .= '<style>.image{-moz-animation: none; -o-animation: none; -webkit-animation: none; animation: none}></style>';
  
  $page .= '<img class="image" src="webcam/image.jpg" alt="" width="480" height="320" >';

  $page .= '<div class="nav">';
  $page .= plaatenergy_link('pid='.PAGE_HOME, t('LINK_HOME'), 'home');
  $page .=  '</div>';
  
  $page .= '<script>setTimeout(link,5000,\'pid='.$pid.'\');</script>';

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
