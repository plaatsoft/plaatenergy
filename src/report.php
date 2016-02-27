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
	
	$sql  = 'select date, dal as low_used, piek as normal_used, '; 
	$sql .= 'dalterug as low_delivered, piekterug as normal_delivered, ';
	$sql .= 'solar as solar_delivered, gas as gas_used ';
	$sql .= 'FROM energy_day where date>="'.$start.'" and date<="'.$end.'" ';
	$sql .= 'order by date';

	$result = plaatenergy_db_query($sql);
	
	$csv  = '"'.t('TAG_DATE').'";';
	$csv .= '"'.t('TAG_LOW_USED').'";';
	$csv .= '"'.t('TAG_NORMAL_USED').'";';
	$csv .= '"'.t('TAG_SOLAR_USED').'";';
	$csv .= '"'.t('TAG_GAS_USED').'";'; 
	$csv .= '"'.t('TAG_LOW_DELIVERED').'";'; 
	$csv .= '"'.t('TAG_NORMAL_DELIVERED').'";';
	$csv .= '"'.t('TAG_SOLAR_DELIVERED')."\"\r\n";

	
	while ($row = plaatenergy_db_fetch_object($result)) {	
		$csv .=  '"'.$row->date.'";';
		$csv .=  '"'.plaatenergy_round($row->low_used,2).'";';
		$csv .=  '"'.plaatenergy_round($row->normal_used,2).'";';
		
		$tmp = $row->solar-$row->dalterug-$row->piekterug;
		if ($tmp<0) {
			$tmp=0;
		}
		
		$csv .=  '"'.plaatenergy_round($tmp,2).'";';
		$csv .=  '"'.plaatenergy_round($row->gas_used,2).'";';
		$csv .=  '"'.plaatenergy_round($row->low_delivered,2).'";';
		$csv .=  '"'.plaatenergy_round($row->normal_delivered,2).'";';
		$csv .=  '"'.plaatenergy_round($row->solar_delivered,2)."\"\r\n";		
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
		 
		$sql  = 'select sum(dal) as dal, sum(piek) as piek, sum(dalterug) as dalterug, sum(piekterug) as piekterug, ';
		$sql .= 'sum(solar) as solar, sum(gas) as gas FROM energy_day where date>="'.$start.'" and date<="'.$end.'"';
			
		$result = plaatenergy_db_query($sql);
		$row = plaatenergy_db_fetch_object($result);
	
		$tmp = $row->solar-$row->dalterug-$row->piekterug;
		if ($tmp<0) {
			$tmp=0;
		}
		
		$page  =  '&Sigma; '.t('TAG_LOW_USED').' = '.plaatenergy_round($row->dal,2).' '.t('KWH');
		$page .=  '<br/>';
		$page .=  '&Sigma; '.t('TAG_NORMAL_USED').' = '.plaatenergy_round($row->piek,2).' '.t('KWH');
		$page .=  '<br/>';
		$page .=  '&Sigma; '.t('TAG_SOLAR_USED').' = '.plaatenergy_round($tmp,2).' '.t('KWH');
		$page .=  '<br/>';
		$page .=  '&Sigma; '.t('TAG_GAS_USED').' = '.plaatenergy_round($row->gas,2).' '.t('M3');
		$page .=  '<br/>';
		$page .=  '<br/>';
		$page .=  '&Sigma; '.t('TAG_LOW_DELIVERED').' = '.plaatenergy_round($row->dalterug,2).' '.t('KWH');
		$page .=  '<br/>';
		$page .=  '&Sigma; '.t('TAG_NORMAL_DELIVERED').' = '.plaatenergy_round($row->piekterug,2).' '.t('KWH');
		$page .=  '<br/>';
		$page .=  '&Sigma; '.t('TAG_SOLAR_DELIVERED').' = '.plaatenergy_round($row->solar,2).' '.t('KWH');

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
