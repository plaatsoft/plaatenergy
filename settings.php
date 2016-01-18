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

$value = plaatenergy_post("value", "");

$sql  = 'select id, token, value from config where readonly=0';
$result = plaatenergy_db_query($sql);
$step = 5;
$max = round((plaatenergy_db_num_rows($result)/$step),0);
	
/*
** ---------------------
** EVENTS
** ---------------------
*/

function plaatenergy_setting_save_event() {

   // input
	global $id;
	global $value;

	$sql  = 'update config set value="'.$value.'" where id='.$id;		

	plaatenergy_db_query($sql);
	
	plaatenergy_process(EVENT_PROCESS_ALL_DAYS);
}

/*
** ---------------------
** PAGE
** ---------------------
*/

function plaatenergy_setting_edit_page() {

   // input
   global $id;
			
	$sql  = 'select token, value from config where id='.$id;
	$result = plaatenergy_db_query($sql);
	$row = plaatenergy_db_fetch_object($result);

	// -------------------------------------

	$page  = ' <h1>'.t('SETTING_TITLE').'</h1>';

	$page .= '<br/>';
	$page .= '<label>'.t($row->token).'</label>';
	$page .= '<br/>';
	$page .= '<input type="text" name="value" value="'.$row->value.'" size="40" />';
	$page .= '<br/>';

	// -------------------------------------
 
	$page .= '<div class="nav">';
	$page .= plaatenergy_link('pid='.PAGE_SETTING_LIST, t('LINK_CANCEL'));
	$page .= plaatenergy_link('pid='.PAGE_SETTING_LIST.'&eid='.EVENT_SAVE.'&id='.$id, t('LINK_SAVE'));
	$page .= '</div>';
	
	return $page;
}

function plaatenergy_setting_list_page() {

   // input
	global $pid;
	global $limit;
	global $step;

	$sql  = 'select id, token, value from config where readonly=0 order by id limit '.($limit*$step).','.$step;
	$result = plaatenergy_db_query($sql);
	
	// -------------------------------------

	$page  = ' <h1>'.t('SETTING_TITLE').'</h1>';

	$page .= '<br/>';
	
	$page .= '<table>';
	$page .= '<tr>';
	$page .= '<th width="200">'.t('LABEL_TOKEN').'</th>';
	$page .= '<th width="100">'.t('LABEL_VALUE').'</th>';
	$page .= '<th width="300">'.t('LABEL_DESCRIPTION').'</th>';
	$page .= '</tr>';
	
	while ($row = plaatenergy_db_fetch_object($result)) {
	
		$page .= '<tr>';
		$page .= '<td width="200">'.plaatenergy_link('pid='.PAGE_SETTING_EDIT.'&id='.$row->id, $row->token).'</td>';
		$page .= '<td width="100">'.$row->value.'</td>';
		$page .= '<td width="300">'.t($row->token).'</td>';
		$page .= '</tr>';
	}
	$page .= '</table>';
	
	// -------------------------------------
 
	$page .= '<div class="nav">';
	$page .= plaatenergy_link('pid='.$pid.'&eid='.EVENT_PREV.'&limit='.$limit, t('LINK_PREV'));
   $page .= plaatenergy_link('pid='.PAGE_HOME, t('LINK_HOME'));
   $page .= plaatenergy_link('pid='.$pid.'&eid='.EVENT_NEXT.'&limit='.$limit, t('LINK_NEXT'));
	$page .= '</div>';
	
	return $page;
}
/*
** ---------------------
** HANDLER
** ---------------------
*/

function plaatenergy_settings() {

  /* input */
  global $pid;
  global $eid;
  
  global $max;
  global $limit; 

  /* Event handler */
	switch ($eid) {
      
		case EVENT_NEXT:
			if ($limit<$max) {
				$limit++;
			}
			break;

		case EVENT_PREV:
			if ($limit>0) {
				$limit--;
			}
			break;
		
     case EVENT_SAVE:
        plaatenergy_setting_save_event();
        break;
   }

  /* Page handler */
  switch ($pid) {

		case PAGE_SETTING_EDIT:
			echo plaatenergy_setting_edit_page();
			break;
			
		case PAGE_SETTING_LIST:
			echo plaatenergy_setting_list_page();
			break;
  }
}

/*
** ---------------------
** THE END
** ---------------------
*/

?>
