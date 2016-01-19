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

/*
** ---------------------
** PAGES
** ---------------------
*/

function plaatenergy_donate_page() {

  $page = '<h1>'.t('DONATE_TITLE').'</h1>';

  $page .= '<br/>';
  $page .= '<div class="large_text">'.t('DONATE_CONTENT').'</div>';
  $page .= '<br/>';
  $page .= '<br/>';

  // Dirty hack to get Paypal button working :)
  $page .= '</form>';

  $page .= '<form action="https://www.paypal.com/cgi-bin/webscr" method="post">';
  $page .= '<input type="hidden" name="cmd" value="_s-xclick">';
  $page .= '<input type="hidden" name="hosted_button_id" value="R7TMYGJV42QTL">';
  $page .= '<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="">';
  $page .= '<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">';
  $page .= '</form>';

  $page .= '<br/>';

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
 * Help handler
 */
function plaatenergy_donate() {

  /* input */
  global $pid;

  /* Page handler */
  switch ($pid) {

     case PAGE_DONATE:
        return plaatenergy_donate_page();
        break;
  }
}

/*
** ---------------------
** THE END
** ---------------------
*/


?>
