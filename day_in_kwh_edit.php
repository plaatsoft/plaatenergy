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

$low_used = plaatenergy_post("low_used", 0);
$normal_used = plaatenergy_post("normal_used", 0);
$low_delivered = plaatenergy_post("low_delivered", 0);
$normal_delivered = plaatenergy_post("normal_delivered", 0);

/*
** ---------------------
** EVENTS
** ---------------------
*/

function plaatenergy_day_in_edit_save_event() {

   // input
	global $eid;
	global $date;

	global $low_used;
	global $normal_used;
	global $low_delivered;
	global $normal_delivered;

	$sql  = 'select dal as low_used, piek as normal_used, dalterug as low_delivered, piekterug as normal_deliverd ';
	$sql .= 'from energy where timestamp="'.$date.' 00:00:00" order by timestamp asc limit 0,1';

	$result = plaatenergy_db_query($sql);
	$row = plaatenergy_db_fetch_object($result);
	
	if (isset($row->low_used)) {  
	
		$sql  = 'update energy set dal='.$low_used.', piek='.$normal_used.', dalterug='.$low_delivered.', piekterug='.$normal_delivered.' ';
		$sql .= 'where timestamp="'.$date.' 00:00:00"';		
		
	} else {	  
	
		$sql  = 'insert into energy ( timestamp, dal, piek, dalterug, piekterug) values ("'.$date.' 00:00:00",'.$low_used.','.$normal_used.',';
		$sql .= $low_delivered.','.$normal_delivered.')';
   }
 	
	plaatenergy_db_query($sql);
	plaatenergy_db_process(EVENT_PROCESS_ALL_DAYS);
	
	$eid = EVENT_NONE;
}

/*
** ---------------------
** PAGE
** ---------------------
*/

function plaatenergy_day_in_edit_page() {

   // input
   global $pid;
	global $eid;
	global $date;
	
	global $low_used;
	global $normal_used;
	global $low_deliverd;
	global $normal_deliverd;
	
	list($year, $month, $day) = explode("-", $date);	
		
	$sql1  = 'select dal as low_used, piek as normal_used, dalterug as low_delivered, piekterug as normal_delivered ';
	$sql1 .= 'from energy where timestamp<"'.$date.' 00:00:00" order by timestamp desc limit 0,1';

	$result1 = plaatenergy_db_query($sql1);
	$row1 = plaatenergy_db_fetch_object($result1);
	
	$prev_low_used=0;
	$prev_normal_used=0;
	$prev_low_delivered=0;
	$prev_normal_delivered=0;
	
	if ( isset($row1->low_used)) {
		$prev_low_used = $row1->low_used;
		$prev_normal_used = $row1->normal_used;
		$prev_low_delivered = $row1->low_delivered;
		$prev_normal_delivered = $row1->normal_delivered;
	}

	// -------------------------------------

	$sql2  = 'select dal as low_used, piek as normal_used, dalterug as low_delivered, piekterug as normal_delivered ';
	$sql2 .= 'from energy where timestamp>"'.$date.' 00:00:00" order by timestamp asc limit 0,1';

	$result2 = plaatenergy_db_query($sql2);
	$row2 = plaatenergy_db_fetch_object($result2);

	$next_low_used = 999999;
	$next_normal_used = 999999;
	$next_low_delivered = 999999;
	$next_normal_delivered = 999999;
	
	if ( isset($row2->low_used)) {
		$next_low_used = $row2->low_used;
		$next_normal_used = $row2->normal_used;
		$next_low_delivered = $row2->low_delivered;
		$next_normal_delivered = $row2->normal_delivered;
	}
	
	// -------------------------------------

	$low_used = $prev_low_used + round((($next_low_used-$prev_low_used)/2),1);
	if (isset($_POST["low_used"])) {
		$low_used = $_POST["low_used"];
	}
	
	$normal_used = $prev_normal_used + round((($next_normal_used-$prev_normal_used)/2),1);
	if (isset($_POST["normal_used"])) {
		$normal_used = $_POST["normal_used"];
	}
	
	$low_delivered = $prev_low_delivered + round((($next_low_delivered-$prev_low_delivered)/2),1);
	if (isset($_POST["low_delivered"])) {
		$low_delivered = $_POST["low_delivered"];
	}
	
	$normal_delivered = $prev_normal_delivered + round((($next_normal_delivered-$prev_normal_delivered)/2),1);
	if (isset($_POST["normal_delivered"])) {
		$normal_delivered = $_POST["normal_delivered"];
	}

	// -------------------------------------

	$sql3  = 'select dal as low_used, piek as normal_used, dalterug as low_delivered, piekterug as normal_delivered ';
	$sql3 .= 'from energy where timestamp="'.$date.' 00:00:00" order by timestamp asc limit 0,1';
	$result3 = plaatenergy_db_query($sql3);
	$row3 = plaatenergy_db_fetch_object($result3);
	
	$found=0;
	if (isset($row3->low_used)) {
		$found=1;
		if ($eid!=EVENT_SAVE) {
			$low_used = $row3->low_used;
			$normal_used = $row3->normal_used;
			$low_delivered = $row3->low_delivered;
			$normal_delivered = $row3->normal_delivered;
		}
	}

	// -------------------------------------

	$page  = ' <h1>'.t('TITLE_IN_KWH_EDIT').' '.$day.'-'.$month.'-'.$year.'</h1>';

	$page .= '<br/>';
	$page .= '<label>'.t('LABEL_LOW_USED').':</label>';
	$page .= '<br/>';
	$page .= '<br/>';
	$page .= $prev_low_used.' - ';
	$page .= '<input type="text" name="low_used" value="'.$low_used.'" size="6" />';
	$page .= ' - '.$next_low_used;
	$page .= '<br/>';
	
	// -------------------------------------

	$page .= '<br/>';
	$page .= '<label>'.t('LABEL_NORMAL_USED').':</label>';
	$page .= '<br/>';
	$page .= '<br/>';
	$page .= $prev_normal_used.' - ';
	$page .= '<input type="text" name="normal_used" value="'.$normal_used.'" size="6" />';
	$page .= ' - '.$next_normal_used;
	$page .= '<br/>';
	
	// -------------------------------------

	$page .= '<br/>';
	$page .= '<label>'.t('LABEL_LOW_DELIVERED').':</label>';
	$page .= '<br/>';
	$page .= '<br/>';
	$page .= $prev_low_delivered.' - ';
	$page .= '<input type="text" name="low_delivered" value="'.$low_delivered.'" size="6" />';
	$page .= ' - '.$next_low_delivered;
	$page .= '<br/>';
	
	// -------------------------------------

	$page .= '<br/>';
	$page .= '<label>'.t('LABEL_NORMAL_DELIVERED').':</label>';
	$page .= '<br/>';
	$page .= '<br/>';
	$page .= $prev_normal_delivered.' - ';
	$page .= '<input type="text" name="normal_delivered" value="'.$normal_delivered.'" size="6" />';
	$page .= ' - '.$next_normal_delivered;
	$page .= '<br/>';
	
	$page .= '<br/>';
	
	// -------------------------------------
 
	$page .= '<div class="nav">';
	$page .= plaatenergy_link('pid='.PAGE_DAY_IN_ENERGY.'&date='.$date, t('LINK_CANCEL'));
	$page .= plaatenergy_link('pid='.PAGE_DAY_IN_ENERGY.'&eid='.EVENT_SAVE.'&date='.$date, t('LINK_SAVE'));
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
        return plaatenergy_day_in_edit_page();
        break;
  }
}

/*
** ---------------------
** THE END
** ---------------------
*/

?>
