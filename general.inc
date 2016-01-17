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
** -----------
** CONSTANTS
** -----------
*/

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


/*
** -----------
** PAGES
** -----------
*/

define('PAGE_HOME',                 100);
define('PAGE_ABOUT',                101);
define('PAGE_DONATE',               102);
define('PAGE_RELEASE_NOTES',        103);
define('PAGE_REPORT',               104);

define('PAGE_YEARS_IN_ENERGY',      110);
define('PAGE_YEARS_OUT_ENERGY',     111);
define('PAGE_YEARS_IN_GAS',         112);

define('PAGE_YEAR_IN_ENERGY',       120);
define('PAGE_YEAR_OUT_ENERGY',      121);
define('PAGE_YEAR_IN_GAS',          122);

define('PAGE_MONTH_IN_ENERGY',      130);
define('PAGE_MONTH_OUT_ENERGY',     131);
define('PAGE_MONTH_OUT_ENERGY_MAX', 132);
define('PAGE_MONTH_IN_GAS',         133);

define('PAGE_DAY_IN_ENERGY',        140);
define('PAGE_DAY_OUT_ENERGY',       141);
define('PAGE_DAY_IN_GAS',           142);
define('PAGE_DAY_IN_KWH_EDIT',      143);
define('PAGE_DAY_OUT_KWH_EDIT',     144);
define('PAGE_DAY_IN_GAS_EDIT',      145);

define('PAGE_DAY_PRESSURE',         150);
define('PAGE_DAY_TEMPERATURE',      151);
define('PAGE_DAY_HUMIDITY',         152);

/*
** -----------
** EVENTS
** -----------
*/

define('EVENT_PROCESS_CURRENT_DAY',   1);
define('EVENT_PROCESS_ALL_DAYS',      2);

define('EVENT_NONE',                200);
define('EVENT_PREV',                201);
define('EVENT_NEXT',                202);
define('EVENT_EXECUTE',             203);
define('EVENT_SAVE',                204);
define('EVENT_EURO',                205);
define('EVENT_KWH',                 206);
define('EVENT_M3',                  207);
define('EVENT_WATT',                208);

// ----------------------------
// SETTINGS
// ----------------------------

function set_cookie_and_refresh ($name, $value) {
	setcookie($name, $value, time() + (86400 * 30), "/");
	header("Location: " . $_SERVER['PHP_SELF']);
}


function i ($name) { 
	$icon = '<i class="fa fa-' . $name;
	if ($name == 'chevron-right') {
		$icon .= ' right';
	}
	$icon .= ' fa-fw"></i>';
	return $icon;
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
function add_icons($path = './ui/') {
	$path = $path . 'images/icons/';
	
	// Charset
	$page = '<meta charset="UTF-8">';
	
	// Normal icons
	$page .= '<link rel="shortcut icon" type="image/png" sizes="16x16" href="' . $path . '16.png">';
	$page .= '<link rel="shortcut icon" type="image/png" sizes="24x24" href="' . $path . '24.png">';
	$page .= '<link rel="shortcut icon" type="image/png" sizes="32x32" href="' . $path . '32.png">';
	$page .= '<link rel="shortcut icon" type="image/png" sizes="48x48" href="' . $path . '48.png">';
	$page .= '<link rel="shortcut icon" type="image/png" sizes="64x64" href="' . $path . '64.png">';
	$page .= '<link rel="shortcut icon" type="image/png" sizes="128x128" href="' . $path . '128.png">';
	$page .= '<link rel="shortcut icon" type="image/png" sizes="256x256" href="' . $path . '256.png">';
	$page .= '<link rel="shortcut icon" type="image/png" sizes="512x512" href="' . $path . '512.png">';
	
	// Apple icons
	$page .= '<link rel="apple-touch-icon" type="image/png" href="' . $path . 'apple-60.png">';
	$page .= '<link rel="apple-touch-icon" type="image/png" sizes="76x76" href="' . $path . 'apple-76.png">';
	$page .= '<link rel="apple-touch-icon" type="image/png" sizes="120x120" href="' . $path . 'apple-120.png">';
	$page .= '<link rel="apple-touch-icon" type="image/png" sizes="152x152" href="' . $path . 'apple-152.png">';
	
	// Web app cable (runs the website as app)
	$page .= '<meta name="apple-mobile-web-app-capable" content="yes">';
	$page .= '<meta name="mobile-web-app-capable" content="yes">';
	
	// Title
	$page .= '<title>'.t('TITLE').'</title>';
	   
	return $page;
}

/**
 * General header
 */
function general_header() {

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
function general_footer($time=0) {

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
 * Get previous day
 */
function plaatenergy_prev_day($date) {

  list($year, $month, $day) = explode("-", $date);

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
  
  return $prev_year.'-'.$prev_month.'-'.$prev_day; 
}

/**
 * Get next day
 */
function plaatenergy_next_day($date) {

  list($year, $month, $day) = explode("-", $date);

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
  
  return $next_year.'-'.$next_month.'-'.$next_day; 
}
 
/**
* Get previous year 
*/
function plaatenergy_prev_month($date) {

	list($year, $month) = explode("-", $date);

	$prev_year=$year;   
	$prev_month=$month-1;
	if ($prev_month<=0) {
		$prev_month=12;
		$prev_year=$year-1;
	}
  
	return $prev_year.'-'.$prev_month; 
}

/**
 * Get next month
 */
function plaatenergy_next_month($date) {

	list($year, $month) = explode("-", $date);

	$next_year=$year;   
	$next_month=$month+1;
	if ($next_month>12) {
		$next_month=1;
		$next_year=$year+1;
	}
  
	return $next_year.'-'.$next_month; 
}

/**
 * Get previous year 
 */
function plaatenergy_prev_year($year) {

	return $year-1;   
}

/**
 * Get next year 
 */
function plaatenergy_next_year($year) {

	return $year+1;  
}

/**
 * Process post parameters 
 */
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
