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

$etotal = plaatenergy_post("etotal", 0);

day_parameters();

/*
** ---------------------
** EVENTS
** ---------------------
*/

function plaatenergy_day_out_edit_save_event() {

   // input
	global $etotal;
	global $prev_date;
	
	$timestamp1 = date("Y-m-d 00:00:00", $prev_date);
	$timestamp2 = date("Y-m-d 23:59:59", $prev_date);
	
	$sql1  = 'select max(etotal) as etotal FROM solar where ';
	$sql1 .= 'timestamp>="'.$timestamp1.'" and timestamp<="'.$timestamp2.'"';
	$result1 = plaatenergy_db_query($sql1);
	$row1 = plaatenergy_db_fetch_object($result1);
	
	$prev_etotal=0;
	if ( isset($row1->etotal)) {
		$prev_etotal = $row1->etotal;
	}
		
	$sql2  = 'insert into solar (`id`, `timestamp`, `etoday`, `etotal`) ';
	$sql2 .= 'values (null, "'.$year.'-'.$month.'-'.$day.' 00:00:00","'.($etotal-$prev_etotal).'","'.$etotal.'")';
	
	plaatenergy_db_query($sql2);

	plaatenergy_process(2);
}

/*
** ---------------------
** PAGES
** ---------------------
*/

function plaatenergy_day_out_edit_page() {

	// input
	global $prev_date;
	
	global $pid;
	global $etotal;
	
	$timestamp1 = date("Y-m-d 00:00:00", $prev_date);
	$timestamp2 = date("Y-m-d 23:59:59", $prev_date);
	$sql1  = 'select max(etotal) as etotal FROM solar where ';
	$sql1 .= 'timestamp>="'.$timestamp1.'" and timestamp<="'.$timestamp2.'"';
	
	$result1 = plaatenergy_db_query($sql1);
	$row1 = plaatenergy_db_fetch_object($result1);
	
	$prev_etotal=0;
	if ( isset($row1->etotal)) {
		$prev_etotal = $row1->etotal;
	}

	$timestamp1 = date("Y-m-d 00:00:00", $next_date);
	$timestamp2 = date("Y-m-d 23:59:59", $next_date+(86400*2));

	$sql2  = 'select min(etotal) as etotal FROM solar where ';
	$sql2 .= 'timestamp>="'.$timestamp1.'" and timestamp<="'.$timestamp2.'"';
	
	$result2 = plaatenergy_db_query($sql2);
	$row2 = plaatenergy_db_fetch_object($result2);
	
	$next_etotal=999999;
	if ( isset($row2->etotal)) {
		$next_etotal = $row2->etotal;
	}

	$etotal=0;
	if (isset($_POST["etotal"])) {
		$etotal = $_POST["etotal"];
	} else {
	
	$etotal = round((($next_etotal+$prev_etotal)/2),1);
		if ($etotal<$prev_etotal) {
			$etotal=$prev_etotal;
		} 
	}

	$page  = ' <h1>'.t('TITLE_OUT_KWH_EDIT').' '.$day.'-'.$month.'-'.$year.'</h1>';

	$page .=  '<br/>';
	$page .=  '<label>'.t('LABEL_ETOTAL').':</label>';
	$page .=  '<br/>';
	$page .=  '<br/>';
	$page .=  $prev_etotal.' - ';
	$page .=  '<input type="text" name="etotal" id="etotal" value="'.$etotal.'" size="7" />';
	$page .=  ' - '.$next_etotal;
	$page .=  '<br/>';
	$page .=  '<br/>';
	$page .=  '<input type="hidden" name="do" value="1" />';
	
	// -------------------------------------
	
	if ($eid==EVENT_SAVE) {
		$page .= t('RECORD_SAVED');
	}
	
	// -------------------------------------
 
	$page .= '<div class="nav">';
	$page .= plaatenergy_link('pid='.PAGE_HOME, t('LINK_HOME'), 'home');
	$page .= plaatenergy_link('pid='.$pid.'&eid='.EVENT_SAVE, t('LINK_SAVE'));
	$page .= '</div>';
}

/*
** ---------------------
** HANDLER
** ---------------------
*/

function plaatenergy_day_out_edit() {

  /* input */
  global $pid;
  global $eid;

  /* Event handler */
  switch ($eid) {
      
     case EVENT_SAVE:
			plaatenergy_day_out_edit_save_event();
			break;
   }

  /* Page handler */
  switch ($pid) {

     case PAGE_DAY_OUT_KWH_EDIT:
			$page .=  plaatenergy_day_out_edit_page();
			break;
  }
}

/*
** ---------------------
** THE END
** ---------------------
*/


?>
