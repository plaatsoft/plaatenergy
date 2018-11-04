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
**  All copyrights reserved (c) 2008-2018 PlaatSoft
*/

/**
 * @file
 * @brief contain database backup script
 */
 
/*
** ---------------------
** BACKUP
** ---------------------
*/

$time_start = microtime(true);

include "config.php";
include "general.php";
include "database.php";

function plaatenergy_cleanup_old_data() {

	// cleanup solar1 table
	$count=1;
	for ($y=1; $y<=12; $y++) {		
		for ($i=1; $i<5; $i++) {
			$query= 'delete from solar1 where MINUTE(timestamp)='.$count.' and timestamp<DATE_SUB(NOW(),INTERVAL 1 YEAR)';
			plaatenergy_db_query($query);			
			$count++;
		}	
		$count++;
	}
	
	$query= 'OPTIMIZE TABLE solar1';
	plaatenergy_db_query($query);	
	
	// cleanup solar2 table
	$count=1;
	for ($y=1; $y<=12; $y++) {		
		for ($i=1; $i<5; $i++) {
			$query= 'delete from solar2 where MINUTE(timestamp)='.$count.' and timestamp<DATE_SUB(NOW(),INTERVAL 1 YEAR)';
			plaatenergy_db_query($query);			
			$count++;
		}	
		$count++;
	}
	
	$query= 'OPTIMIZE TABLE solar2';
	plaatenergy_db_query($query);	
	
	// cleanup solar3 table
	$count=1;
	for ($y=1; $y<=12; $y++) {		
		for ($i=1; $i<5; $i++) {
			$query= 'delete from solar3 where MINUTE(timestamp)='.$count.' and timestamp<DATE_SUB(NOW(),INTERVAL 1 YEAR)';
			plaatenergy_db_query($query);			
			$count++;
		}	
		$count++;
	}
	
	$query= 'OPTIMIZE TABLE solar3';
	plaatenergy_db_query($query);	
		
	// cleanup energy1 table
	$count=1;
	for ($y=1; $y<=12; $y++) {		
		for ($i=1; $i<5; $i++) {
			$query= 'delete from energy1 where MINUTE(timestamp)='.$count.' and timestamp<DATE_SUB(NOW(),INTERVAL 1 YEAR)';
			plaatenergy_db_query($query);			
			$count++;
		}	
		$count++;
	}
	
	$query= 'OPTIMIZE TABLE energy1';
	plaatenergy_db_query($query);	
}

function plaatenergy_cleanup_old_backup_files() {

	$directory = BASE_DIR.'/backup';
	$older = 30;

	if (file_exists($directory)) {
		foreach (new DirectoryIterator($directory) as $fileInfo) {
			if ($fileInfo->isDot()) {
				continue;
			}
			if ($fileInfo->isFile() && time() - $fileInfo->getCTime() >= $older*24*60*60) {
				unlink($fileInfo->getRealPath());
			}
		}
	}
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
	$filename = BASE_DIR.'/backup/'.$filename.'-'.uniqid().'.sql';

    /* Remove old file if it exists */
    @unlink($filename.'.gz');

        /* Make mysql backup */	
	$command = 'mysqldump --user='.$dbuser.' --password='.$dbpass.' --host='.$dbhost.' '.$dbname.' > '.$filename;
	system($command);
	
    /* Zip database dump file */	
	$command = 'gzip '.$filename;
	system($command);
}

plaatenergy_db_connect($dbhost, $dbuser, $dbpass, $dbname);

plaatenergy_cleanup_old_data();

plaatenergy_cleanup_old_backup_files();

plaatenergy_backup_event();

plaatenergy_db_close();

// Calculate to page render time
$time_end = microtime(true);
$time = $time_end - $time_start;

if (DEBUG==1) {
	echo "backup took ".round($time,2)." secs";
}
