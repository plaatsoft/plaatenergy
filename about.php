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
**  All copyrights reserved (c) 2008-2015 PlaatSoft
*/

/*
** ---------------------
** PAGE
** ---------------------
*/

function plaatenergy_about_page() {

  $page  = '<h1>'.t('ABOUT_TITLE').'</h1>';
  $page .= '<br/>';
  $page .= '<div class="large_text">'.t('ABOUT_CONTENT').'</div>';

  $page .= '<h2>'.t('CREDITS_TITLE').'</h2>';
  $page .= '<div class="large_text">'.t('CREDITS_CONTENT').'</div>';

  $page .= '<h2>'.t('DISCLAIMER_TITLE').'</h2>';
  $page .= '<div class="large_text">'.t('DISCLAIMER_CONTENT').'</div>';

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

function plaatenergy_about() {

  /* input */
  global $pid;

  /* Page handler */
  switch ($pid) {

     case PAGE_ABOUT:
        return plaatenergy_about_page();
        break;
  }
}

/*
** ---------------------
** THE END
** ---------------------
*/

?>
