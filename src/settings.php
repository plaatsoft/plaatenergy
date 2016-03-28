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
 * @brief contain setting page
 */
 
/*
** ---------------------
** PARAMETERS
** ---------------------
*/

$value = plaatenergy_post("value", "");
$password = plaatenergy_post("password", "");


$sql  = 'select id from config where category='.$cat.' and readonly=0';
$result = plaatenergy_db_query($sql);
$count = plaatenergy_db_num_rows($result);
$step = 6;
$max = 0;
if ($count>$step) {
	$max = 1;
}
	
/*
** ---------------------
** EVENTS
** ---------------------
*/

function plaatenergy_setting_login_event() {

	global $pid;
	global $password;
	
	$settings_password = plaatenergy_db_get_config_item('settings_password',SECURITY);
	
	if ($settings_password == $password) {
	
		// Correct password, redirect to setting page
		$pid = PAGE_SETTING_CATEGORY;
	}
}
	
function plaatenergy_setting_save_event() {

    // input
	global $id;
	global $value;

	$sql  = 'update config set value="'.$value.'", date=SYSDATE() where id='.$id;		
	plaatenergy_db_query($sql);
	
	$sql  = 'select rebuild from config where id='.$id;		
	$result = plaatenergy_db_query($sql);
	$row = plaatenergy_db_fetch_object($result);
	
	if ($row->rebuild==1) {
		plaatenergy_db_process(EVENT_PROCESS_ALL_DAYS);
	}
}


function plaatenergy_setting_backup_event() {

	/* input */
	global $dbuser;
	global $dbpass;
	global $dbhost;
	global $dbname;
		
	/* Create new database backup file */
	$filename = 'backup/plaatenergy-'.date("Ymd").'.sql';

    /* Remove old file if it exists */
    @unlink($filename.'.gz');

        /* Make mysql backup */	
	$command = 'mysqldump --user='.$dbuser.' --password='.$dbpass.' --host='.$dbhost.' '.$dbname.' > '.$filename;
	system($command);
	
    /* Zip database dump file */	
	$command = 'gzip '.$filename;
	system($command);
}

/*
** ---------------------
** PAGE
** ---------------------
*/

function plaatenergy_setting_login_page() {

   // input
   global $id;
			
   $page  = ' <h1>'.t('SETTING_TITLE').'</h1>';

   $page .= '<br/>';
   $page .= '<label>'.t('LABEL_PASSWORD').'</label>';
   $page .= '<input type="password" name="password" size="20" />';
   $page .= '<br/>';

   $page .= '<div class="nav">';
   $page .= plaatenergy_link('pid='.PAGE_HOME, t('LINK_CANCEL'));   
   $page .= '<input type="hidden" name="token" value="pid='.PAGE_SETTING_LOGIN.'&eid='.EVENT_LOGIN.'"/>';
   $page .= '<input type="submit" name="Submit" id="normal_link" value="'.t('LINK_LOGIN').'"/>';
   $page .= '</div>';
   
   /* Set focus on first input element */
	$page .= '<script type="text/javascript" language="JavaScript">';
	$page .= 'document.forms[\'plaatenergy\'].elements[\'password\'].focus();';
	$page .= '</script>';
      
   $page .= '</div>';
	
   return $page;
}

function plaatenergy_setting_edit_page() {

   // input
   global $id;
	global $cat;

	$sql  = 'select token, value, options from config where id='.$id;
	$result = plaatenergy_db_query($sql);
	$row = plaatenergy_db_fetch_object($result);

	$page  = ' <h1>'.t('SETTING_TITLE').' - '.t('CATEGORY'.$cat).'</h1>';

	$page .= '<br/>';
	$page .= '<label>'.t($row->token).'</label>';
	$page .= '<br/>';
	
	if (strlen($row->options)>0) {	   
		$options = explode(",", $row->options);		
		$page .= '<select name="value" >';		
		foreach ($options as $option) {
			if ($row->value==$option) {
				$page .= '<option selected="selected" value="'.$option.'">'.$option.'</option>';
 			} else {
				$page .= '<option value="'.$option.'">'.$option.'</option>';
			}
		}
	$page .= '</select>';

    } else {	   
	   $page .= '<input type="text" name="value" value="'.$row->value.'" size="40" />';
	}
	$page .= '<br/>';
 
	$page .= '<div class="nav">';
	$page .= plaatenergy_link('pid='.PAGE_SETTING_LIST.'&cat='.$cat, t('LINK_CANCEL'));
	$page .= plaatenergy_link('pid='.PAGE_SETTING_LIST.'&eid='.EVENT_SAVE.'&id='.$id.'&cat='.$cat, t('LINK_SAVE'));
	$page .= '</div>';
	
	return $page;
}

function plaatenergy_setting_list_page() {

   // input
	global $pid;
	global $cat;
	global $limit;
	global $step;
	global $max;

	$sql  = 'select id, token, value from config where readonly=0 and category='.$cat.' order by token limit '.($limit*$step).','.$step;
	$result = plaatenergy_db_query($sql);
	
	$page  = ' <h1>'.t('SETTING_TITLE').' - '.t('CATEGORY'.$cat).'</h1>';

	$page .= '<br/>';
	
	$page .= '<div class="setting">';
	$page .= '<table>';
	$page .= '<tr>';
	$page .= '<th width="200">'.t('LABEL_TOKEN').'</th>';
	$page .= '<th width="100">'.t('LABEL_VALUE').'</th>';
	$page .= '<th width="325">'.t('LABEL_DESCRIPTION').'</th>';
	$page .= '</tr>';
	
	while ($row = plaatenergy_db_fetch_object($result)) {
	
		$page .= '<tr>';
		$page .= '<td width="200">'.plaatenergy_link('pid='.PAGE_SETTING_EDIT.'&id='.$row->id.'&cat='.$cat, $row->token).'</td>';
		$page .= '<td width="100">'.$row->value.'</td>';
		$page .= '<td width="325">'.t($row->token).'</td>';
		$page .= '</tr>';
	}
	$page .= '</table>';
	$page .= '</div>';
	 
	$page .= '<div class="nav">';
	if ($max>0) {
		$page .= plaatenergy_link('pid='.$pid.'&eid='.EVENT_PREV.'&cat='.$cat.'&limit='.$limit, t('LINK_PREV'));
	}
	$page .= plaatenergy_link('pid='.PAGE_SETTING_CATEGORY, t('LINK_BACK'));
	if ($max>0) {
		$page .= plaatenergy_link('pid='.$pid.'&eid='.EVENT_NEXT.'&cat='.$cat.'&limit='.$limit, t('LINK_NEXT'));
	}
	$page .= '</div>';
	
	return $page;
}


function plaatenergy_setting_category_page() {

   // input
	global $pid;
	global $limit;
	global $step;

	$sql  = 'select category from config group by category order by category';
	$result = plaatenergy_db_query($sql);
	
	$page  = ' <h1>'.t('SETTING_TITLE').'</h1>';

	$page .= '<br/>';
	
	$page .= '<div class="setting">';
	$page .= '<table>';
	
	$count = 0;
	while ($row = plaatenergy_db_fetch_object($result)) {
	
		if (($count%3)==0) {
			$page .= '<tr>';
		}
		$page .= '<td width="200">'.plaatenergy_link('pid='.PAGE_SETTING_LIST.'&cat='.$row->category, i('cog').t('CATEGORY'.$row->category)).'</td>';
		if (($count%3)==3) {
			$page .= '</tr>';
		}
		
		$count++;
		
	}
	$page .= '</table>';
	$page .= '</div>';
	
	$page .= '<br/>';
	 
	$page .= '<div class="nav">';
	$page .= plaatenergy_link('pid='.$pid.'&eid='.EVENT_BACKUP, t('LINK_BACKUP'));
	$page .= plaatenergy_link('pid='.PAGE_HOME, t('LINK_HOME'));	
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
		  
		case EVENT_BACKUP:
			plaatenergy_setting_backup_event();
			break;
						
		case EVENT_LOGIN:
			plaatenergy_setting_login_event();
			break;		
   }

  /* Page handler */
  switch ($pid) {

		case PAGE_SETTING_LOGIN:
			return plaatenergy_setting_login_page();
			break;
			
		case PAGE_SETTING_CATEGORY:
			return plaatenergy_setting_category_page();
			break;
		
		case PAGE_SETTING_LIST:
			return plaatenergy_setting_list_page();
			break;
	
		case PAGE_SETTING_EDIT:
			return plaatenergy_setting_edit_page();
			break;
			
  }
}

/*
** ---------------------
** THE END
** ---------------------
*/

?>
