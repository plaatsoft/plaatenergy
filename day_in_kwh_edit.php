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
** PARAMETERS
** ---------------------
*/

$low = plaatenergy_post("low", 0);
$normal = plaatenergy_post("normal", 0);

day_parameters();

/*
** ---------------------
** EVENTS
** ---------------------
*/

function plaatenergy_day_in_edit_save_event() {

   // input
	global $low;
	global $normal;
	
	$timestamp = date("Y-m-d 00:00:00", $current_date);

	$sql  = 'select dal as low, piek as normal from energy where ';
	$sql .= 'timestamp="'.$timestamp.'" order by timestamp asc limit 0,1';

	$result = plaatenergy_db_query($sql);
	$row = plaatenergy_db_fetch_object($result);
	
	if (isset($row->low)) {
  
		$sql4 = 'update energy set dal='.$low.', piek='.$normal.' where timestamp="'.$timestamp.'"';
		
	} else {
	  
		$sql4  = 'insert into energy ( timestamp, dal, piek) values ("'.$timestamp.'",'.$low.','.$normal.')';
   }
 
	plaatenergy_db_query($sql4);
	plaatenergy_process(2);
}

/*
** ---------------------
** PAGE
** ---------------------
*/

function plaatenergy_day_in_edit_page() {

   // input
	global $low;
	global $normal;
	
   global $pid;
	global $eid;
   global $current_date;
	
	$timestamp = date("Y-m-d 00:00:00", $current_date);
	$sql1  = 'select dal as low, piek as normal FROM energy where ';
	$sql1 .= 'timestamp<"'.$timestamp.'" order by timestamp desc limit 0,1';

	$result1 = plaatenergy_db_query($sql1);
	$row1 = plaatenergy_db_fetch_object($result1);
	
	$prev_low=0;
	$prev_normal=0;
	if ( isset($row1->low)) {
		$prev_low = $row1->low;
		$prev_normal = $row1->normal;
	}

	// -------------------------------------

	$timestamp = date("Y-m-d 00:00:00", $current_date);
	
	$sql2  = 'select dal as low, piek as normal from energy where ';
	$sql2 .= 'timestamp>"'.$timestamp.'" order by timestamp asc limit 0,1';

	$result2 = plaatenergy_db_query($sql2);
	$row2 = plaatenergy_db_fetch_object($result2);

	$next_low=999999;
	$next_normal=999999;
	if ( isset($row2->low)) {
		$next_low = $row2->low;
		$next_normal = $row2->normal;
	}
	
	// -------------------------------------

	$low=$prev_low+round((($next_low-$prev_low)/2),1);
	if (isset($_POST["low"])) {
		$low = $_POST["low"];
	}
	$normal=$prev_normal+round((($next_normal-$prev_normal)/2),1);
	if (isset($_POST["normal"])) {
		$normal = $_POST["normal"];
	}

	// -------------------------------------

	$timestamp = date("Y-m-d 00:00:00", $current_date);

	$sql3  = 'select dal as low, piek as normal from energy where ';
	$sql3 .= 'timestamp="'.$timestamp.'" order by timestamp asc limit 0,1';

	$result3 = plaatenergy_db_query($sql3);
	$row3 = plaatenergy_db_fetch_object($result3);
	
	$found=0;
	if (isset($row3->low)) {
		$found=1;
		if ($do==0) {
			$low = $row3->low;
			$normal = $row3->normal;
		}
	}

	// -------------------------------------

	$page  = ' <h1>'.t('TITLE_IN_KWH_EDIT').' '.$day.'-'.$month.'-'.$year.'</h1>';

	$page .= '<br/>';
	$page .= '<label>'.t('LABEL_LOW').':</label>';
	$page .= '<br/>';
	$page .= '<br/>';
	$page .= $prev_low.' - ';
	$page .= '<input type="text" name="low" value="'.$low.'" size="6" />';
	$page .= ' - '.$next_low;
	$page .= '<br/>';
	
	// -------------------------------------

	$page .= '<br/>';
	$page .= '<label>'.t('LABEL_NORMAL').':</label>';
	$page .= '<br/>';
	$page .= '<br/>';
	$page .= $prev_normal.' - ';
	$page .= '<input type="text" name="normal" value="'.$normal.'" size="6" />';
	$page .= ' - '.$next_normal;
	$page .= '<br/>';
	$page .= '<br/>';
	$page .= '<input type="hidden" name="do" value="1" />';

	// -------------------------------------
	
	if ($eid==EVENT_SAVE) {
		$page .= t('RECORD_SAVED');
	}
	
	// -------------------------------------
 
	$page .= '<div class="nav">';
	$page .= plaatenergy_link('pid='.PAGE_HOME, t('LINK_HOME'), 'home');
	$page .= plaatenergy_link('pid='.$pid.'&eid='.EVENT_SAVE, t('LINK_SAVE'));
	$page .= '</div>';
	
	return $page;
}

/*
** ---------------------
** HANDLER
** ---------------------
*/

function plaatenergy_day_in_edit() {

  /* input */
  global $pid;
  global $eid;

  /* Event handler */
  switch ($eid) {
      
     case EVENT_SAVE:
        plaatenergy_day_in_edit_save_event();
        break;
   }

  /* Page handler */
  switch ($pid) {

     case PAGE_DAY_IN_KWH_EDIT:
        echo plaatenergy_day_in_edit_page();
        break;
  }
}

/*
** ---------------------
** THE END
** ---------------------
*/



?>
