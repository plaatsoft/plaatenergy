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
 * @brief contain release notes
 */
 
/*
** ---------------------
** NOTES
** ---------------------
*/


$note[7] = '<div class="subparagraph">Version 0.7 (14-02-2016)</div>
<div class="large_text">
<ul>
<li>General: Added CSV export feature.</li>
<li>General: Improve query report output.</li>
<li>General: Add day name to day report title.</li>
<li>General: Improve main menu for disabled devices.</li>
<li>General: Added slide show mode. Day reports automatic cycle without human interaction.</li>
<li>Bugfix: New version check is now working correct.</li>
<li>Bugfix: Month and Day navigation is now working correct.</li>
</ul>
</div>';

$note[6] = '<div class="subparagraph">Version 0.6 (28-01-2016)</div>
<div class="large_text">
<ul>
<li>General: Added database backup feature to setting page.</li>
<li>General: Minimum and maximum day value to weather information pages.</li>
<li>General: Raspberry Pi Sense Hat led display shows now current power usage every minute.</li>
<li>General: Added PlaatSoft Logo to about page.</li>
<li>General: Added source code documentation for better support.</li>
<li>Bugfix: Used energy years report show now correct Y axes scale.</li>
<li>Bugfix: Selecting day in the future is now not possible anymore!</li>
</ul>
</div>';

$note[5] = '<div class="subparagraph">Version 0.5 (20-01-2016)</div>
<div class="large_text">
<ul>
<li>General: Move all configuration items to database.</li>
<li>General: Add settings page.</li>
<li>General: Add energy, gas, solar measurement correction page.</li>
<li>General: Improve header and footer block of all pages.</li>
<li>General: Improve error handeling. If something goes wrong user is better informed.</li>
<li>Realtime Information: Add slider effect to information boxes.</li>
<li>Realtime Information: Add sunrise and sunset information box based on location.</li>
<li>Realtime Information: Add energy and gas carbon dioxide emission information box.</li>
<li>Realtime Information: Setting slider contain now more options.</li>
<li>Installation: Database is now automaticly created/patched during installation.</li>
<li>Installation: Now only one cron job is needed.</li>
<li>Installation: Python sensors scripts fetch device settings from database.</li>
<li>Installation: When new version is available user is informed.</li>
<li>Bugfix: Energy / Weather station meter connection down detection improved.</li>
</ul>
</div>';

$note[4] = '<div class="subparagraph">Version 0.4 (01-01-2016)</div>
<div class="large_text">
<ul>
<li>Add forecast information to years and year reports.</li>
<li>Add multi language support.</li>
<li>Move dutch translation to resource file.</li>
<li>Add english resource file.</li>
<li>Add solar correction page to add missing measurement.</li>
<li>Add release notes page.</li>
<li>Add about page.</li>
<li>Add donate page.</li>
<li>Improve realtime info page. Now page has same dimensions as other pages.</li>
<li>Add light and dark css theme (thanks bplaat).</li>
<li>Add buttons icons (thanks bplaat).</li>
</ul>
</div>';

$note[3] = '<div class="subparagraph">Version 0.3 (30-11-2015)</div>
<div class="large_text">
<ul>
<li>Added Energy, Solar, Gas years reports.</li>
<li>Optimised database structure. Data is now aggregrated on day base.</li>
<li>Update energy, solar, gas year reports.</li>
<li>Update energy, solar, gas month reports.</li>
<li>Update energy, solar, gas day reports.</li>
<li>Move sensor python scripts to website directory structure.</li>
<li>Add option to disable weather meter.</li>
<li>Add option to disable solar meter.</li>
<li>Add connection check to Energy, Solar and Weather meter.</li>
<li>Realtime information GUI is improved.</li>
</ul>
</div>';

$note[2] = '<div class="subparagraph">Version 0.2 (31-10-2015)</div>
<div class="large_text">
<ul>
<li>Created mysql database</li>
<li>Imported all solar csv data in database</li> 
<li>Add P1 energy meter python sensor script.</li>
<li>Add AstroHat weather sensor script.</li>
<li>Add energy, solar, gas year reports.</li>
<li>Add energy, solar, gas month reports.</li>
<li>Add energy, solar, gas day reports.</li>
<li>Add temperature, huminity, pressure day reports.</li>
<li>Add realtime information GUI (Created by bplaat).</li>
</ul>
</div>';
		
$note[1] = '<div class="subparagraph">Version 0.1 (30-09-2015)</div>
<div class="large_text">
<ul>
<li>Add solar meter python sensor script.</li>
<li>Add solar year reports.</li>
<li>Add solar month reports.</li>
<li>Add solar day reports.</li>
<li>Store solar in CSV file.</li>
</ul>
</div>';
		
/*
** ---------------------
** PAGES
** ---------------------
*/

function plaatenergy_release_notes_page(){

  global $pid;
  global $id;
  global $note;
  
  $page  = '<h1>Release Notes</h1>';
  $page .= '<br/>';
  
  $page .= $note[$id];
  
  $page .= '<div class="nav">';
  $page .= plaatenergy_link('pid='.$pid.'&eid='.EVENT_PREV.'&id='.$id, t('LINK_PREV'));
  $page .= plaatenergy_link('pid='.PAGE_HOME, t('LINK_HOME'), 'home');
  $page .= plaatenergy_link('pid='.$pid.'&eid='.EVENT_NEXT.'&id='.$id, t('LINK_NEXT'));
  $page .= '</div>';

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
function plaatenergy_release_notes() {

	/* input */
	global $max;
	global $pid;
	global $eid;
	global $id;
	global $note;

	if($id==0) {
		$id = sizeof($note);
	}
	
	/* Event handler */
	switch ($eid) {
      
		case EVENT_NEXT:
			if ($id<sizeof($note)) {
				$id++;
			}
			break;

		case EVENT_PREV:
			if ($id>1) {
				$id--;
			}
			break;
   }

	/* Page handler */
	switch ($pid) {

		case PAGE_RELEASE_NOTES:
			return plaatenergy_release_notes_page();
			break;
	}
}

/*
** ---------------------
** THE END
** ---------------------
*/

?>
