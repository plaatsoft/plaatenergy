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
**  All copyrights reserved (c) 2008-2015 PlaatSoft
*/

include "config.inc";
include "general.inc";

$start="";
if (isset($_POST["start"])) {
  $start = $_POST["start"];
}

$end="";
if (isset($_POST["end"])) {
  $end = $_POST["end"];
}

$report=0;
if (isset($_POST["report"])) {
  $report = $_POST["report"];
}

general_header();

echo '<h1>'.t('TITLE_QUERY_REPORT'),'</h1>';

echo '<form method="post">';
echo '<label>'.t('LABEL_START_DATE').': </label>';
echo '<br/>';
echo '<input name="start" type="date" size="10" maxlength="10" value="'.$start.'"/>';
echo '<br/>';
echo '<br/>';
echo '<label>'.t('LABEL_END_DATE').': </label>';
echo '<br/>';
echo '<input name="end" type="date" size="10" maxlength="10" value="'.$end.'"/>';
echo '<br/>';
echo '<br/>';
echo '<input type="hidden" name="report" value="1" />';
echo '<input type="submit" value="'.t('LINK_EXECUTE').'" />';
echo '</form>';

if ($report==1) {

  $conn = new mysqli($servername, $username, $password, $dbname);

  $sql = 'select sum(dal) as dal, sum(piek) as piek, sum(dalterug) as dalterug, sum(piekterug) as piekterug, sum(solar) as solar, sum(gas) as gas FROM energy_day where date>="'.$start.'" and date<="'.$end.'"';

  $result = $conn->query($sql);
  $row = $result->fetch_assoc();

  echo '<br/>';
  echo 'dal='.round($row['dal'],2).' ';
  echo 'piek='.round($row['piek'],2).' ';
  echo 'dalterug='.round($row['dalterug'],2).' ';
  echo 'piekterug='.round($row['piekterug'],2).' ';
  echo 'solar='.round($row['solar'],2).' ';
  echo 'gas='.round($row['gas'],2).' ';
}

general_navigation();
general_copyright();
general_footer();

?>
