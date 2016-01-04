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

year_parameters();

$conn = new mysqli($servername, $username, $password, $dbname);

$sql = 'select gas_prijs,start_gas FROM config';
$result = $conn->query($sql);
$config = $result->fetch_assoc();
$price = $config['gas_prijs'];

$total=0;
$total_price=0;
$count=0;
$data="";

for($y=($year-10); $y<=$year; $y++) {
    $value=0;

    $time=mktime(0, 0, 0, 1, 1, $y);          
    $timestamp1=date('Y-1-1 00:00:00', $time);
    $timestamp2=date('Y-12-t 23:59:59', $time);

    $sql1  = 'select sum(gas) as gas FROM energy_day ';
	 $sql1 .= 'where date>="'.$timestamp1.'" and date<="'.$timestamp2.'"';
    $result1 = $conn->query($sql1);
    $row1 = $result1->fetch_assoc();

    if ( isset($row1['gas'])) {

      $count++;
      $value=$row1['gas'];
   }

   $sql2  = 'select month(date) as month from energy_day ';
   $sql2 .= 'where date>="'.$timestamp1.'" and date<="'.$timestamp2.'" ';
   $sql2 .= "group by month ";
   $result2 = $conn->query($sql2);

   $progress_total=0;
   while ($row2 = $result2->fetch_assoc()) {
      if (isset($row2['month'])) { 
         $progress_total += $gas_prognoss[$row2['month']];
      }
   } 
   
   if (strlen($data)>0) {
     $data.=',';
   }
   $price2 = $value * $price;
   $data .= "['".date("Y", $time)."',";

   if ($type==1) {
      if ($value>0) { 
         $data .= round($value,2).','.round(($progress_total*$gas_total),2).']';
      } else { 
         $data .= '0,0]';
      }
   } else { 
      $data .= round($price2,2).']';
   }
   $total += $value;
   $total_price += $price2;
}

if ($type==1) {
   $json = "[['','".t('USED_M3')."','".t('FORECAST_M3')."'],".$data."]";
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
          if ($type==1) {
             echo "colors: ['#0066cc', '#808080']";
          } else {
             echo "colors: ['#e0440e']";
          }
          ?>
        };

        var data = google.visualization.arrayToDataTable(<?php echo $json?>);
        var chart = new google.charts.Bar(document.getElementById('chart_div'));
        chart.draw(data, google.charts.Bar.convertOptions(options));

        google.visualization.events.addListener(chart, "select", selectHandler);

        function selectHandler(e)     {
           var year = data.getValue(chart.getSelection()[0].row, 0);
           window.location="year_in_gas.php?year="+year;
        }
     }

    </script>
    

<?php

echo '<h1>'.t('TITLE_YEARS_IN_M3', ($year-10), $year).'</h1>';
echo '<div id="chart_div" style="width: '.$graph_width.'; height: '.$graph_height.';"></div>';

if ($count>0) {
  if ($type==1) {
     text_banner(t('AVERAGE_PER_YEAR_M3', round(($total/$count),2), round($total,2)));
  } else {
     text_banner(t('AVERAGE_PER_YEAR_EURO', round(($total_price/$count),2), round($total_price,2)));
  }
} else {
   text_banner('&nbsp;');
}

year_navigation(t('LINK_M3'));

general_footer();

?>
