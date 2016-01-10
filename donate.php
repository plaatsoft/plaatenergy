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

include './config.inc';
include './general.inc';
include './database.inc';

plaatenergy_db_connect($dbhost, $dbuser, $dbpass, $dbname);

general_header();

echo '<h1>'.t('DONATE_TITLE').'</h1>';
echo '<div class="large_text">'.t('DONATE_CONTENT').'</div>';

echo '<br/>';
echo '<br/>';

echo '<form action="https://www.paypal.com/cgi-bin/webscr" method="post">';
echo '<input type="hidden" name="cmd" value="_s-xclick">';
echo '<input type="hidden" name="hosted_button_id" value="R7TMYGJV42QTL">';
echo '<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="">';
echo '<img alt="" border="0" src="https://www.paypalobjects.com/nl_NL/i/scr/pixel.gif" width="1" height="1">';
echo '</form>';

echo '<br/>';

general_navigation();
general_footer();

?>
