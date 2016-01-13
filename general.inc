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

// ----------------------------
// SETTINGS
// ----------------------------

$version = "0.5";

// Graphical screen resolution
$graph_width = "950px";
$graph_height = "280px";

// Energy use forecast (per month)
$in_forecast = array(0,100/1040,100/1040,90/1040,90/1040,80/1040,70/1040,70/1040,70/1040,80/1040,90/1040,100/1040,100/1040);

// Energy delivery forecast (per month)
$out_forecast = array(0,50/2550,100/2550,210/2550,310/2550,360/2550,360/2550,330/2550,290/2550,240/2550,160/2550,90/2550,50/2550);

// Gas use forecast (per month)
$gas_forecast = array(0,250/1500,220/1500,180/1500,110/1500,60/1500,40/1500,30/1500,30/1500,50/1500,110/1500,190/1500,230/1500);

$prev_date=0;
$current_date=0;
$next_date=0;

$day=0;
$month=0;
$year=0;   
$type=0;   
$zoom=1;

$next_day=0;
$next_month=0;
$next_year=0;   

$prev_day=0;   
$prev_month=0;
$prev_year=0;

$day=0;   
$month=0;
$year=0;
$page=1;

/**
 *  COOKIES VERY IMPORTANT
 */
function set_cookie_and_refresh ($name, $value) {
	setcookie($name, $value, time() + (86400 * 30), "/");
	header("Location: " . $_SERVER['PHP_SELF']);
}

if (!isset($_COOKIE["theme"])) {
	$_COOKIE["theme"] = "light";
}

if (!isset($_COOKIE["lang"])) {
	if (substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) == "nl") {
		$_COOKIE["lang"] = "nl";
	} else {
		$_COOKIE["lang"] = "en";
	}
}

if (!isset($_COOKIE["allow_cookies"])) {
	$_COOKIE["allow_cookies"] = "no";
}

if (isset($_GET["theme"])) {
	if ($_GET["theme"] == "light") {
		set_cookie_and_refresh("theme", "light");
	} elseif ($_GET["theme"] == "dark") {
		set_cookie_and_refresh("theme", "dark");
	}
}

if (isset($_GET["lang"])) {
	if ($_GET["lang"] == "nl") {
		set_cookie_and_refresh("lang", "nl");
	} elseif ($_GET["lang"] == "en") {
		set_cookie_and_refresh("lang", "en");
	}
}

if (isset($_GET["allow_cookies"])) {
	if ($_GET["allow_cookies"] == "yes") {
		set_cookie_and_refresh("allow_cookies", "yes");
	}
}

function i ($name) { 
	$icon = '<i class="fa fa-' . $name;
	if ($name == 'chevron-right') {
		$icon .= ' right';
	}
	$icon .= ' fa-fw"></i>';
	return $icon;
}

// Load language resource based on browser language setting.
switch ($_COOKIE["lang"]) {
    case "nl":
			include("dutch.inc");
         break;        
		
    default:
         include("english.inc");
         break;
}

// ----------------------------
// PAGE LAYOUT
// ----------------------------

/**
 * Language function 
 */
function t() {

	global $lang;
	
   $numArgs = func_num_args();

   $temp = $lang[func_get_arg(0)];

   $pos = 0;
   $i = 1;

   while (($pos = strpos($temp, "%s", $pos)) !== false) {
      if ($i >= $numArgs) {
         throw new InvalidArgumentException("Not enough arguments passed.");
		}

      $temp = substr($temp, 0, $pos) . func_get_arg($i) . substr($temp, $pos + 2);
      $pos += strlen(func_get_arg($i));
      $i++;
   }      
	
	$temp = mb_convert_encoding($temp, "UTF-8", "HTML-ENTITIES" ); 
   return $temp; 
}

/**
 * Add title icon 
 */
function add_icons ($path = './ui/') {
    $path = $path . 'images/icons/';

   $page = '<meta charset="UTF-8">';

   $page .= '<link rel="shortcut icon" type="image/png" sizes="16x16" href="' . $path . '16.png">';
   $page .= '<link rel="shortcut icon" type="image/png" sizes="24x24" href="' . $path . '24.png">';
   $page .= '<link rel="shortcut icon" type="image/png" sizes="32x32" href="' . $path . '32.png">';
   $page .= '<link rel="shortcut icon" type="image/png" sizes="48x48" href="' . $path . '48.png">';
   $page .= '<link rel="shortcut icon" type="image/png" sizes="64x64" href="' . $path . '64.png">';
   $page .= '<link rel="shortcut icon" type="image/png" sizes="128x128" href="' . $path . '128.png">';
   $page .= '<link rel="shortcut icon" type="image/png" sizes="256x256" href="' . $path . '256.png">';
   $page .= '<link rel="shortcut icon" type="image/png" sizes="512x512" href="' . $path . '512.png">';

   $page .= '<link rel="apple-touch-icon" type="image/png" href="' . $path . 'apple-60.png">';
   $page .= '<link rel="apple-touch-icon" type="image/png" sizes="76x76" href="' . $path . 'apple-76.png">';
   $page .= '<link rel="apple-touch-icon" type="image/png" sizes="120x120" href="' . $path . 'apple-120.png">';
   $page .= '<link rel="apple-touch-icon" type="image/png" sizes="152x152" href="' . $path . 'apple-152.png">';

   $page .= '<title>'.t('TITLE').'</title>';
	return $page;
}

/**
 * Text Banner
 */
function text_banner($text) { 
  echo '<div class="remark">' . $text . '</div>';
}


/**
 * General header
 */
function general_header() {

  global $time_start;

  $time_start = microtime(true);

  echo '<!DOCTYPE html>';
  echo '<html>';
  echo '<head>'; 

  echo add_icons();

  echo '<script language="JavaScript" src="js/link.js" type="text/javascript"></script>';
  echo '<link href="css/general.css" rel="stylesheet" type="text/css" />';
  
  // Load the dark theme css file only if COOKIE theme = dark
  if ($_COOKIE["theme"] == "dark") {
    echo '<link rel="stylesheet" type="text/css" href="./css/theme-dark.css">';
  }
  
  // Load the icons from Font Awesome
  echo '<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">';
 
  echo '</head>';
  
  echo '<body>';
  echo '<form id="plaatenergy" method="POST">';  
  
  // Language toggler
  if ($_COOKIE["lang"] == "en") {
	echo '<a class="language normal_link" href="?lang=nl">'.t('DUTCH').'</a>';
  } elseif ($_COOKIE["lang"] == "nl") {
	echo '<a class="language normal_link" href="?lang=en">'.t('ENGLISH').'</a>';
  }
  
  // Theme toggler
  if ($_COOKIE["theme"] == "light") {
	echo '<a class="theme normal_link" href="?theme=dark">' . t('THEME_TO_DARK') . '</a>';
  } elseif ($_COOKIE["theme"] == "dark") {
	echo '<a class="theme normal_link" href="?theme=light">' . t('THEME_TO_LIGHT') . '</a>';
  }
  
  // Cookie Bar Message (show only if COOKIE allow_cookies = NULL)
  if ($_COOKIE["allow_cookies"] == "no") {
	echo '<div class="cookie_bar">' . t("COOKIE_BAR") . ' <a class="fa normal_link fa-times" href="?allow_cookies=yes"></a></div>';
  }
}

/**
 * General footer
 */
function general_footer() {

  global $time_start;

  // Increase request counter with one!
  $counter = plaatenergy_db_get_config_item('request_counter');  
  plaatenergy_db_set_config_item('request_counter', ++$counter);  

  $time_end = microtime(true);
  $time = $time_end - $time_start;

  echo '<div class="copyright">' . t('LINK_COPYRIGHT') . '<br/>';
  //echo '['.round($time*1000).' ms - '.plaatenergy_db_count().' queries - '.$counter.' requests]';
  echo '['.round($time*1000).' ms - '.plaatenergy_db_count().' queries]';

  echo '</div>';
  echo '</form>';
  echo '</body>';
  echo '</html>';
}

// ----------------------------
// NAVIGATION
// ----------------------------

/**
 * Process day parameters 
 */
function day_parameters() {

  global $prev_date;
  global $current_date;
  global $next_date;

  global $day;
  global $month;
  global $year;   
  global $type;   
  global $edit;   

  global $next_day;
  global $next_month;
  global $next_year;   

  global $prev_day;   
  global $prev_month;
  global $prev_year;

  if (isset($_GET["type"])) {
     $type = $_GET["type"];
  } else {
     $type = 1;
  }

  if (isset($_GET["edit"])) {
     $edit = $_GET["edit"];
  } else {
     $edit = 0;
  }

  if (isset($_GET["day"])) {
     $day = $_GET["day"];
  } else {
     $day = date('d');
  }

  if (isset($_GET["month"])) {
     $month = $_GET["month"];
  }   else {
     $month = date('m');
  }

  if (isset($_GET["year"])) {
     $year = $_GET["year"];
  }   else {
     $year = date('Y');
  }

  $prev_day=$day-1;
  $prev_month=$month;
  $prev_year=$year;   

  if ($prev_day<=0) {
     $prev_month=$month-1;
     $prev_year=$year;
     $prev_day=date("t", strtotime($prev_year.'-'.$prev_month.'-1'));
  }

  if ($prev_month<=0) {
     $prev_month=12;
     $prev_year=$year-1;
     $prev_day=date("t", strtotime($prev_year.'-'.$prev_month.'-1'));
  }

  $next_day=$day+1;   
  $next_month=$month;
  $next_year=$year;   
  
  if ($next_day>date("t", strtotime($next_year.'-'.$next_month.'-1'))) {
     $next_day=1;
     $next_month=$next_month+1;
     $next_year=$year;
  }
  
  if ($next_month>12) {
     $next_day=1;
     $next_month=1;
     $next_year=$year+1;
  }

  $prev_date=mktime(0, 0, 0, $prev_month, $prev_day, $prev_year);
  $current_date=mktime(0, 0, 0, $month, $day, $year);
  $next_date=mktime(0, 0, 0, $next_month, $next_day, $next_year);
}

/**
 * Process month parameters 
 */
function month_parameters() { 
  
  global $month;
  global $year;   

  global $next_month;
  global $next_year;   

  global $prev_month;
  global $prev_year;   
  global $type;

  if (isset($_GET["type"])) {
     $type = $_GET["type"];
  } else {
     $type = 1;
  }

  if (isset($_GET["month"])) {
   $month = $_GET["month"];
  } else {
     $month = date('m');
  }

  if (isset($_GET["year"])) {
     $year = $_GET["year"];
  } else {
     $year = date('Y');
  }

  $prev_year=$year;   
  $prev_month=$month-1;
  if ($prev_month<=0) {
     $prev_month=12;
     $prev_year=$year-1;
  }
  
  $next_year=$year;   
  $next_month=$month+1;
  if ($next_month>12) {
     $next_month=1;
     $next_year=$year+1;
  }
}

/**
 * Process year parameters 
 */
function year_parameters() { 

  global $year;   
  global $next_year;   
  global $prev_year;   
  global $type;   
  global $zoom;

  if (isset($_GET["type"])) {
     $type = $_GET["type"];
  } else {
     $type = 1;
  }

  if (isset($_GET["year"])) {
     $year = $_GET["year"];
  } else {
     $year = date('Y');
  }

  if (isset($_GET["zoom"])) {
     $zoom = $_GET["zoom"];
  } else {
     $zoom = 1;
  }

  $prev_year=$year-1;   
  $next_year=$year+1;   
}

/**
 * Process day navigation
 */
function day_navigation($edit=0) { 

  global $type;   
  global $year;   
  global $month;   
  global $day;   

  global $next_day;
  global $next_month;
  global $next_year;   

  global $prev_day;   
  global $prev_month;
  global $prev_year;   

  echo '<div class="nav">';

  echo '<a href="?day='.$prev_day.'&month='.$prev_month.'&year='.$prev_year.'&type='.$type.'">'.t('LINK_PREV_DAY').'</a>';
  echo plaatenergy_link('pid='.PAGE_HOME, t('LINK_HOME'));
  echo '<a href="?day='.$next_day.'&month='.$next_month.'&year='.$next_year.'&type='.$type.'">'.t('LINK_NEXT_DAY').'</a>';

  switch ($type) {

    case 1: echo '<a href="?day='.$day.'&month='.$month.'&year='.$year.'&type=2">'.t('LINK_WATT').'</a>';
            break;

    case 2: echo '<a href="?day='.$day.'&month='.$month.'&year='.$year.'&type=1">'.t('LINK_KWH').'</a>';
            break;
  }

  switch ($edit) {

    case 1: echo plaatenergy_link('pid='.PAGE_IN_KWH_EDIT, t('LINK_EDIT'));
            break;

    case 2: echo '<a href="day_out_kwh_edit.php?day='.$day.'&month='.$month.'&year='.$year.'&edit=1">'.t('LINK_EDIT').'</a>';
            break;

    case 3: echo '<a href="day_in_gas_edit.php?day='.$day.'&month='.$month.'&year='.$year.'&edit=1">'.t('LINK_EDIT').'</a>';
            break;
  }
    
  echo "</div>";
}

/**
 * Process month navigation
 */
function month_navigation($default) { 

  global $type;   
  global $year;   
  global $month;   

  global $next_month;
  global $next_year;   

  global $prev_month;
  global $prev_year;   

  echo '<div class="nav">';

  echo '<a href="?month='.$prev_month.'&year='.$prev_year.'&type='.$type.'">'.t('LINK_PREV_MONTH').'</a>';
  echo plaatenergy_link('pid='.PAGE_HOME, t('LINK_HOME'));
  echo '<a href="?month='.$next_month.'&year='.$next_year.'&type='.$type.'">'.t('LINK_NEXT_MONTH').'</a>';

  switch ($type) {

    case 1: echo '<a href="?month='.$month.'&year='.$year.'&type=2">'.t('LINK_EURO').'</a>';
            break;
    
    case 2: echo '<a href="?month='.$month.'&year='.$year.'&type=1">'.$default.'</a>';
            break;
  }
  echo "</div>";
}

/**
 * Process year navigation
 */
function year_navigation($default) { 

  global $type;   
  global $zoom;   
  global $prev_year;   
  global $year;   
  global $next_year;   

  echo '<div class="nav">';

  echo '<a href="?year='.$prev_year.'&type='.$type.'">'.t('LINK_PREV_YEAR').'</a>';
  echo plaatenergy_link('pid='.PAGE_HOME, t('LINK_HOME'));
  echo '<a href="?year='.$next_year.'&type='.$type.'">'.t('LINK_NEXT_YEAR').'</a>';
 
  switch ($type) {

    case 1: echo '<a href="?year='.$year.'&type=2">'.t('LINK_EURO').'</a>';
            break;
    
    case 2: echo '<a href="?year='.$year.'&type=1">'.$default.'</a>';
            break;
  }

  echo "</div>";
}

function plaatenergy_post($label, $default) {
	
	$value = $default;
	
	if (isset($_POST[$label])) {
		$value = $_POST[$label];
		$value = stripslashes($value);
		$value = htmlspecialchars($value);
	}
	
	return $value;
}

/** 
 * Zip and uuencode token.
 */
function plaatenergy_token($token) {
   
	/* Encode token  */
	$token = base64_encode(gzdeflate($token));
	
	return $token;
}

/**
 * Create link 
 */
function plaatenergy_link($parameters, $label, $id="", $title="") {
   
	global $link_counter;
	
	$link_counter++;
	
	
	$link  = '<a href="javascript:link(\''.plaatenergy_token($parameters).'\');" class="link" ';			
	if (strlen($id)!=0) {
		$link .= ' id="'.strtolower($id).'"';
	}
	if (strlen($title)!=0) {
		$link .= ' title="'.strtolower($title).'"';
	}
	$link .= '>'.$label.'</a>';	
	return $link;
}

// ----------------------------
// THE END
// ----------------------------


?>
