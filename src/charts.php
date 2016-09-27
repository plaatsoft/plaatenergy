<?php
// Set the content-type
header('Content-Type: image/png');

$width = 640;
$height = 480;

// Replace path by your own font path
$font = './fonts/arial.ttf';

$data = array(
    "01-09" => "1.5",
    "02-09" => "2",
	 "03-09" => "3.5",	
	 "04-09" => "4",	
	 "05-09" => "5",	
	 "06-09" => "5.1",	
	 "07-09" => "7",	
	 "08-09" => "3",	
	 "09-09" => "4.5",	
	 "10-09" => "3",	
	 "11-09" => "6",	
	 "12-09" => "3.4",	
	 "13-09" => "8",	
	 "14-09" => "10"
);

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

function drawChart($im, $data)  {


  
}

// Create the image
$im = imagecreatetruecolor($width, $height);

// Create some colors
$white = imagecolorallocate($im, 0xd3, 0xd3, 0xd3);
imagefilledrectangle($im, 0, 0, $width, $height, $white);

drawLabel($im, 30, 'Used Electricity 9-2016', 16);
drawLegend($im, "Low (Kwh)", "Normal (kWh)", "Local (Kwh)");

drawChart($im, $data);

drawLabel($im, $height-40, 'Average per day 5.76 kWh [Total = 155.48 kWh]', 14);
drawLabel($im, $height-20, 'PlaatSoft 2008-2016 - All Copyright Reserved - PlaatEnergy', 8);

// Using imagepng() results in clearer text compared with imagejpeg()
imagepng($im);
imagedestroy($im);
?>