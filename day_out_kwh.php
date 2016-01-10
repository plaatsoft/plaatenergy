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
$label="";
$data="";
$value=0;
$total_energy=0;

if ($type==1) {
  $current_date=mktime(0, 0, 0, $month, $day, $year);
  while ($i<96) {

     $timestamp = date("Y-m-d H:i:s", $current_date+(900*$i));
     $sql = 'select etoday FROM solar where timestamp="'.$timestamp.'"';
	  
     $result = plaatenergy_db_query($sql);
     $row = plaatenergy_db_fetch_object($result);
  
     if ($timestamp>date("Y-m-d H:i:s")) {
       $value=0;
     } else {
        $total_energy = round($value,2);  
     }

     if ( isset($row->etoday)) {
          $value= $row->etoday;
     }
  
     if (strlen($data)>0) {
       $data.=',';
     }
     $data .= "['".date("H:i", $current_date+(900*$i))."',";
     $data .= round($value,2).']';
  
     $i++;
  }
  $json = "[['','Levering'],".$data."]";
}

if ($type==2) { 

  $timestamp1 = date("Y-m-d 00:00:00", $current_date);
  $timestamp2 = date("Y-m-d 23:59:59", $current_date);

  $sql = 'select timestamp, etoday, pac FROM solar where timestamp>="'.$timestamp1.'" and timestamp<="'.$timestamp2.'" order by timestamp';
  $result = plaatenergy_db_query($sql);

  while ($row = plaatenergy_db_fetch_object($result)) {

   $value=0;
   if ( isset($row->pac)) {
     $value= $row->pac;
     $total_energy = $row->etoday;
   }

   if (strlen($data)>0) {
     $data.=',';
   }
   $data .= "['".substr($row->timestamp,11,5)."',";
   $data .= round($value,2).']';
   $i++;
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
      google.load("visualization", "1", {packages:["line"]});
      google.setOnLoadCallback(drawChart);

      function drawChart() {

         var data = new google.visualization.DataTable();
         data.addColumn('string', 'Time');
         data.addColumn('number', 'Watt');

         data.addRows(<?php echo $json?>);

         var options = {
           legend: { position: 'none' },
           vAxis: {format: 'decimal', title:''},
           hAxis: {title:''},
         };

         var chart = new google.charts.Line(document.getElementById('chart_div'));
         chart.draw(data, google.charts.Line.convertOptions(options));
    }
    </script>
    
<?php

}

echo '<h1>'.t('TITLE_DAY_OUT_KWH', $day, $month, $year).'</h1>';
echo '<div id="chart_div" style="width: '.$graph_width.'; height: '.$graph_height.';"></div>';

text_banner( t('TOTAL_PER_DAY_KWH', $total_energy) );

// If zero or one measurements are found. Measurement can be manully adapted.
$timestamp1 = date("Y-m-d 00:00:00", $current_date);
$timestamp2 = date("Y-m-d 23:59:59", $current_date);
$sql = 'select * FROM solar where timestamp>="'.$timestamp1.'" and timestamp<="'.$timestamp2.'"';
$result = plaatenergy_db_query($sql);
$records = plaatenergy_db_num_rows($result);

if ($records<=1) {
   $edit=2;
} else {
   $edit=0;
}

day_navigation($edit);

general_footer();

?>
