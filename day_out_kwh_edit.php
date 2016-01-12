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

$etotal=0;
if (isset($_POST["etotal"])) {
  $etotal = $_POST["etotal"];
}

day_parameters();

plaatenergy_db_connect($dbhost, $dbuser, $dbpass, $dbname);

$timestamp1 = date("Y-m-d 00:00:00", $prev_date);
$timestamp2 = date("Y-m-d 23:59:59", $prev_date);
$sql1  = 'select max(etotal) as etotal FROM solar where ';
$sql1 .= 'timestamp>="'.$timestamp1.'" and timestamp<="'.$timestamp2.'"';

$result1 = plaatenergy_db_query($sql1);
$row1 = plaatenergy_db_fetch_object($result1);
	
$prev_etotal=0;
if ( isset($row1->etotal)) {
  $prev_etotal = $row1->etotal;
}

$timestamp1 = date("Y-m-d 00:00:00", $next_date);
$timestamp2 = date("Y-m-d 23:59:59", $next_date+(86400*2));

$sql2  = 'select min(etotal) as etotal FROM solar where ';
$sql2 .= 'timestamp>="'.$timestamp1.'" and timestamp<="'.$timestamp2.'"';

$result2 = plaatenergy_db_query($sql2);
$row2 = plaatenergy_db_fetch_object($result2);

$next_etotal=999999;
if ( isset($row2->etotal)) {
  $next_etotal = $row2->etotal;
}

$etotal=0;
if (isset($_POST["etotal"])) {
  $etotal = $_POST["etotal"];
} else {
  $etotal = round((($next_etotal+$prev_etotal)/2),1);
  if ($etotal<$prev_etotal) {
    $etotal=$prev_etotal;
  } 
}

general_header();

echo' <h1>'.t('TITLE_OUT_KWH_EDIT').' '.$day.'-'.$month.'-'.$year.'</h1>';

echo '<form method="post">';
echo '<br/>';
echo '<label>'.t('LABEL_ETOTAL').':</label>';
echo '<br/>';
echo '<br/>';
echo $prev_etotal.' - ';
echo '<input type="text" name="etotal" id="etotal" value="'.$etotal.'" size="7" />';
echo ' - '.$next_etotal;
echo '<br/>';
echo '<br/>';
echo '<input type="hidden" name="do" value="1" />';

if ($do==0) {
   echo '<input type="submit" value="'.t('LINK_INSERT').'" />';
} 
echo '</form>';

if ($do==1) {

  $sql3  = 'insert into solar (`id`, `timestamp`, `etoday`, `etotal`) ';
  $sql3 .= 'values (null, "'.$year.'-'.$month.'-'.$day.' 00:00:00","'.($etotal-$prev_etotal).'","'.$etotal.'")';

  plaatenergy_db_query($sql3);
  plaatenergy_process(2);

  echo t('RECORD_INSERTED'); 
}

general_navigation();
general_footer();

?>
