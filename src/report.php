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
 * @brief contain customer reports and exports
 */
 
/*
** ---------------------
** PARAMETERS
** ---------------------
*/

$start = plaatenergy_post("start", date('Y-m-d'));
$end = plaatenergy_post("end", date('Y-m-d'));

/*
** ---------------------
** EVENTS
** ---------------------
*/

function plaatenergy_round($number) {

   return number_format((float)$number, 2, ',', '');
}

function plaatenergy_export_event() {

	/* input */
	global $start;
	global $end;
	
	$sql  = 'select date, low_used, normal_used, low_delivered, normal_delivered, ';
	$sql .= 'solar_delivered, gas_used ';
	$sql .= 'FROM energy_summary where date>="'.$start.'" and date<="'.$end.'" ';
	$sql .= 'order by date';

	$result = plaatenergy_db_query($sql);
	
	$csv  = '"'.t('TAG_DATE').'";';
	
	$csv .= '"'.t('TAG_LOW_USED').'";';
	$csv .= '"'.t('TAG_NORMAL_USED').'";';
	$csv .= '"'.t('TAG_LOCAL_USED').'";';
	$csv .= '"'.t('TAG_TOTAL_USED').'";';
		
	$csv .= '"'.t('TAG_LOW_DELIVERED').'";'; 
	$csv .= '"'.t('TAG_NORMAL_DELIVERED').'";';
	$csv .= '"'.t('TAG_LOCAL_DELIVERED').'";';
	$csv .= '"'.t('TAG_TOTAL_DELIVERED').'";';
		
	$csv .= '"'.t('TAG_GAS_USED').'"';
	$csv .= "\r\n";

	
	while ($row = plaatenergy_db_fetch_object($result)) {	
				
		$local_used = $row->solar_delivered - $row->low_delivered - $row->normal_delivered;
		if ($local_used<0) {
			$local_used=0;
		}
		
		$total_used = $row->low_used + $row->normal_used + $local_used;		
		$total_delivered = $local_used + $row->low_delivered + $row->normal_delivered;
				
		$csv .=  '"'.$row->date.'";';
		
		$csv .=  '"'.plaatenergy_round($row->low_used,2).'";';
		$csv .=  '"'.plaatenergy_round($row->normal_used,2).'";';		
		$csv .=  '"'.plaatenergy_round($local_used,2).'";';
		$csv .=  '"'.plaatenergy_round($total_used,2).'";';
		
				
		$csv .=  '"'.plaatenergy_round($row->low_delivered,2).'";';
		$csv .=  '"'.plaatenergy_round($row->normal_delivered,2).'";';
		$csv .=  '"'.plaatenergy_round($local_used,2).'";';
		$csv .=  '"'.plaatenergy_round($total_delivered,2).'";';
		
		$csv .=  '"'.plaatenergy_round($row->gas_used,2).'"';		
		$csv .= "\r\n";
	}

	header('Content-Type: application/csv');
	header('Content-Disposition: attachment; filename=export.csv');
	header('Pragma: no-cache');

	echo $csv;
}


function plaatenergy_report_event() {

	/* input */
	global $start;
	global $end;
	
	global $eid;

	if ($eid==EVENT_EXECUTE) {
		 
		$sql  = 'select sum(low_used) as low_used, sum(normal_used) as normal_used, ';
		$sql .= 'sum(low_delivered) as low_delivered, sum(normal_delivered) as normal_delivered, ';
		$sql .= 'sum(solar_delivered) as solar_delivered, sum(gas_used) as gas_used ';
		$sql .= 'FROM energy_summary where date>="'.$start.'" and date<="'.$end.'"';
			
		$result = plaatenergy_db_query($sql);
		$row = plaatenergy_db_fetch_object($result);
	
		$local_used = $row->solar_delivered - $row->low_delivered - $row->normal_delivered;
		if ($local_used<0) {
			$local_used=0;
		}
		
		$total_used = $row->low_used + $row->normal_used + $local_used;
		$total_delivered = $local_used + $row->low_delivered + $row->normal_delivered;
				
		$page  =  '&Sigma; '.t('TAG_LOW_USED').' = '.plaatenergy_round($row->low_used,2).' '.t('KWH');
		$page .=  '<br/>';
		$page .=  '&Sigma; '.t('TAG_NORMAL_USED').' = '.plaatenergy_round($row->normal_used,2).' '.t('KWH');
		$page .=  '<br/>';
		$page .=  '&Sigma; '.t('TAG_LOCAL_USED').' = '.plaatenergy_round($local_used,2).' '.t('KWH');
		$page .=  '<br/>';		
		$page .=  '&Sigma; '.t('TAG_TOTAL_USED').' = '.plaatenergy_round($total_used,2).' '.t('KWH');
		
		$page .=  '<br/>';
		$page .=  '<br/>';
		$page .=  '&Sigma; '.t('TAG_LOW_DELIVERED').' = '.plaatenergy_round($row->low_delivered,2).' '.t('KWH');
		$page .=  '<br/>';
		$page .=  '&Sigma; '.t('TAG_NORMAL_DELIVERED').' = '.plaatenergy_round($row->normal_delivered,2).' '.t('KWH');
		$page .=  '<br/>';		
		$page .=  '&Sigma; '.t('TAG_LOCAL_DELIVERED').' = '.plaatenergy_round($local_used,2).' '.t('KWH');
		$page .=  '<br/>';
		$page .=  '&Sigma; '.t('TAG_TOTAL_DELIVERED').' = '.plaatenergy_round($total_delivered,2).' '.t('KWH');
		
		$page .=  '<br/>';
		$page .=  '<br/>';
		$page .=  '&Sigma; '.t('TAG_GAS_USED').' = '.plaatenergy_round($row->gas_used,2).' '.t('M3');
		$page .=  '<br/>';
		

		return $page;
	}
}

/*
** ---------------------
** PAGES
** ---------------------
*/

function plaatenergy_report_page() {

	/* input */
	global $pid;

	global $start;
	global $end;
	
	$page  =  '<h1>'.t('TITLE_QUERY_REPORT').'</h1>';
	
	$page .=  '<br/>';
	$page .=  '<label>'.t('LABEL_START_DATE').': </label>';
	$page .=  '<input name="start" type="date" size="10" maxlength="10" value="'.$start.'"/>';
	
	$page .=  '<br/>';
	$page .=  '<br/>';
	
	$page .=  '<label>'.t('LABEL_END_DATE').': </label>';
	$page .=  '<input name="end" type="date" size="10" maxlength="10" value="'.$end.'"/>';
	$page .=  '<br/>';
	$page .=  '<br/>';
	
	$page .=  plaatenergy_report_event();
	 	
	$page .= '<div class="nav">';
	$page .= plaatenergy_link('pid='.$pid.'&eid='.EVENT_EXPORT, t('LINK_EXPORT'));
	$page .= plaatenergy_link('pid='.PAGE_HOME, t('LINK_HOME'));
	$page .= plaatenergy_link('pid='.$pid.'&eid='.EVENT_EXECUTE, t('LINK_EXECUTE'));
	$page .=  '</div>';
	
	return $page;	
}
	
/*
** ---------------------
** HANDLER
** ---------------------
*/

function plaatenergy_report() {

  /* input */
  global $pid;
  global $eid;

  /* Page handler */
  switch ($eid) {

     case EVENT_EXPORT:
        return plaatenergy_export_event();
        exit;
  }
  
  /* Page handler */
  switch ($pid) {

     case PAGE_REPORT:
        return plaatenergy_report_page();
        break;
  }
}

/*
** ---------------------
** THE END
** ---------------------
*/


?>
