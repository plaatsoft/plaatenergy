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

$gas = plaatenergy_post("gas", 0);

/*
** ---------------------
** EVENTS
** ---------------------
*/

function plaatenergy_day_in_gas_edit_save_event() {

   // input
	global $gas;
	global $date;

	$sql  = 'select dal as low, piek as normal from energy where ';
	$sql .= 'timestamp="'.$date.' 00:00:00" order by timestamp asc limit 0,1';

	$result = plaatenergy_db_query($sql);
	$row = plaatenergy_db_fetch_object($result);
	
	if (isset($row->low)) {  
		$sql = 'update energy set gas='.$gas.' where timestamp="'.$date.' 00:00:00"';		
	} else {	  
		$sql  = 'insert into energy ( timestamp, gas) values ("'.$date.' 00:00:00",'.$gas.')';
   }
 
	plaatenergy_db_query($sql);
	plaatenergy_process(EVENT_PROCESS_ALL_DAYS);
}

/*
** ---------------------
** PAGE
** ---------------------
*/

function plaatenergy_day_in_gas_edit_page() {

   // input
   global $pid;
	global $eid;
	
	global $date;	
	global $gas;
	
	list($year, $month, $day) = explode("-", $date);	
		
	$sql1  = 'select gas from energy where ';
	$sql1 .= 'timestamp<"'.$date.' 00:00:00" order by timestamp desc limit 0,1';
	$result1 = plaatenergy_db_query($sql1);
	$row1 = plaatenergy_db_fetch_object($result1);
	
	$prev_gas=0;
	if ( isset($row1->gas)) {
		$prev_gas = $row1->gas;
	}

	// -------------------------------------

	$sql2  = 'select gas from energy where ';
	$sql2 .= 'timestamp>"'.$date.' 00:00:00" order by timestamp asc limit 0,1';
	$result2 = plaatenergy_db_query($sql2);
	$row2 = plaatenergy_db_fetch_object($result2);

	$next_gas=999999;
	if ( isset($row2->gas)) {
		$next_gas = $row2->gas;
	}
	
	// -------------------------------------

	$gas = $prev_gas + round((($next_gas-$prev_gas)/2),1);
	if (isset($_POST["gas"])) {
		$gas = $_POST["gas"];
	}

	// -------------------------------------

	$sql3  = 'select gas from energy where ';
	$sql3 .= 'timestamp="'.$date.' 00:00:00" order by timestamp asc limit 0,1';
	$result3 = plaatenergy_db_query($sql3);
	$row3 = plaatenergy_db_fetch_object($result3);
	
	$found=0;
	if (isset($row3->gas)) {
		$found=1;
		if ($eid!=EVENT_SAVE) {
			$gas = $row3->gas;
		}
	}

	// -------------------------------------

	$page  = ' <h1>'.t('TITLE_IN_GAS_EDIT').' '.$day.'-'.$month.'-'.$year.'</h1>';

	$page .= '<br/>';
	$page .= '<label>'.t('LABEL_GAS').':</label>';
	$page .= '<br/>';
	$page .= '<br/>';
	$page .= $prev_gas.' - ';
	$page .= '<input type="text" name="gas" value="'.$gas.'" size="6" />';
	$page .= ' - '.$next_gas;
	$page .= '<br/>';
	
	$page .= '<input type="hidden" name="do" value="1" />';
	$page .= '<br/>';

	// -------------------------------------
	
	if ($eid==EVENT_SAVE) {
		$page .= t('RECORD_SAVED');
	}
	
	// -------------------------------------
 
	$page .= '<div class="nav">';
	$page .= plaatenergy_link('pid='.PAGE_HOME, t('LINK_HOME'), 'home');
	$page .= plaatenergy_link('pid='.$pid.'&eid='.EVENT_SAVE.'&date='.$date, t('LINK_SAVE'));
	$page .= '</div>';
	
	return $page;
}

/*
** ---------------------
** HANDLER
** ---------------------
*/

function plaatenergy_day_in_gas_edit() {

  /* input */
  global $pid;
  global $eid;

  /* Event handler */
  switch ($eid) {
      
     case EVENT_SAVE:
        plaatenergy_day_in_gas_edit_save_event();
        break;
   }

  /* Page handler */
  switch ($pid) {

     case PAGE_DAY_IN_GAS_EDIT:
        echo plaatenergy_day_in_gas_edit_page();
        break;
	}
}

/*
** ---------------------
** THE END
** ---------------------
*/

?>
