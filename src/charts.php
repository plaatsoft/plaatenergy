<?php
// Set the content-type
header('Content-Type: image/png');

$width = 1920/2;
$height = 1080/2;
$offset = 20;

// Replace path by your own font path
$font = './fonts/arial.ttf';

$data = array(
    "01-09" => 1,
    "02-09" => 2,
	 "03-09" => 3,	
	 "04-09" => 4,	
	 "05-09" => 5,	
	 "06-09" => 6,	
	 "07-09" => 7,	
	 "08-09" => 8,	
	 "09-09" => 7,	
	 "10-09" => 6,	
	 "11-09" => 5,	
	 "12-09" => 4,	
	 "13-09" => 3,
	 "14-09" => 2,
	 "15-09" => 11,
	 "16-09" => 1,
    "17-09" => 2,
	 "18-09" => 3,	
	 "20-09" => 4,	
	 "21-09" => 5,	
	 "22-09" => 6,	
	 "23-09" => 7,	
	 "24-09" => 8,	
	 "25-09" => 7,	
	 "26-09" => 6,	
	 "27-09" => 5,	
	 "28-09" => 4,	
	 "29-09" => 3,
	 "30-09" => 2,
	 "31-09" => 1
);

function getMax($data) {

	$tmp=0;
	foreach ($data as $value) {
		if ($value>$tmp) {
			$tmp = $value;
		}
	}
	return $tmp;
}
 
function drawLabel($im, $y, $text, $font_size=28, $angle = 0) {
	
	global $font;
	global $width;
	global $height;
	
	$black = imagecolorallocate($im, 0x00, 0x00, 0x00);
	
	// Get Bounding Box Size
	$text_box = imagettfbbox($font_size, $angle, $font, $text);

	// Get your Text Width and Height
	$text_width = $text_box[2]-$text_box[0];
	
	// Calculate coordinates of the text
	$x = ($width/2) - ($text_width/2);

	// Add some shadow to the text
	imagettftext($im, $font_size, 0, $x, $y, $black, $font, $text);
}

function drawLegend($im, $text1, $text2, $text3)  {

	global $width;
	global $height;
	global $font;
	
	$size = 10;
	$font_size = 10;

	$blue1 = imagecolorallocate($im, 0x55, 0x99, 0xdd);
	$blue2 = imagecolorallocate($im, 0x48, 0x82, 0xbc);
	$blue3 = imagecolorallocate($im, 0xaa, 0xcc, 0xee);
	$black = imagecolorallocate($im, 0x00, 0x00, 0x00);

	imagefilledrectangle( $im, 120, $height-80 , 120+$size , $height-80+$size, $blue1 );
	imagettftext($im, $font_size, 0, 140, $height-70, $black, $font, $text1);
	
	imagefilledrectangle( $im, $width-($width/2)-30, $height-80 , $width-($width/2)+$size-30 , $height-80+$size, $blue2 );
	imagettftext($im, $font_size, 0, $width-($width/2)-10, $height-70, $black, $font, $text2);
	
	imagefilledrectangle( $im, $width-170, $height-80 , $width-170+$size , $height-80+$size, $blue3 );
	imagettftext($im, $font_size, 0, $width-150, $height-70, $black, $font, $text3);
}

function drawAxes($im, $data)  {
	
	global $width;
	global $height;
	global $offset;
	global $arial;
	global $font;

	$font_size = 10;
	$max = getMax($data);
	
	$black = imagecolorallocate($im, 0x00, 0x00, 0x00);
	
	$step = round ($max / 5);
	
	$starty = $height-120;
	
	for ($y=0; $y<=5; $y++) {
		imageline( $im , $offset+20, $starty-($y*70) , $width-$offset , $starty-($y*70), $black );
		imagettftext($im, $font_size, 0, 10, $starty-($y*70), $black, $font, $step*$y);
	}
}

function drawBars($im, $data)  {

	global $width;
	global $height;
	global $offset;
	global $arial;
	global $font;
	
	$font_size = 7;
	
	$blue1 = imagecolorallocate($im, 0x55, 0x99, 0xdd);
	$blue2 = imagecolorallocate($im, 0x48, 0x82, 0xbc);
	$blue3 = imagecolorallocate($im, 0xaa, 0xcc, 0xee);
	$black = imagecolorallocate($im, 0x00, 0x00, 0x00);
	
	$max = getMax($data);
	
	$amount = sizeof($data);
	$bar_width = round(($width-(2*($offset+90))) / sizeof($data));
		
	//echo $bar_width.'<br/>';
		
	$starty = $height-120;	
	$startx = $offset + 30;
	
	$count=0;
	foreach ($data as $key => $value) {
	
		$bar_height = round($value * (70 * 5) / $max);
		imagefilledrectangle( $im, $startx, $starty-1 , ($startx+$bar_width) , ($starty-$bar_height-1), $blue1 );
		
		if ($count%2==0) {
			imagettftext($im, $font_size, 0, $startx-5, $starty+20, $black, $font, $key);
		}

		$startx += $bar_width+5;		
		$count++;	
	}
}

// Create the image
$im = imagecreatetruecolor($width, $height);

// Create some colors
$white = imagecolorallocate($im, 0xd3, 0xd3, 0xd3);
imagefilledrectangle($im, 0, 0, $width, $height, $white);

drawLabel($im, 30, 'Used Electricity 9-2016', 16);
drawLegend($im, "Low (Kwh)", "Normal (kWh)", "Local (Kwh)");

drawAxes($im, $data);
drawBars($im, $data);

drawLabel($im, $height-40, 'Average per day 5.76 kWh [Total = 155.48 kWh]', 14);
drawLabel($im, $height-20, 'PlaatSoft 2008-2016 - All Copyright Reserved - PlaatEnergy', 8);

// Using imagepng() results in clearer text compared with imagejpeg()
imagepng($im);
imagedestroy($im);
?>