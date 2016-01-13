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

$start = plaatenergy_post("start", "");
$end = plaatenergy_post("end", "");

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
	
		$page  =  'low_used='.round($row->dal,2).' ';
		$page .=  'normal_used='.round($row->piek,2).' ';
		$page .=  'low_delivered='.round($row->dalterug,2).' ';
		$page .=  'normal_delivered='.round($row->piekterug,2).' ';
		$page .=  'solar='.round($row->solar,2).' ';
		$page .=  'gas='.round($row->gas,2).' ';

		return $page;
	}
}

function plaatenergy_report_page() {

	/* input */
	global $pid;

	global $start;
	global $end;
	
	$page  =  '<h1>'.t('TITLE_QUERY_REPORT').'</h1>';

	$page .=  '<label>'.t('LABEL_START_DATE').': </label>';
	$page .=  '<br/>';
	$page .=  '<input name="start" type="date" size="10" maxlength="10" value="'.$start.'"/>';
	$page .=  '<br/>';
	$page .=  '<br/>';
	$page .=  '<label>'.t('LABEL_END_DATE').': </label>';
	$page .=  '<br/>';
	$page .=  '<input name="end" type="date" size="10" maxlength="10" value="'.$end.'"/>';
	$page .=  '<br/>';
	$page .=  '<br/>';
	
	$page .=  plaatenergy_report_event();
	 	
	$page .= '<div class="nav">';
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
  
  /* Page handler */
  switch ($pid) {

     case PAGE_REPORT:
        echo plaatenergy_report_page();
        break;
  }
}

/*
** ---------------------
** THE END
** ---------------------
*/


?>
