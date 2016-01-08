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

include './config.inc';
include './general.inc';
include './database.inc';

plaatenergy_db_connect($dbhost, $dbuser, $dbpass, $dbname);

general_header();

echo '<h1>'.t('ABOUT_TITLE').'</h1>';
echo  '<div class="large_text">'.t('ABOUT_CONTENT').'</div>';

echo '<h2>'.t('CREDITS_TITLE').'</h2>';
echo  '<div class="large_text">'.t('CREDITS_CONTENT').'</div>';

echo '<h2>'.t('DISCLAIMER_TITLE').'</h2>';
echo '<div class="large_text">'.t('DISCLAIMER_CONTENT').'</div>';

general_navigation();
general_footer();

?>
