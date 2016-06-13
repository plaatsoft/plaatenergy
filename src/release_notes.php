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
$note[12] = '<div class="subparagraph">Version 1.2 (13-06-2016)</div>
<div class="large_text">
<ul>
<li>General: Add Notify My Android (NMA) webservice integration for push messages to andriod phone.</li>
<li>General: Add basic support for two webcams.</li>
<li>General: Add option to make picture from webcam view.</li>
<li>General: Add motion detection with automatic recording to webcam sensor script.</li>
<li>General: Add option to navigate through webcam recordings.</li>
<li>General: Add basic support for Philips HUE lighting system.</li>
<li>General: Passwords are now encrypted stored in database.</li>
</ul>
</div>';

$note[11] = '<div class="subparagraph">Version 1.1 (07-05-2016)</div>
<div class="large_text">
<ul>
<li>General: Added solar converter efficiency value to energy delivered year report</li>
<li>General: Improve export to SQL feature. Now export file can be downloaded and delete afterwards.</li>
<li>General: Added weather station script base on onboard pi sensor.</li>
<li>General: Show status of all enabled solar converters at home page.</li>
<li>General: Added system name field to configuration.</li>
<li>General: Added solar description field to configuration.</li>
<li>General: Solar measument adaption now support 3 solar converters</li>
<li>General: Maximum value calculation improved for used energy year report.</li>
<li>General: Added solar converter peak power setting to configuration.</li>
<li>Bugfix: Google Chart background set explicient to transparent.</li>
<li>Bugfix: Delivered electricity year report scale is now correct calculated.</li>
<li>Bugfix: Monthly max power chart is now working correct with 3 solar converters active.</li>
<li>Bugfix: Data capturing is now working correct for hosola solar converter.</li>
<li>Bugfix: If only solar converters are configured then data processing is now working fine.</li>
</ul>
</div>';

$note[10] = '<div class="subparagraph">Version 1.0 (24-04-2016)</div>
<div class="large_text">
<ul>
<li>General: Improve main and setting menu layout.</li>
<li>General: Added support for maximum 3 solar converter meters.</li>
<li>General: Added support for Hosola Bright solar converter meter.</li>
<li>General: Added export data page.</li>
<li>General: Added system overview page.</li>
<li>General: Added setting categories.</li>
<li>General: Added setting option to set initial solar converter reading.</li>
<li>General: Added setting option to disable gas meter.</li>
<li>General: Solar converter sensor scripts are now PHP based.</li>
<li>General: Refactor database model - All entity names are now english.</li>
</ul>
</div>';

$note[9] = '<div class="subparagraph">Version 0.9 (04-03-2016)</div>
<div class="large_text">
<ul>
<li>General: Added security shield arround WebGUI based on sessionId.</li>
<li>General: Improve customer query report.</li>
<li>General: Redesign solar deliverable charts (Now they are more flexible).</li>
<li>General: Added option to select solar meter vendor.</li>
<li>General: Improve theme and language hyperlink.</li>
<li>General: Added home password feature.</li>
<li>General: Added setting password feature.</li>
<li>General: If database connection fails, user is informed.</li>
</ul>
</div>';

$note[8] = '<div class="subparagraph">Version 0.8 (23-02-2016)</div>
<div class="large_text">
<ul>
<li>Hot fix release to help one of the launching customers!</li>
<li>Bugfix: Fix 3 nasty bugs which only occur the first installed day.</li>
<li>Bugfix: Used gas years report is now showing correct data.</li>
<li>Bugfix: Kampstrup energy sensor script is now working correct.</li>
</ul>
</div>';

$note[7] = '<div class="subparagraph">Version 0.7 (20-02-2016)</div>
<div class="large_text">
<ul>
<li>General: Added CSV export feature.</li>
<li>General: Improve customer query output.</li>
<li>General: Add day name to day reports.</li>
<li>General: Improve main menu when solar and/or weather station is disabled.</li>
<li>General: Added slide show mode. Year reports automatic cycle without human interaction.</li>
<li>General: Added option to enable chart legend.</li>
<li>General: Added option to set chart dimenision.</li>
<li>General: Setting page support now combobox to prevent invalid input.</li>
<li>General: Add support for Kampstrup energy meters.</li>
<li>General: Add support for Landis energy meters.</li>
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
<li>Imported CSV solar data files in database</li> 
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
<li>Store solar data in CSV files.</li>
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
