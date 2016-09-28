<?php
// Set the content-type
header('Content-Type: image/png');

$width = 1920/2;
$height = 1080/2;

$offset = 20;

// Replace path by your own font path
$font = './fonts/arial.ttf';

$data = array(
    array("01-09", 0, 10, 10),
    array("02-09", 0, 12, 10),
	 array("03-09", 10.5, 0, 2),	
	 array("04-09", 12, 0, 8),	
	 array("05-09", 0, 3, 2),	
	 array("06-09", 0, 8, 3),	
	 array("07-09", 0, 7, 4),	
	 array("08-09", 0, 8, 5.6),	
	 array("09-09", 0, 7, 7),	
	 array("10-09", 12, 0, 4),	
	 array("11-09", 14, 0, 3),	
	 array("12-09", 0, 14, 2),	
	 array("13-09", 0, 13, 3),
	 array("14-09", 0, 12, 4),
	 array("15-09", 0, 14, 5),
	 array("16-09", 0, 11, 5),
    array("17-09", 13, 0, 6),
	 array("18-09", 12, 0, 7),	
	 array("20-09", 0, 14, 7),	
	 array("21-09", 0, 15, 8),	
	 array("22-09", 0, 16, 9),	
	 array("23-09", 0, 17, 4),	
	 array("24-09", 0, 8, 1),	
	 array("25-09", 13, 0, 2),	
	 array("26-09", 12, 0, 3),	
	 array("27-09", 0, 20, 4),	
	 array("28-09", 0, 14, 5),	
	 array("29-09", 0, 13, 6),
	 array("30-09", 0, 12, 7)
);

// -------------------------------------------------------

function getTotal($data) {

	$total=0;
	
	for ($row=0; $row<sizeof($data); $row++) {
		$total += $data[$row][1] + $data[$row][2] + $data[$row][3];
	}

	return number_format($total,2);
}

function getAverage($data) {

	$total=0;
	
	for ($row=0; $row<sizeof($data); $row++) {
		$total += $data[$row][1] + $data[$row][2] + $data[$row][3];
	}
	
	$average =0;
	if (sizeof($data)>0) {
		$average = $total / sizeof($data);
	}
	
	return number_format($average,2);
}

function getMax($data) {

	$max=0;
	
	for ($row=0; $row<sizeof($data); $row++) {
		$value = $data[$row][1] + $data[$row][2] + $data[$row][3];
		if ($value>$max) {
			$max = $value;
		}
	}
	return $max;
}
 
function drawLabel($im, $y, $text, $font_size=28, $color) {
	
	global $font;
	global $width;
	global $height;
	
	// Get Bounding Box Size
	$text_box = imagettfbbox($font_size, 0, $font, $text);

	// Get your Text Width and Height
	$text_width = $text_box[2]-$text_box[0];
	
	// Calculate coordinates of the text
	$x = ($width/2) - ($text_width/2);

	// Add some shadow to the text
	imagettftext($im, $font_size, 0, $x, $y, $color, $font, $text);
}

function drawLegend($im, $text1, $text2, $text3, $text4)  {

	global $width;
	global $height;
	global $font;
	
	global $black;
	global $blue1;
	global $blue2;
	global $blue3;
	global $red;
	
	$size = 10;
	$font_size = 13;
	
	imagefilledrectangle( $im, 200, $height-80 , 200+$size , $height-80+$size, $blue1 );
	imagettftext($im, $font_size, 0, 220, $height-70, $black, $font, $text1);
	
	imagefilledrectangle( $im, 330, $height-80 , 330+$size , $height-80+$size, $blue2 );
	imagettftext($im, $font_size, 0, 350, $height-70, $black, $font, $text2);
	
	imagefilledrectangle( $im, $width-480, $height-80 , $width-480+$size , $height-80+$size, $blue3 );
	imagettftext($im, $font_size, 0, $width-460, $height-70, $black, $font, $text3);
	
	imagefilledrectangle( $im, $width-350, $height-80 , $width-350+$size , $height-80+$size, $red );
	imagettftext($im, $font_size, 0, $width-330, $height-70, $black, $font, $text4);
}

function imagelinedashed($im, $offset, $y, $dist, $col) {
    $width = imagesx($im);
    $nextX = $dist * 2;

    for ($x = $offset; $x <= $width; $x += $nextX) {
        imageline($im, $x, $y, $x + $dist - 1, $y, $col);
    }
}

function drawAxes($im, $data, $color)  {
	
	global $width;
	global $height;
	global $offset;
	global $arial;
	global $font;
	
	global $gray;

	$lines = 5;
	$font_size = 10;
	
	$max = getMax($data);	
	$step = ceil($max / $lines);
	$pixel = ($height-180) / $lines;
	
	$starty = $height-120;
	
	for ($y=0; $y<=$lines; $y++) {
		imagelinedashed($im, $offset+10, $starty-($y*$pixel), 3, $color);
		imagettftext($im, $font_size, 0, 10, $starty-($y*$pixel)+$lines, $color, $font, $step*$y);
	}
}

function drawForcast($im, $data, $value, $color)  {
	
	global $width;
	global $height;
	global $offset;
	global $arial;
	global $font;
	
	global $gray;

	$lines = 5;
	$font_size = 10;
	$pixel = ($height-180) / $lines;
	
	$max = getMax($data);	
	$step = ceil($max / $lines);
	
	$y = $height-120 - ($value / $step) * $pixel;
		
	imagefilledrectangle( $im, $offset+10, $y , $width-5, $y+1, $color );
}

function drawBars($im, $data)  {

	global $width;
	global $height;
	global $offset;
	global $arial;
	global $font;
	
	global $gray;
	global $white;
	global $blue1;
	global $blue2;
	global $blue3;
	
	$lines = 5;
	$font_size = 9;
	
	$max = getMax($data);
	$step = ceil($max / $lines);
	$pixel = ($height-180) / $lines;
	
	$amount = sizeof($data);
	$bar_width = ($width-(2*($offset+90))) / sizeof($data)+2;
	
	$starty = $height - 120;	
	$startx = $offset + 18;
		
	$count=0;
	
	for ($row=0; $row<sizeof($data); $row++) {
			
		$bar_height1 = ($data[$row][1] / $step) * $pixel;		
		$bar_start1 = $starty;
		$bar_end1 = $bar_start1 - $bar_height1;
		if ($data[$row][1]>0) {
			imagefilledrectangle( $im, $startx, $bar_start1 , ($startx+$bar_width) , $bar_end1, $blue1 );		
			if ($data[$row][1]>1) {		
				imagettftext($im, $font_size-2, 0, $startx+5, $bar_end1+16, $white, $font, number_format($data[$row][1],1) );
			}
		}
				
		$bar_height2 = ($data[$row][2] / $step) * $pixel;
		$bar_start2 = $bar_end1;
		$bar_end2 = $bar_start2 - $bar_height2;
			
		if ($data[$row][2]>0) {
			imagefilledrectangle( $im, $startx, $bar_start2 , ($startx+$bar_width) , $bar_end2, $blue2 );
			if ($data[$row][2]>1) {	
				imagettftext($im, $font_size-2, 0, $startx+5, $bar_end2+16, $white, $font, number_format($data[$row][2],1) );
			}
		}
		
		$bar_height3 = ($data[$row][3] / $step) * $pixel;
		$bar_start3 = $bar_end2;
		$bar_end3 = $bar_start3 - $bar_height3;
			
		if ($data[$row][3]>0) {			
			imagefilledrectangle( $im, $startx, $bar_start3 , ($startx+$bar_width) , $bar_end3, $blue3 );
			if ($data[$row][3]>1) {	
				imagettftext($im, $font_size-2, 0, $startx+5, $bar_end3+16, $white, $font, number_format($data[$row][3],1) );
			}
		}
		
		if ($count%2==0) {
			imagettftext($im, $font_size, 0, $startx-5, $starty+20, $gray, $font, $data[$row][0] );
		}

		$startx += $bar_width+4;		
		$count++;	
	}
}

function drawLogo($im) {

	global $width;

	$src= imagecreatefrompng('images/logo.png');

	// Copy and merge
	imagecopymerge($im, $src, 150, 12, 0, 0, 32, 32, 100);
	imagecopymerge($im, $src, $width-180, 12, 0, 0, 32, 32, 100);
}

// -------------------------------------------------------

// Create the image
$im = imagecreatetruecolor($width, $height);

$white = imagecolorallocate($im, 0xff, 0xff, 0xff);
$blue1 = imagecolorallocate($im, 0x00, 0x66, 0xcc);
$blue2 = imagecolorallocate($im, 0x48, 0x82, 0xbc);
$blue3 = imagecolorallocate($im, 0xaa, 0xcc, 0xee);
$black = imagecolorallocate($im, 0x00, 0x00, 0x00);
$gray = imagecolorallocate($im, 0x85, 0x85, 0x85);
$red = imagecolorallocate($im, 0xff, 0x00, 0x00);

imagefilledrectangle($im, 0, 0, $width, $height, $white);

drawLabel($im, 38, 'Geleverde Electriciteit September 2016', 24, $black);
drawLogo($im);
drawLegend($im, "Laag (kWh)", "Normaal (kWh)", "Lokaal (kWh)", 'Voorspelling (kWh)');
drawAxes($im, $data, $gray);
drawForcast($im, $data, 12.55, $red);
drawBars($im, $data);
drawLabel($im, $height-38, 'Gemiddeld per dag '.getAverage($data).' kWh [Totaal = '.getTotal($data).' kWh]', 18, $black);
drawLabel($im, $height-10, 'PlaatSoft 2008-2016 - All Copyright Reserved - PlaatEnergy', 12, $gray);

imagepng($im);
imagedestroy($im);

?>