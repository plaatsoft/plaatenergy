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

/*
** ---------------------
** EVENTS
** ---------------------
*/

function plaatenergy_day_out_edit_save_event() {

   // input
	global $etotal;
	global $date;
	global $eid;
	
	$sql  = 'select etotal FROM solar where timestamp="'.$date.' 00:00:00"';
	$result = plaatenergy_db_query($sql);
	$row = plaatenergy_db_fetch_object($result);
	
	$prev_etotal=0;
	if ( isset($row->etotal) ) {
		$sql = 'update solar set etotal='.$etotal.' where timestamp="'.$date.' 00:00:00"';	
	} else {			
		$sql  = 'insert into solar (`timestamp`, `etotal`) ';
		$sql .= 'values ("'.$date.' 00:00:00","'.$etotal.'")';
	}
	
	plaatenergy_db_query($sql);
	plaatenergy_db_process(EVENT_PROCESS_ALL_DAYS);
	
	$eid = EVENT_NONE;
}

/*
** ---------------------
** PAGES
** ---------------------
*/

function plaatenergy_day_out_edit_page() {

	// input
	global $date;	
	global $eid;
	global $pid;
	
	global $etotal;
	
	list($year, $month, $day) = explode("-", $date);	
	
	$sql1  = 'select etotal FROM solar where ';
	$sql1 .= 'timestamp<"'.$date.' 00:00:00" order by timestamp desc limit 0,1';
	$result1 = plaatenergy_db_query($sql1);
	$row1 = plaatenergy_db_fetch_object($result1);
	
	$prev_etotal=0;
	if ( isset($row1->etotal)) {
		$prev_etotal = $row1->etotal;
	}

	// -------------------------------------

	$sql2  = 'select etotal FROM solar where ';
	$sql2 .= 'timestamp>"'.$date.' 00:00:00" order by timestamp asc limit 0,1';
	$result2 = plaatenergy_db_query($sql2);
	$row2 = plaatenergy_db_fetch_object($result2);
	
	$next_etotal=999999;
	if ( isset($row2->etotal)) {
		$next_etotal = $row2->etotal;
	}
	
	// -------------------------------------

	$etotal=0;
	if (isset($_POST["etotal"])) {
		$etotal = $_POST["etotal"];
	} else {
	
	$etotal = round((($next_etotal+$prev_etotal)/2),1);
		if ($etotal<$prev_etotal) {
			$etotal=$prev_etotal;
		} 
	}
	
	// -------------------------------------

	$sql3  = 'select etotal FROM solar where ';
	$sql3 .= 'timestamp="'.$date.' 00:00:00" order by timestamp asc limit 0,1';

	$result3 = plaatenergy_db_query($sql3);
	$row3 = plaatenergy_db_fetch_object($result3);
	
	$found=0;
	if (isset($row3->etotal)) {
		$found=1;
		if ($eid!=EVENT_SAVE) {
			$etotal = $row3->etotal;
		}
	}
	
	$page  = ' <h1>'.t('TITLE_OUT_KWH_EDIT').' '.$day.'-'.$month.'-'.$year.'</h1>';

	$page .=  '<br/>';
	$page .=  '<label>'.t('LABEL_ETOTAL').':</label>';
	$page .=  '<br/>';
	$page .=  $prev_etotal.' - ';
	$page .=  '<input type="text" name="etotal" id="etotal" value="'.$etotal.'" size="7" />';
	$page .=  ' - '.$next_etotal;
	$page .=  '<br/>';
	$page .=  '<br/>';
	$page .=  '<input type="hidden" name="do" value="1" />';
		
	// -------------------------------------
 
	$page .= '<div class="nav">';
	$page .= plaatenergy_link('pid='.PAGE_DAY_OUT_ENERGY.'&date='.$date, t('LINK_CANCEL'));
	$page .= plaatenergy_link('pid='.PAGE_DAY_OUT_ENERGY.'&eid='.EVENT_SAVE.'&date='.$date, t('LINK_SAVE'));
	$page .= '</div>';
	
	return $page;
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
			return plaatenergy_day_out_edit_page();
			break;
  }
}

/*
** ---------------------
** THE END
** ---------------------
*/


?>
