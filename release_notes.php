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

$max=5;

function plaatenergy_release_notes_page(){

  global $pid;
  global $id;
  
  $page  = '<h1>Release Notes</h1>';
  $page .= '<br/>';

  if ($id==5) {
    
		$page .= '<div class="subparagraph">Version 0.5 (18-01-2016)</div>';
		$page .= '<div class="large_text">';
		$page .= '<ul>';
		$page .= '<li>General: Move all configuration items to database.</li>';
		$page .= '<li>General: Add settings page.</li>';
		$page .= '<li>General: Add energy, gas, solar measurement correction page.</li>';
		$page .= '<li>General: Improve header and footer block of all pages.</li>';  
		$page .= '<li>General: Improve error handeling. If something goes wrong user is better informed.</li>';
		$page .= '<li>Realtime Information: Add slider effect to information boxes.</li>';
		$page .= '<li>Realtime Information: Add sunrise and sunset information box based on location.</li>';
		$page .= '<li>Realtime Information: Add energy and gas carbon dioxide emission information box.</li>';
		$page .= '<li>Realtime Information: Setting slider contain now more options.</li>';
		$page .= '<li>Installation: Database is now automaticly created/patched during installation.</li>';	 
		$page .= '<li>Installation: Now only one cron job is needed.</li>';
		$page .= '<li>Installation: Python sensors scripts fetch device settings from database.</li>';
		$page .= '<li>Installation: When new version is available user is informed.</li>';
		$page .= '<li>Bugfix: Energy / Weather station meter connection down detection improved.</li>';
		$page .= '</ul>';
		$page .= '</div>';
  }

  if ($id==4) {
		$page .= '<div class="subparagraph">Version 0.4 (01-01-2016)</div>';
		$page .= '<div class="large_text">';
		$page .= '<ul>';
		$page .= '<li>Add forecast information to years and year reports.</li>';
		$page .= '<li>Add multi language support.</li>';
		$page .= '<li>Move dutch translation to resource file.</li>';
		$page .= '<li>Add english resource file.</li>';
		$page .= '<li>Add solar correction page to add missing measurement.</li>';
		$page .= '<li>Add release notes page.</li>';
		$page .= '<li>Add about page.</li>';
		$page .= '<li>Add donate page.</li>';
		$page .= '<li>Improve realtime info page. Now page has same dimensions as other pages.</li>';
		$page .= '<li>Add light and dark css theme (thanks bplaat).</li>';
		$page .= '<li>Add buttons icons (thanks bplaat).</li>';
		$page .= '</ul>';
		$page .= '</div>';
  }

  if ($id==3) {
		$page .= '<div class="subparagraph">Version 0.3 (30-11-2015)</div>';
		$page .= '<div class="large_text">';
		$page .= '<ul>';
		$page .= '<li>Added Energy, Solar, Gas years reports.</li>';
		$page .= '<li>Optimised database structure. Data is now aggregrated on day base.</li>';
		$page .= '<li>Update energy, solar, gas year reports.</li>';
		$page .= '<li>Update energy, solar, gas month reports.</li>';
		$page .= '<li>Update energy, solar, gas day reports.</li>';
		$page .= '<li>Move sensor python scripts to website directory structure.</li>';
		$page .= '<li>Add option to disable weather meter.</li>';
		$page .= '<li>Add option to disable solar meter.</li>';
		$page .= '<li>Add connection check to Energy, Solar and Weather meter.</li>';
		$page .= '<li>Realtime information GUI is improved.</li>';
		$page .= '</ul>';
		$page .= '</div>';
   }
 
  if ($id==2) {
		$page .= '<div class="subparagraph">Version 0.2 (31-10-2015)</div>';
		$page .= '<div class="large_text">';
		$page .= '<ul>';
		$page .= '<li>Created mysql database</li>';
		$page .= '<li>Imported all solar csv data in database</li>'; 
		$page .= '<li>Add P1 energy meter python sensor script.</li>';
		$page .= '<li>Add AstroHat weather sensor script.</li>';
		$page .= '<li>Add energy, solar, gas year reports.</li>';
		$page .= '<li>Add energy, solar, gas month reports.</li>';
		$page .= '<li>Add energy, solar, gas day reports.</li>';
		$page .= '<li>Add temperature, huminity, pressure day reports.</li>';
		$page .= '<li>Add realtime information GUI (Created by bplaat).</li>';
		$page .= '</ul>';
		$page .= '</div>';
   }
 
  if ($id==1) {
		$page .= '<div class="subparagraph">Version 0.1 (30-09-2015)</div>';
		$page .= '<div class="large_text">';
		$page .= '<ul>';
		$page .= '<li>Add solar meter python sensor script.</li>';
		$page .= '<li>Add solar year reports.</li>';
		$page .= '<li>Add solar month reports.</li>';
		$page .= '<li>Add solar day reports.</li>';
		$page .= '<li>Store solar in CSV file.</li>';
		$page .= '</ul>';
		$page .= '</div>';
   }

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

	if($id==0) {
		$id = $max;
	}
	
	/* Event handler */
	switch ($eid) {
      
		case EVENT_NEXT:
			if ($id<$max) {
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
