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

function plaatenergy_backup_event() {

	/* input */
	global $dbuser;
	global $dbpass;
	global $dbhost;
	global $dbname;
	
	$filename = plaatenergy_db_get_config_item('system_name', LOOK_AND_FEEL);
	if (strlen($filename)==0) {
		$filename=t('TITLE');
	}
	$filename = strtolower($filename);
	
	/* Create new database backup file */
	$filename = BASE_DIR.'/backup/'.$filename.'-'.date("Ymd").'.sql';

    /* Remove old file if it exists */
    @unlink($filename.'.gz');

        /* Make mysql backup */	
	$command = 'mysqldump --user='.$dbuser.' --password='.$dbpass.' --host='.$dbhost.' '.$dbname.' > '.$filename;
	system($command);
	
    /* Zip database dump file */	
	$command = 'gzip '.$filename;
	system($command);
}

function plaatenergy_delete_file_event() {

	/* input */
	global $filename;
	
	unlink (BASE_DIR.'/backup/'.$filename);
}

	/*
** ---------------------
** PAGES
** ---------------------
*/

function plaatenergy_export_import_page() {

	/* input */
	global $pid;

	global $start;
	global $end;
	
	$page  =  '<h1>'.t('TITLE_EXPORT_IMPORT').'</h1>';
	
	$page .=  '<br/>';
	
	$page .= '<fieldset>';
	
	$page .=  '<label>'.t('LABEL_START_DATE').': </label>';
	$page .=  '<input name="start" type="date" size="10" maxlength="10" value="'.$start.'"/>';
	
	$page .=  '<br/>';
	$page .=  '<br/>';
	
	$page .=  '<label>'.t('LABEL_END_DATE').': </label>';
	$page .=  '<input name="end" type="date" size="10" maxlength="10" value="'.$end.'"/>';
	$page .=  '<br/>';
	$page .=  '<br/>';
	
	$page .= '<div class="nav">';
	$page .= plaatenergy_link('pid='.$pid.'&eid='.EVENT_EXPORT, t('LINK_EXPORT'));
	$page .=  '</div>';
	
	$page .= '</fieldset>';
	
	$page .=  '<br/>';
	
	// ------------------
	
	$page .= '<fieldset>';
	
	$page .= '<div class="nav">';
	
	$tmp = '';
	if (@$dh = opendir(BASE_DIR.'/backup')) {
		while (false !== ($filename = readdir($dh))) {
	
			if (($filename!='.') && ($filename!='..') && ($filename!='.htaccess') && ($filename!='index.php') && ($filename!='readme.txt')) {
				$tmp .= '<tr>';
				$tmp .= '<td><a href="backup/'.$filename.'">'.$filename.'</a></td>';
				$tmp .= '<td>&nbsp;</td>';
				$tmp .= '<td>'.plaatenergy_link('pid='.$pid.'&eid='.EVENT_DELETE.'&filename='.$filename, t('LINK_DELETE')).'</td>';
				$tmp .= '</tr>';
			}
		}	
	}
	
	if (strlen($tmp)>0) {
		
		$page .= '<table>';
		$page .= $tmp;
		$page .= '</table>';
		$page .= '<br/>';
		$page .= '<br/>';
	}
	
	$page .= plaatenergy_link('pid='.$pid.'&eid='.EVENT_BACKUP, t('LINK_BACKUP'));
	$page .= '<br/>';
	$page .=  '</div>';
	
	$page .= '</fieldset>';
	
	// ------------------
	
	$page .= '<div class="nav">';
	$page .= plaatenergy_link('pid='.PAGE_HOME, t('LINK_HOME'));	
	$page .=  '</div>';
	
	return $page;	
}
	
/*
** ---------------------
** HANDLER
** ---------------------
*/

function plaatenergy_export() {

  /* input */
  global $pid;
  global $eid;

  /* Page handler */
  switch ($eid) {

		case EVENT_EXPORT:
        return plaatenergy_export_event();
        exit;
		  
		case EVENT_DELETE:
			plaatenergy_delete_file_event();
			break;
		  
		case EVENT_BACKUP:
			plaatenergy_backup_event();
			break;
  }
  
  /* Page handler */
  switch ($pid) {

     case PAGE_EXPORT_IMPORT:
        return plaatenergy_export_import_page();
        break;
  }
}

/*
** ---------------------
** THE END
** ---------------------
*/


?>
