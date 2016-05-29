<?php

$width=320;
$height=240;
$segment=5;
$offset=$segment/2;
$detect_level=15;

$time_start = microtime(true);

function getColor($img, $x, $y) {
    $rgb = imagecolorat($img, $x, $y);
    $r = ($rgb >> 16) & 0xFF;
    $g = ($rgb >> 8) & 0xFF;
    $b = $rgb & 0xFF;
    return array ($x, $y, $r, $g, $b);
}

$im1 = imagecreatefromjpeg('image1.jpg');
if(!$im1) return;

$im2 = imagecreatefromjpeg('image2.jpg');

$color= imagecolorallocate($im1, 255, 0, 0);

$detection=0;
for ($x=0;$x<($width/$segment);$x++) {
   for ($y=0;$y<($height/$segment);$y++) {
       list($x1, $y1, $r1, $g1, $b1) = getColor($im1, ($x*$segment)+$offset, ($y*$segment)+$offset);
       list($x2, $y2, $r2, $g2, $b2) = getColor($im2, ($x*$segment)+$offset, ($y*$segment)+$offset);

       $motion=0;
       if (abs($r1-$r2)>$detect_level) {
	  $motion=1;
       } 
       if (abs($g1-$g2)>$detect_level) {
	  $motion=1;
       } 
       if (abs($b1-$b2)>$detect_level) {
	  $motion=1;
       } 

       if  ($motion==1) {
	  $detection++;
      	  imagerectangle ( $im1, $x*$segment , $y*$segment , ($x+1)*$segment , ($y+1)*$segment , $color);
       }
   }
}

#header('Content-Type: image/png');
#imagepng($im1);
imagedestroy($im1);
imagedestroy($im2);

$time_end = microtime(true);
$time = $time_end - $time_start;

copy('image1.jpg','image2.jpg');

echo 'detections='.$detection.' ['.round($time,2).' secs]';
?>
