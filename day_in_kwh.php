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

day_parameters();

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$i=0;
$first = 1;
$dal_first = 0;
$piek_first = 0;
$dalterug_first = 0;
$piekterug_first = 0;
$dal_value=0;
$piek_value=0;
$solar_value=0;
$dalterug_value=0;
$piekterug_value=0;
$data="";
$total=0;

if ($type==1) {
  while ($i<97) {

     $timestamp = date("Y-m-d H:i:s", $current_date+(900*$i));
     $sql = 'select a.dal, a.piek, a.dalterug, a.piekterug, b.etoday FROM energy a left join solar b on a.timestamp=b.timestamp where a.timestamp="'.$timestamp.'" order by a.timestamp';
     $result = $conn->query($sql);
     $row = $result->fetch_assoc();

     if ($timestamp>date("Y-m-d H:i:s")) {
       $solar_value=0;
       $dal_value=0;
       $piek_value=0;
       $dalterug_value=0;
       $piekterug_value=0;
       break;
     }
     if ( isset($row['dal'])) {
  
        if ($first==1) {
           $dal_first=$row['dal'];
           $piek_first=$row['piek'];
           $dalterug_first=$row['dalterug'];
           $piekterug_first=$row['piekterug'];
           $first=0;
        }
        $dal_value= $row['dal']-$dal_first;
        $piek_value = $row['piek']-$piek_first;
        $dalterug_value= $row['dalterug']-$dalterug_first;
        $piekterug_value = $row['piekterug']-$piekterug_first;
        if ($row['etoday']>0) {
           $solar_value = $row['etoday']-$dalterug_value-$piekterug_value;
        }
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
  $result = $conn->query($sql);

  while ($row = $result->fetch_assoc()) {

     $value=0;
     if ( isset($row['vermogen'])) {
       $value= $row['vermogen'];
     }
  
     if (strlen($data)>0) {
       $data.=',';
     }
     $data .= "['".substr($row['timestamp'],11,5)."',";
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

day_navigation();

general_footer();

?>
