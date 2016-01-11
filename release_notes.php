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

echo '<h1>Release Notes</h1>';

$max_page = 5;

$page = $max_page;
release_notes_parameters();

if ($page==1) {
  echo '<div class="subparagraph">Version 0.1 (30-09-2015)</div>';
  echo '<div class="large_text">';
  echo '<ul>';
  echo '<li>Add solar meter python sensor script.</li>';
  echo '<li>Add solar year reports.</li>';
  echo '<li>Add solar month reports.</li>';
  echo '<li>Add solar day reports.</li>';
  echo '<li>Store solar in CSV file.</li>';
  echo '</ul>';
  echo '</div>';
}

if ($page==2) {
  echo '<div class="subparagraph">Version 0.2 (31-10-2015)</div>';
  echo '<div class="large_text">';
  echo '<ul>';
  echo '<li>Created mysql database</li>';
  echo '<li>Imported all solar csv data in database</li>'; 
  echo '<li>Add P1 energy meter python sensor script.</li>';
  echo '<li>Add AstroHat weather sensor script.</li>';
  echo '<li>Add energy, solar, gas year reports.</li>';
  echo '<li>Add energy, solar, gas month reports.</li>';
  echo '<li>Add energy, solar, gas day reports.</li>';
  echo '<li>Add temperature, huminity, pressure day reports.</li>';
  echo '<li>Add realtime information GUI (Created by bplaat).</li>';
  echo '</ul>';
  echo '</div>';
}

if ($page==3) {
  echo '<div class="subparagraph">Version 0.3 (30-11-2015)</div>';
  echo '<div class="large_text">';
  echo '<ul>';
  echo '<li>Added Energy, Solar, Gas years reports.</li>';
  echo '<li>Optimised database structure. Data is now aggregrated on day base.</li>';
  echo '<li>Update energy, solar, gas year reports.</li>';
  echo '<li>Update energy, solar, gas month reports.</li>';
  echo '<li>Update energy, solar, gas day reports.</li>';
  echo '<li>Move sensor python scripts to website directory structure.</li>';
  echo '<li>Add option to disable weather meter.</li>';
  echo '<li>Add option to disable solar meter.</li>';
  echo '<li>Add connection check to Energy, Solar and Weather meter.</li>';
  echo '<li>Realtime information GUI is improved.</li>';
  echo '</ul>';
  echo '</div>';
}

if ($page==4) {
  echo '<div class="subparagraph">Version 0.4 (01-01-2016)</div>';
  echo '<div class="large_text">';
  echo '<ul>';
  echo '<li>Add forecast information to years and year reports.</li>';
  echo '<li>Add multi language support.</li>';
  echo '<li>Move dutch translation to resource file.</li>';
  echo '<li>Add english resource file.</li>';
  echo '<li>Add solar correction page to add missing measurement.</li>';
  echo '<li>Add release notes page.</li>';
  echo '<li>Add about page.</li>';
  echo '<li>Add donate page.</li>';
  echo '<li>Improve realtime info page. Now page has same dimensions as other pages.</li>';
  echo '<li>Add light and dark css theme (thanks bplaat).</li>';
  echo '<li>Add buttons icons (thanks bplaat).</li>';
  echo '</ul>';
  echo '</div>';
}

if ($page==5) {
  echo '<div class="subparagraph">Version 0.5 (09-01-2016)</div>';
  echo '<div class="large_text">';
  echo '<ul>';
  echo '<li>Database model is now automaticly patch during version upgrade.</li>';
  echo '<li>Move most of the configuration items from php to database.</li>';
  echo '<li>Now realtime outside weather information can be customized (thanks bplaat).</li>';
  echo '<li>Improve footer information of all pages.</li>';
  echo '<li>Now day reports support manual added measements.</li>';
  echo '</ul>';
  echo '</div>';
}

release_notes_navigation();
general_footer();

?>
