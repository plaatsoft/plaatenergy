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
 * @brief contain day energy out edit page
 */
 
/*
** ---------------------
** PARAMETERS
** ---------------------
*/

$etotal1 = plaatenergy_post("etotal1", 0);
$etotal2 = plaatenergy_post("etotal2", 0);
$etotal3 = plaatenergy_post("etotal3", 0);

$solar_meter_present1 = plaatenergy_db_get_config_item('solar_meter_present', SOLAR_METER_1);
$solar_meter_present2 = plaatenergy_db_get_config_item('solar_meter_present', SOLAR_METER_2);
$solar_meter_present3 = plaatenergy_db_get_config_item('solar_meter_present', SOLAR_METER_3);

/*
** ---------------------
** UTILS
** ---------------------
*/

function plaatenergy_day_out_save_db($date, $nr, $etotal) {

	$sql  = 'select etotal FROM solar'.$nr.' where timestamp="'.$date.' 00:00:00"';
	$result = plaatenergy_db_query($sql);
	$row = plaatenergy_db_fetch_object($result);
	
	if ( isset($row->etotal) ) {
		$sql = 'update solar'.$nr.' set etotal='.$etotal.' where timestamp="'.$date.' 00:00:00"';	
	} else {			
		$sql  = 'insert into solar'.$nr.' (`timestamp`, `etotal`) ';
		$sql .= 'values ("'.$date.' 00:00:00","'.$etotal.'")';
	}
	
	plaatenergy_db_query($sql);
}

function plaatenergy_prev_value($date, $nr=1) {

   $sql  = 'select etotal FROM solar'.$nr.' where ';
	$sql .= 'timestamp<"'.$date.' 00:00:00" order by timestamp desc limit 0,1';
	$result = plaatenergy_db_query($sql);
	$row = plaatenergy_db_fetch_object($result);
	
	$etotal=0;
	if ( isset($row->etotal)) {
		$etotal = $row->etotal;
	}
	
	return $etotal;
}

function plaatenergy_next_value($date, $nr=1) {

   $sql  = 'select etotal FROM solar'.$nr.' where ';
	$sql .= 'timestamp>"'.$date.' 00:00:00" order by timestamp asc limit 0,1';
	$result = plaatenergy_db_query($sql);
	$row = plaatenergy_db_fetch_object($result);
	
	$etotal=999999;
	if ( isset($row->etotal)) {
		$etotal = $row->etotal;
	}	
	return $etotal;
}

function plaatenergy_current_value($etotal, $date, $nr, $next_value, $prev_value) {

	global $eid;
	
	if (etotal==0) {
	   $etotal = round((($next_value+$prev_value)/2),1);
		if ($etotal<$prev_value) {
			$etotal = $prev_value;
		} 
	}

	$sql  = 'select etotal FROM solar'.$nr.' where ';
	$sql .= 'timestamp="'.$date.' 00:00:00" order by timestamp asc limit 0,1';
	$result = plaatenergy_db_query($sql);
	$row = plaatenergy_db_fetch_object($result);
	
	if (isset($row->etotal)) {
		if ($eid!=EVENT_SAVE) {
			$etotal = $row->etotal;
		}
	}
	return $etotal;
}

/*
** ---------------------
** EVENTS
** ---------------------
*/

function plaatenergy_day_out_edit_save_event() {

   // input
	global $etotal1;
	global $etotal2;
	global $etotal3;
	
	global $date;
	global $eid;

	global $solar_meter_present1;
	global $solar_meter_present2;
	global $solar_meter_present3;
	
	if ($solar_meter_present1=="true") {
		plaatenergy_day_out_save_db($date, 1, $etotal1);
	}
	
	if ($solar_meter_present2=="true") {
		plaatenergy_day_out_save_db($date, 2, $etotal2);
	}
	
	if ($solar_meter_present3=="true") {
		plaatenergy_day_out_save_db($date, 3, $etotal3);
	}
	
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
	
	global $etotal1;
	global $etotal2;
	global $etotal3;
	
	global $solar_meter_present1;
	global $solar_meter_present2;
	global $solar_meter_present3;
	
	list($year, $month, $day) = explode("-", $date);	
	
	$prev_etotal1 = plaatenergy_prev_value($date, 1);
	$prev_etotal2 = plaatenergy_prev_value($date, 2);
	$prev_etotal3 = plaatenergy_prev_value($date, 3);
	
	$next_etotal1 = plaatenergy_next_value($date, 1);
	$next_etotal2 = plaatenergy_next_value($date, 2);
	$next_etotal3 = plaatenergy_next_value($date, 3);
		
	$etotal1 = plaatenergy_current_value($etotal1, $date, 1, $next_etotal1, $prev_etotal1);
	$etotal2 = plaatenergy_current_value($etotal2, $date, 2, $next_etotal2, $prev_etotal2);
	$etotal3 = plaatenergy_current_value($etotal3, $date, 3, $next_etotal3, $prev_etotal3);
		
	// -------------------------------------

	$page  = ' <h1>'.t('TITLE_OUT_KWH_EDIT').' '.$day.'-'.$month.'-'.$year.'</h1>';

	$page .=  '<br/>';

	if ($solar_meter_present1=="true") {

		$page .=  '<label>'.t('LABEL_ETOTAL', 1).':</label>';
		$page .=  '<br/>';
		$page .=  $prev_etotal1.' - ';
		$page .=  '<input type="text" name="etotal1" id="etotal1" value="'.$etotal1.'" size="7" />';
		$page .=  ' - '.$next_etotal1;
		$page .=  '<br/>';
	}
	
	if ($solar_meter_present2=="true") {
		$page .=  '<br/>';
		$page .=  '<label>'.t('LABEL_ETOTAL', 2).':</label>';
		$page .=  '<br/>';
		$page .=  $prev_etotal2.' - ';
		$page .=  '<input type="text" name="etotal2" id="etotal2" value="'.$etotal2.'" size="7" />';
		$page .=  ' - '.$next_etotal2;
		$page .=  '<br/>';
	}
	
	if ($solar_meter_present3=="true") {	
		$page .=  '<br/>';
		$page .=  '<label>'.t('LABEL_ETOTAL', 3).':</label>';
		$page .=  '<br/>';
		$page .=  $prev_etotal3.' - ';
		$page .=  '<input type="text" name="etotal3" id="etotal3" value="'.$etotal3.'" size="7" />';
		$page .=  ' - '.$next_etotal3;
		$page .=  '<br/>';
	}

	$page .=  '<br/>';
 
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
