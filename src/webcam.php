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
 * @brief contain webcam page
 */

/*
** ---------------------
** EVENT
** ---------------------
*/

function plaatenergy_action_picture() {
	
	$device1 = plaatenergy_db_get_config_item('webcam_present', WEBCAM_1);
	
	$path = 'webcam/picture/'.date('Y-m-d');		
	plaatenergy_create_path($path);
	
	if ($device1=="true" ) {
		$source = 'webcam/image1.jpg';
		$destination = $path.'/image1-'.date("His").'.jpg';
	
		if (!copy($source, $destination)) {
			echo "failed to copy $file...\n";
		}
	}
	
	$device2 = plaatenergy_db_get_config_item('webcam_present', WEBCAM_2);
	
	if ($device2=="true" ) {
		$source = 'webcam/image2.jpg';
		$destination = $path.'/image2-'.date("His").'.jpg';
	
		if (!copy($source, $destination)) {
			echo "failed to copy $file...\n";
		}
	}
}

function plaatenergy_picture_delete_event() {

	/* input */
	global $id;
	global $directory;
	
	$nr = 1;
		
	$files = scandir(BASE_DIR.'/webcam/picture/'.$directory);
	sort($files);

	foreach ($files as $file) {
			
		if (($file!='.') && ($file!='..')) {
	
			if ($id == $nr++) {
		
				unlink (BASE_DIR.'/webcam/picture/'.$directory.'/'.$file);			
				break;
			}
		}
	}	
}

/*
** ---------------------
** PAGE
** ---------------------
*/

function plaatenergy_image_viewer_page() {

	//input 
	global $pid;
	global $eid;
	global $directory;
	global $id;
		
	if ($id==0) {
		$id=1;
	}

	$page  = '<h1>'.t('TITLE_ARCHIVE').'</h1>';
	$page .= '<br/>';
	
	$nr = 1;
	$files = scandir(BASE_DIR.'/webcam/picture/'.$directory);
	sort($files);

	foreach ($files as $file) {
			
		if (($file!='.') && ($file!='..')) {
	
			if ($id == $nr++) {
		
				$page .= '<style>.image{-moz-animation: none; -o-animation: none; -webkit-animation: none; animation: none}></style>';
				$page .= '<img class="image" id="webcam" src="/webcam/picture/'.$directory.'/'.$file.'" alt="" width="480" height="360" >';			
				break;
			}
		}
	}	
	
	$page .= '<div class="nav">';
	$page .= plaatenergy_link('pid='.$pid.'&id=1&directory='.$directory.'&eid='.EVENT_BEGIN, t('LINK_BEGIN'), 'begin');
	$page .= plaatenergy_link('pid='.$pid.'&id='.$id.'&directory='.$directory.'&eid='.EVENT_PREV_FAST, t('LINK_PREV_FAST'),'fast back');
	$page .= plaatenergy_link('pid='.$pid.'&id='.$id.'&directory='.$directory.'&eid='.EVENT_PREV, t('LINK_PREV_STEP'), 'back');
	$page .= plaatenergy_link('pid='.$pid.'&id='.$id.'&directory='.$directory.'&eid='.EVENT_DELETE, t('LINK_REMOVE'), 'remove');
	$page .= plaatenergy_link('pid='.PAGE_ARCHIVE, t('LINK_HOME'), 'home');
	
	if ($eid==EVENT_PLAY) {
	   $page .= plaatenergy_link('pid='.$pid.'&id=\'+id+\'&directory='.$directory.'&eid='.EVENT_STOP, t('LINK_STOP') ,'stop');
	} else {
	   $page .= plaatenergy_link('pid='.$pid.'&id='.$id.'&directory='.$directory.'&eid='.EVENT_PLAY, t('LINK_PLAY'), 'play');
	} 
		
	$page .= plaatenergy_link('pid='.$pid.'&id='.$id.'&directory='.$directory.'&eid='.EVENT_NEXT, t('LINK_NEXT_STEP'), 'next');	
	$page .= plaatenergy_link('pid='.$pid.'&id='.$id.'&directory='.$directory.'&eid='.EVENT_NEXT_FAST, t('LINK_NEXT_FAST'),'fast next');
	$page .= plaatenergy_link('pid='.$pid.'&directory='.$directory.'&eid='.EVENT_END, t('LINK_END') ,'end');
	$page .=  '</div>';
	
	if ($eid==EVENT_PLAY) {	         
		$tmp = '';
		$files = scandir(BASE_DIR.'/webcam/picture/'.$directory);
		$max = 0;
		foreach ($files as $file) {
			if (($file!='.') && ($file!='..')) {
				if (strlen($tmp)>0) {	
					$max++;
					$tmp .= ',';
				}
				$tmp .= '"/webcam/picture/'.$directory.'/'.$file.'"';
			}
		};
				
		$page .= '<script>';		
		$page .= 'var files = ['.$tmp.'];';
		$page .= 'var id = '.$id.';';
		$page .= 'max = '.$max.';';
		$page .= 'window.setInterval(function() { if (id<max) id++; document.getElementById("webcam").src = files[id] }, 100);';
		$page .= '</script>';
	}
	return $page;
}

function plaatenergy_archive_page() {

	//input 
	global $pid;
	
	$page  = '<h1>'.t('TITLE_ARCHIVE').'</h1>';
	$page .= '<br/>';

	$tmp = '';
	$directories = scandir(BASE_DIR.'/webcam/picture');
	rsort($directories);
	
	$i=0;
	foreach ($directories as $directory) {
			
		if (($directory!='.') && ($directory!='..')) {		
			if (($i%5)==0) {
				$tmp .= '<tr>';
			}			
			$tmp .= '<td>'.plaatenergy_link('pid='.PAGE_IMAGE_VIEWER.'&directory='.$directory, i('folder-open') .$directory).'</td>';
			if (($i%5)==4) {
				$tmp .= '</tr>';
			}
			$i++;
		}
	}	
	
	if (strlen($tmp)>0) {
		
		$page .= '<table>';
		$page .= $tmp;
		$page .= '</table>';
		$page .= '<br/>';
	}
	
	$page .= '<div class="nav">';
	$page .= plaatenergy_link('pid='.PAGE_WEBCAM, t('LINK_BACK'));
	$page .=  '</div>';
	
	return $page;
}

/**
 * plaatenergy webcam page
 * @return HTML block which page contain.
 */
function plaatenergy_webcam_page() {

	// input
	global $pid;
	
	$device1 = plaatenergy_db_get_config_item('webcam_present', WEBCAM_1);
	$device2 = plaatenergy_db_get_config_item('webcam_present', WEBCAM_2);
		
	$page  = '<h1>'.t('TITLE_WEBCAM').'</h1>';
	$page .= '<br/>';
	$page .= '<style>.image{-moz-animation: none; -o-animation: none; -webkit-animation: none; animation: none}></style>';
  
	if ($device1=="true" ) {
		$page .= '<img class="image" src="webcam/image1.jpg" alt="" id="webcam1" width="480" height="360" >';
		$page .= '<script>window.setInterval(function() { document.getElementById("webcam1").src = "webcam/image3.jpg?random="+new Date().getTime(); }, 500);</script>';
	}
	
	if ($device2=="true" ) {
		$page .= '&nbsp;';
		$page .= '<img class="image" src="webcam/image2.jpg" alt="" id="webcam2" width="480" height="360" >';
		$page .= '<script>window.setInterval(function() { document.getElementById("webcam2").src = "webcam/image4.jpg?random="+new Date().getTime(); }, 500);</script>';
	}
	
	$page .= '<div class="nav">';
	//$page .= '<a href="webcam/picture">'.t('LINK_ARCHIVE').'</a>';
	$page .= plaatenergy_link('pid='.PAGE_ARCHIVE, t('LINK_ARCHIVE'));
	$page .= plaatenergy_link('pid='.PAGE_HOME, t('LINK_HOME'));
	$page .= plaatenergy_link('pid='.$pid.'&eid='.EVENT_PICTURE, t('LINK_PICTURE'));
	$page .=  '</div>';
	
	return $page;
}

/*
** ---------------------
** HANDLER
** ---------------------
*/

/**
 * plaatenergy about handler
 * @return HTML block which page contain.
 */
function plaatenergy_webcam() {

	/* input */
	global $pid;
	global $eid;
	global $id;
	global $directory;

	switch ($eid) {

		case EVENT_END:
	         $id=0;
	         $files = scandir(BASE_DIR.'/webcam/picture/'.$directory);
	         foreach ($files as $file) {
					if (($file!='.') && ($file!='..')) {
						$id++;
					}
	         };
				break;
	
		case EVENT_DELETE:
			plaatenergy_picture_delete_event();
			break;
			
		case EVENT_NEXT:
			$id++;
			break;

		case EVENT_NEXT_FAST:
			$id=$id+50;
			break;

		case EVENT_PREV_FAST:
			$id=$id-50;
			if ($id<1) {
			   $id=1;
			}
			break;
  
  		case EVENT_PREV:
			$id--;
			if ($id<1) {
			   $id=1;
			}
			break;
			
		case EVENT_PICTURE:
			plaatenergy_action_picture();
			break;

	}

	/* Page handler */
	switch ($pid) {

		case PAGE_WEBCAM:
			return plaatenergy_webcam_page();
			break;
			
		case PAGE_ARCHIVE:
			return plaatenergy_archive_page();
			break;
			
		case PAGE_IMAGE_VIEWER:
			return plaatenergy_image_viewer_page();
			break;
	}
}

/*
** ---------------------
** THE END
** ---------------------
*/

?>
