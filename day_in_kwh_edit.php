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
 
include "config.inc";
include "general.inc";
include "database.inc";

$do=0;
if (isset($_POST["do"])) {
  $do = $_POST["do"];
}

day_parameters();

plaatenergy_db_connect($dbhost, $dbuser, $dbpass, $dbname);

// -------------------------------------

$timestamp = date("Y-m-d 00:00:00", $current_date);
$sql1  = 'select dal as low, piek as normal FROM energy where ';
$sql1 .= 'timestamp<"'.$timestamp.'" order by timestamp desc limit 0,1';

$result1 = plaatenergy_db_query($sql1);
$row1 = plaatenergy_db_fetch_object($result1);
	
$prev_low=0;
$prev_normal=0;
if ( isset($row1->low)) {
  $prev_low = $row1->low;
  $prev_normal = $row1->normal;
}

// -------------------------------------

$timestamp = date("Y-m-d 00:00:00", $current_date);

$sql2  = 'select dal as low, piek as normal from energy where ';
$sql2 .= 'timestamp>"'.$timestamp.'" order by timestamp asc limit 0,1';

$result2 = plaatenergy_db_query($sql2);
$row2 = plaatenergy_db_fetch_object($result2);

$next_low=999999;
$next_normal=999999;
if ( isset($row2->low)) {
  $next_low = $row2->low;
  $next_normal = $row2->normal;
}

// -------------------------------------

$low=$prev_low+round((($next_low-$prev_low)/2),1);
if (isset($_POST["low"])) {
  $low = $_POST["low"];
}
$normal=$prev_normal+round((($next_normal-$prev_normal)/2),1);
if (isset($_POST["normal"])) {
  $normal = $_POST["normal"];
}

// -------------------------------------

$timestamp = date("Y-m-d 00:00:00", $current_date);

$sql3  = 'select dal as low, piek as normal from energy where ';
$sql3 .= 'timestamp="'.$timestamp.'" order by timestamp asc limit 0,1';

$result3 = plaatenergy_db_query($sql3);
$row3 = plaatenergy_db_fetch_object($result3);

$found=0;
if (isset($row3->low)) {
  $found=1;
  if ($do==0) {
    $low = $row3->low;
    $normal = $row3->normal;
  }
}

// -------------------------------------

general_header();

echo' <h1>'.t('TITLE_IN_KWH_EDIT').' '.$day.'-'.$month.'-'.$year.'</h1>';

echo '<form method="post">';

// -------------------------------------

echo '<br/>';
echo '<label>'.t('LABEL_LOW').':</label>';
echo '<br/>';
echo '<br/>';
echo $prev_low.' - ';
echo '<input type="text" name="low" value="'.$low.'" size="6" />';
echo ' - '.$next_low;
echo '<br/>';

// -------------------------------------

echo '<br/>';
echo '<label>'.t('LABEL_NORMAL').':</label>';
echo '<br/>';
echo '<br/>';
echo $prev_normal.' - ';
echo '<input type="text" name="normal" value="'.$normal.'" size="6" />';
echo ' - '.$next_normal;
echo '<br/>';
echo '<br/>';
echo '<input type="hidden" name="do" value="1" />';

// -------------------------------------

if ($found==0) {
   echo '<input type="submit" value="'.t('LINK_INSERT').'" />';
} else { 
   echo '<input type="submit" value="'.t('LINK_UPDATE').'" />';
} 
echo '</form>';

if ($do==1) {

  $timestamp = date("Y-m-d 00:00:00", $current_date);

  if ($found==0) {
  
     $sql4  = 'insert into energy ( timestamp, dal, piek) values ("'.$timestamp.'",'.$low.','.$normal.')';
     echo t('RECORD_INSERTED');

  } else { 
  
     $sql4 = 'update energy set dal='.$low.', piek='.$normal.' where timestamp="'.$timestamp.'"';
     echo t('RECORD_UPDATED');
  }
  
  plaatenergy_db_query($sql4);
  plaatenergy_process(2);
}

// -------------------------------------

general_navigation();
general_footer();

?>
