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

day_parameters();

plaatenergy_db_connect($dbhost, $dbuser, $dbpass, $dbname);

$i=0;

$dal_prev = plaatenergy_db_get_config_item('energy_meter_reading_low');
$piek_prev = plaatenergy_db_get_config_item('energy_meter_reading_normal');
$dalterug_prev = 0;
$piekterug_prev = 0;
$solar_prev=0;

$dal_value=0;
$piek_value=0;
$solar_value=0;
$dalterug_value=0;
$piekterug_value=0;

$data="";
$total=0;

if ($type==1) {

  // Get last energy measurement previous day
  $timestamp1 = date("Y-m-d 00:00:00", $prev_date);
  $timestamp2 = date("Y-m-d 23:59:59", $prev_date);
  $sql  = 'select dal, piek, dalterug, piekterug from energy where ';
  $sql .= 'timestamp>="'.$timestamp1.'" and timestamp<="'.$timestamp2.'" order by timestamp desc limit 0,1';

  $result = plaatenergy_db_query($sql);
  $row = plaatenergy_db_fetch_object($result);

  if ( isset($row->dal) ) {
    $dal_prev = $row->dal;
    $piek_prev = $row->piek;
    $dalterug_prev = $row->dalterug;
    $piekterug_prev = $row->piekterug;
  }      

  // Get last solar measurement previous day
  $sql  = 'select etotal from solar where ';
  $sql .= 'timestamp>="'.$timestamp1.'" and timestamp<="'.$timestamp2.'" order by timestamp desc limit 0,1';

  $result = plaatenergy_db_query($sql);
  $row = plaatenergy_db_fetch_object($result);

  if ( isset($row->etotal) ) {
    $solar_prev = $row->etotal;
  }

  while ($i<96) {

     $timestamp1 = date("Y-m-d H:i:s", $current_date+(900*$i));
     $timestamp2 = date("Y-m-d H:i:s", $current_date+(900*($i+1)));
     $sql1  = 'select max(dal) as dal, max(piek) as piek, max(dalterug) as dalterug, max(piekterug) as piekterug from energy where ';
     $sql1 .= 'timestamp>="'.$timestamp1.'" and timestamp<"'.$timestamp2.'"';

     $result1 = plaatenergy_db_query($sql1);
     $row1 = plaatenergy_db_fetch_object($result1);

     $sql2  = 'select max(etotal) as etotal from solar where ';
     $sql2 .= 'timestamp>="'.$timestamp1.'" and timestamp<"'.$timestamp2.'"';

     $result2 = plaatenergy_db_query($sql2);
     $row2 = plaatenergy_db_fetch_object($result2);

     if ( isset($row1->dal)) {
  
        if ($row1->dal>=$dal_prev) {
          $dal_value = $row1->dal - $dal_prev;
        } else {
          $dal_value = $row1->dal;
        }

        if ($row1->piek>=$piek_prev) {
          $piek_value = $row1->piek - $piek_prev;
        } else { 
          $piek_value = $row1->piek;
        }

        if ($row1->dalterug>=$dalterug_prev) {
           $dalterug_value = $row1->dalterug - $dalterug_prev;
        } else {
           $dalterug_value = $row1->dalterug;
        }

        if ($row1->piekterug>=$piekterug_prev) {
           $piekterug_value = $row1->piekterug - $piekterug_prev;
        } else {
           $piekterug_value = $row1->piekterug;
        }
     }

     if ( isset($row2->etotal)) {
        $solar_value = $row2->etotal - $solar_prev - $dalterug_value - $piekterug_value;
        if ($solar_value<0) 
        {
          $solar_value=0;
        }
     }

     // Data in the future is always 0!	
     if ($timestamp1>date("Y-m-d H:i:s")) {

        $dal_value = 0;
        $piek_value = 0;
        $dalterug_value = 0;
        $piekterug_value = 0;
        $solar_value = 0;
     }

     if (strlen($data)>0) {
       $data.=',';
     }
     $data .= "['".date("H:i", $current_date+(900*$i))."',";
     $data .= round($dal_value,2).','.round($piek_value,2).','.round($solar_value,2).']';
     $total = round(($dal_value+$piek_value+$solar_value),2);

     $i++;
  }
  
  $json = "[['','".t('USED_LOW_KWH')."','".t('USED_HIGH_KWH')."','".t('USED_SOLAR_KWH')."'],".$data."]";
}

if ($type==2) {

  $data="";
  $timestamp1 = date("Y-m-d 00:00:00", $current_date);
  $timestamp2 = date("Y-m-d 23:59:59", $current_date);

   $sql = 'select timestamp, vermogen FROM energy where timestamp>="'.$timestamp1.'" and timestamp<="'.$timestamp2.'" order by timestamp';
  
   $result = plaatenergy_db_query($sql);
   while ( $row = plaatenergy_db_fetch_object($result)) {

     $value=0;
     if ( isset($row->vermogen)) {
       $value= $row->vermogen;
     }
  
     if (strlen($data)>0) {
       $data.=',';
     }
     $data .= "['".substr($row->timestamp,11,5)."',";
       $data .= round($value,2).']';
  }
  $json = "[".$data."]";
}

general_header();

if ($type==1) {

?>
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["bar"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {

       var options = {
          bars: 'vertical',
          bar: {groupWidth: "90%"},
          legend: { position: 'none' },
          vAxis: {format: 'decimal'},
          isStacked:true
        };

        var data = google.visualization.arrayToDataTable(<?php echo $json?>);
        var chart = new google.charts.Bar(document.getElementById('chart_div'));
        chart.draw(data, google.charts.Bar.convertOptions(options));
    }
    </script>

<?php
} else { 
?>
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
    google.load("visualization", "1.1", {packages:["line"]});
    google.setOnLoadCallback(drawChart);

    function drawChart() {

         var data = new google.visualization.DataTable();
         data.addColumn('string', 'Time');
         data.addColumn('number', 'Watt');
         data.addRows(<?php echo $json?>);

         var options = {
           legend: { position: 'none' },
           pointSize: 2,
           pointShape: 'circle',
           vAxis: {format: 'decimal', title: ''},
           hAxis: {title: ''},
         };

         var chart = new google.charts.Line(document.getElementById('chart_div'));
         chart.draw(data, google.charts.Line.convertOptions(options));
    }
    </script>

<?php

}

echo '<h1>'.t('TITLE_DAY_IN_KWH', $day, $month, $year).'</h1>';
echo '<div id="chart_div" style="width: '.$graph_width.'; height: '.$graph_height.';"></div>';

text_banner( t('TOTAL_PER_DAY_KWH', $total));

// If zero or one measurements are found. Measurement can be manully adapted.
$timestamp1 = date("Y-m-d 00:00:00", $current_date);
$timestamp2 = date("Y-m-d 23:59:59", $current_date);
$sql = 'select * FROM energy where timestamp>="'.$timestamp1.'" and timestamp<="'.$timestamp2.'"';
$result = plaatenergy_db_query($sql);
$records = plaatenergy_db_num_rows($result);

if ($records<=1) {
   $edit=1;
} else {
   $edit=0;
}

day_navigation($edit);

general_footer();

?>
