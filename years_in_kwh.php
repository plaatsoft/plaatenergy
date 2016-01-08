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

year_parameters();

plaatenergy_db_connect($dbhost, $dbuser, $dbpass, $dbname);

$energy_price = plaatenergy_db_get_config_item('energy_price');
$energy_use_forecast = plaatenergy_db_get_config_item('energy_use_forecast');

$total=0;
$total_price=0;
$count=0;
$data="";
$max_forecast=0;

for($y=$year-10; $y<=$year; $y++) {

   $time=mktime(0, 0, 0, 1, 1, $y);
   $timestamp1=date('Y-1-1', $time);
   $timestamp2=date('Y-12-t', $time);

   $sql1  = 'select sum(dal) as dal, sum(piek) as piek, sum(dalterug) as dalterug, ';
   $sql1 .= 'sum(piekterug) as piekterug, sum(solar) as solar ';
   $sql1 .= 'from energy_day where date>="'.$timestamp1.'" and date<="'.$timestamp2.'"';
	
   $result1 = plaatenergy_db_query($sql1);
   $row1 = plaatenergy_db_fetch_object($result1);
	 
   $sql2 =  'select month(date) as month from energy_day ';
   $sql2 .= 'where date>="'.$timestamp1.'" and date<="'.$timestamp2.'" ';
   $sql2 .= 'group by month ';
	
   $result2 = plaatenergy_db_query($sql2);

   $forecast_total=0;
   while ($row2 = plaatenergy_db_fetch_object($result2)) {
      if (isset($row2->month)) {
         $forecast_total += $in_forecast[$row2->month];
      }
   }

   if (($forecast_total*$energy_use_forecast)>$max_forecast) {
      $max_forecast=$forecast_total*$energy_use_forecast;
   }

   $dal_value=0;
   $piek_value=0;
   $dalterug_value=0;
   $piekterug_value=0;
   $solar_value=0;
   $verbruikt=0;

    if (isset($row1->dal)) {
      $dal_value= $row1->dal;
      $piek_value= $row1->piek;
      $dalterug_value= $row1->dalterug;
      $piekterug_value= $row1->piekterug;
      $solar= $row1->solar;

      $verbruikt = $solar-$dalterug_value-$piekterug_value;
      $count++;
   }

   if (strlen($data)>0) {
     $data.=',';
   }
   $data .= "['".date("Y", $time)."',";
   $price2 = ($dal_value + $piek_value + $verbruikt)*$energy_price;
   if ($type==1) {
      $data .= round($dal_value,2).','.round($piek_value,2).','.round($verbruikt,2).','.round(($forecast_total*$energy_use_forecast),2).']';
   } else { 
      $data .= round($price2,2).']';
   }
   $total += $dal_value + $piek_value + $verbruikt;
   $total_price += $price2;
}

if ($type==1) {
   $json = "[['','".t('USED_LOW_KWH')."','".t('USED_HIGH_KWH')."','".t('USED_SOLAR_KWH')."','".t('FORECAST_KWH')."'],".$data."]";
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
          isStacked:true,
          <?php
          if ($type==1) {
             echo "colors: ['#0066cc', '#808080'],";
             echo "vAxis: { format:'decimal', viewWindow: { min: 0, max: ".round($max_forecast+100)." } }, ";

          } else {
             echo "colors: ['#e0440e'],";
          }
          ?>
          series: {
            3: {
                targetAxisIndex: 1
            }
          }
        };

        var data = google.visualization.arrayToDataTable(<?php echo $json?>);
        var chart = new google.charts.Bar(document.getElementById('chart_div'));
        chart.draw(data, google.charts.Bar.convertOptions(options));

        google.visualization.events.addListener(chart, "select", selectHandler);

        function selectHandler(e)     {
           var year = data.getValue(chart.getSelection()[0].row, 0);
           window.location="year_in_kwh.php?year="+year;
        }
      }
    </script>

<?php
   
echo '<h1>'.t('TITLE_YEARS_IN_KWH',($year-10),$year).'</h1>';
echo '<div id="chart_div" style="width: '.$graph_width.'; height: '.$graph_height.';"></div>';

if ($count>0) {
   if ($type==1) {
       text_banner(t('AVERAGE_PER_YEAR_KWH', round(($total/$count),2), round($total,2)));
   } else {
       text_banner(t('AVERAGE_PER_YEAR_EURO', round(($total_price/$count),2), round($total_price,2)));
   }
} else {
   text_banner('&nbsp;');
}

year_navigation(t('LINK_KWH'));

general_footer();

?>



