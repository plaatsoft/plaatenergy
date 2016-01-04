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

month_parameters();

$conn = new mysqli($servername, $username, $password, $dbname);

$sql = 'select elektra_prijs FROM config';
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$price = $row['elektra_prijs'];

$data="";
$value = 0;
$total = 0;
$total_price = 0;
$count = 0;

for($d=1; $d<=31; $d++)
{
    $time=mktime(12, 0, 0, $month, $d, $year);          
    if (date('m', $time)==$month) {
        $timestamp1=date('Y-m-d 00:00:00', $time);
        $timestamp2=date('Y-m-d 23:59:59', $time);

        $sql = 'select solar FROM energy_day where date>="'.$timestamp1.'" and date<="'.$timestamp2.'"';

        $result = $conn->query($sql);
        $row = $result->fetch_assoc();

        if (isset($row['solar'])) {
           $value = $row['solar'];
        
           $total += $value;
           $count++;
        } else {
           $value=0;
        }
 
        if (strlen($data)>0) {
          $data.=',';
        }
    
        if ($type==1) {
           $data .= "['".date("d-m", $time)."',".round($value,2)."]";
        } else { 
           $data .= "['".date("d-m", $time)."',".round($value*$price,2)."]";
        }
    }
}
$total_price=$total*$price;

if ($type==1) {
   $json = "[['','".t('DELIVERED_KWH')."'],".$data."]";
} else { 
   $json = "[['','".t('EURO')."'],".$data."]";
}

general_header();

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
          <?php
          if ($type==2) {
             echo "colors: ['#e0440e']";
          }
          ?>

        };

        var data = google.visualization.arrayToDataTable(<?php echo $json?>);
        var chart = new google.charts.Bar(document.getElementById('chart_div'));
        chart.draw(data, google.charts.Bar.convertOptions(options));

        google.visualization.events.addListener(chart, "select", selectHandler);

        function selectHandler(e)     {
           var date = data.getValue(chart.getSelection()[0].row, 0);
           var day = date.split("-");
           window.location="day_out_kwh.php?day="+day[0]+"&month=<?php echo $month.'&year='.$year;?>"
        }


      }
    </script>

<?php

echo '<h1>'.t('TITLE_MONTH_OUT_KWH', $month, $year).'</h1>';
echo '<div id="chart_div" style="width: '.$graph_width.'; height: '.$graph_height.';"></div>';

if ($count>0) {
   if ($type==1) {
       text_banner(t('AVERAGE_PER_DAY_KWH', round(($total/$count),2), round($total,2)));
   } else {
       text_banner(t('AVERAGE_PER_DAY_EURO', round(($total_price/$count),2), round($total_price,2)));
   }
} else {
  text_banner('&nbsp;');
}

month_navigation( t('LINK_KWH') );

general_footer();

?>
