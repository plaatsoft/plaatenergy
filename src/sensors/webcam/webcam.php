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
 * @brief webcam script
 */
 
// instance=61 [Webcam 1]
// instance=62 [Webcam 2]
// instance=63 [Webcam 3]

include '/var/www/html/plaatenergy/config.inc';
include '/var/www/html/plaatenergy/database.inc';
include '/var/www/html/plaatenergy/general.inc';

define( 'LOCK_FILE', "/var/run/".basename( $argv[0], ".php" ).".lock" ); 
if( isLocked() ) die( "Already running.\n" ); 

@$index = $argv[1];
if (!isset($index)) {
	$index=1;
}

plaatenergy_db_connect($dbhost, $dbuser, $dbpass, $dbname);

$width=320;
$height=225;  /* remove last 15 pixel lines because of footer */
$segment=5;
$offset=$segment/2;
$detect_level=15;
$detect_areas=25;
$im2 = '';

function isLocked() { 
    if( file_exists( LOCK_FILE ) ) { 

        $lockingPID = trim( file_get_contents( LOCK_FILE ) ); 
        $pids = explode( "\n", trim( `ps -e | awk '{print $1}'` ) ); 
        if( in_array( $lockingPID, $pids ) )  return true; 
        unlink( LOCK_FILE ); 
    } 
    
    file_put_contents( LOCK_FILE, getmypid() . "\n" ); 
    return false; 
} 

function getColor($img, $x, $y) {
    $rgb = imagecolorat($img, $x, $y);
    $r = ($rgb >> 16) & 0xFF;
    $g = ($rgb >> 8) & 0xFF;
    $b = $rgb & 0xFF;
    return array ($x, $y, $r, $g, $b);
}

function plaatenergy_make_picture() {

	$path = BASE_DIR.'/webcam/picture/'.date('Y-m-d');		
	plaatenergy_create_path($path);
	
	$source = BASE_DIR.'/webcam/image1.jpg';

        $now = DateTime::createFromFormat('U.u', microtime(true));
        $now->setTimezone(new DateTimeZone('Europe/Amsterdam'));	
	$destination = $path.'/image1-'.$now->format("His.u").'.jpg';
	
	if (!copy($source, $destination)) {
		echo "failed to copy $file...\n";
	}
}

function plaatenergy_motion() {

   global $im2;
   global $index;
   global $width;
   global $height;
   global $segment;
   global $offset;
   global $detect_level;
   global $detect_areas;

   $input = BASE_DIR.'/webcam/image'.$index.'.jpg';
   $output = BASE_DIR.'/webcam/image'.($index+2).'.jpg';

   $im1 = imagecreatefromjpeg($input);
   if(!$im1) return;
	
   $color = imagecolorallocate($im1, 255, 0, 0);
   if(!$im2) {
     $im2 = $im1;
     return;
   }
	
   $detection=0;

   for ($x=0;$x<($width/$segment);$x++) {
		for ($y=0;$y<($height/$segment);$y++) {
			list($x1, $y1, $r1, $g1, $b1) = getColor($im1, ($x*$segment)+$offset, ($y*$segment)+$offset);
			list($x2, $y2, $r2, $g2, $b2) = getColor($im2, ($x*$segment)+$offset, ($y*$segment)+$offset);

			if ((abs($r1-$r2)>$detect_level) || (abs($g1-$g2)>$detect_level) || (abs($b1-$b2)>$detect_level)) { 

				// dirty hack to filter out threes part of my webcami view
				if ((($y*$segment)<90) && (($x*$segment)<160)) {
					// filter out tree section
				} else {
					$detection++;
				}
				imagerectangle( $im1, $x*$segment , $y*$segment , ($x+1)*$segment , ($y+1)*$segment , $color);
			}
		}
	}

	//echo $detection.' ';

        $im2=$im1;

	imagejpeg($im1, $output);	

	if ($detection>$detect_areas) {
		plaatenergy_make_picture();
	}
}

while (true) {

	$time_start = microtime(true);

	global $index;
	$instance = 1;
 
	switch ($index) {
		case 1: 	$instance=61;
					break;
		case 2: 	$instance=62;
					break;
		case 3: 	$instance=63;
					break;
	}
    
	$name = plaatenergy_db_get_config_item('webcam_name', $instance);
	$resolution = plaatenergy_db_get_config_item('webcam_resolution', $instance);
	$device = plaatenergy_db_get_config_item('webcam_device', $instance);
	 
	$command = 'fswebcam -q --device '.$device.' --timestamp "%Y-%m-%d %H:%M:%S" -r '.$resolution. ' --title '.$name.' -S 1 '.BASE_DIR.'/webcam/image'.$index.'.jpg';
	exec ($command);
	
	plaatenergy_motion();

	$time_end = microtime(true);
	$time = $time_end - $time_start;

	//echo 'Process time '.round($time,2)." seconds\n";
}

unlink( LOCK_FILE ); 
exit(0); 

?>
